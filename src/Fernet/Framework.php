<?php

declare(strict_types=1);

namespace Fernet;

use Fernet\Component\FernetShowError;
use Fernet\Core\ComponentElement;
use Fernet\Core\Exception;
use Fernet\Core\Helper;
use Fernet\Core\NotFoundException;
use Fernet\Core\Router;
use League\Container\Container;
use League\Container\ReflectionContainer;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Fernet\Component\Error404;
use Fernet\Component\Error500;

final class Framework
{
    private const DEFAULT_OPTIONS = [
        'devMode' => false,
        'enableJs' => true,
        'urlPrefix' => '/',
        'componentNamespaces' => [
            'App\\Component',
            'Fernet\\Component',
        ],
        'logPath' => 'php://stdout',
        'logName' => 'fernet',
        'logLevel' => Logger::INFO,
        'error404' => Error404::class,
        'error500' => Error500::class,
        'rootPath' => '.',
    ];

    private static self $instance;

    /**
     * Prefix used in env file
     */
    private const DEFAULT_ENV_PREFIX = 'FERNET_';

    private Container $container;
    private Logger $log;
    private array $options;
    private array $events = [
        'onLoad' => [],
        'onRequest' => [],
        'onResponse' => [],
        'onError' => [],
    ];

    private function __construct(array $options)
    {
        $this->container = new Container();
        $this->container->delegate((new ReflectionContainer())->cacheResolutions());
        $this->options = $options;

        $logger = new Logger($options['logName']);
        $logger->pushHandler(new StreamHandler($options['logPath']), $options['logLevel']);
        $this->container->add(Logger::class, $logger);
        $this->log = $logger;
    }

    public static function setUp(array $options = [], $envPrefix = self::DEFAULT_ENV_PREFIX): self
    {
        $options = array_merge(self::DEFAULT_OPTIONS, $options);
        foreach ($_ENV as $key => $value) {
            if (0 === strpos($key, $envPrefix)) {
                $key = substr($key, \strlen($envPrefix));
                $key = Helper::camelCase($key);
                $options[$key] = \is_bool($options[$key]) ?
                    filter_var($value, FILTER_VALIDATE_BOOLEAN) :
                    $value;
            }
        }
        self::$instance = new self($options);
        return self::$instance;
    }

    public static function getInstance(): self
    {
        if (!self::$instance) {
            self::setUp();
        }
        return self::$instance;
    }

    public static function config(string $class)
    {
        return self::getInstance()->getOption($class);
    }

    public static function subscribe(string $event, callable $callback): self
    {
        return self::getInstance()->observe($event, $callback);
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    public function getOption(string $option)
    {
        if (!isset($this->options[$option])) {
            throw new Exception("Undefined config \"$option\"");
        }
        return $this->options[$option];
    }

    public function setOption(string $option, $value): self
    {
        $this->options[$option] = $value;

        return $this;
    }

    public function addOption(string $option, $value): self
    {
        $this->options[$option][] = $value;

        return $this;
    }

    public function observe(string $event, callable $callback): self
    {
        $this->events[$event][] = $callback;
        return $this;
    }

    public function dispatch(string $event, array $args = []): void
    {
        foreach ($this->events[$event] as $position => $callback) {
            $this->log->debug("Dispatch \"$event\" callback #$position");
            \call_user_func_array($callback, $args);
        }
    }

    public function run($component): Response
    {
        $request = null;
        try {
            $this->dispatch('onLoad', [$this]);
            $request = Request::createFromGlobals();
            $this->dispatch('onRequest', [$request]);
            $this->container->add(Request::class, $request);
            /** @var Router $router */
            $router = $this->container->get(Router::class);
            $response = $router->route($component);
            $this->dispatch('onResponse', [$response]);
        } catch (NotFoundException $notFoundException) {
            $this->log->notice("Route not found");
            return new Response(
                $this->showError($notFoundException, 'error404'),
                Response::HTTP_NOT_FOUND
            );
        } catch (Exception $exception) {
            $this->log->error($exception->getMessage());
            $response = new Response(
                $this->showError($exception, 'error500'),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        } catch (Throwable $error) {
            $this->log->error("An error or an exception was occurred", [$error]);
            $response = new Response(
                $this->showError($error, 'error500'),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
        $response->prepare($request);

        return $response;
    }

    public function showError(Throwable $error, string $type): string
    {
        $this->dispatch('onError', [$error]);
        $element = $this->getOption('devMode') ?
            new ComponentElement(FernetShowError::class, ['error' => $error]) :
            new ComponentElement($this->getOption($type));

        return $element->render();
    }

    public function getLog(): Logger
    {
        return $this->log;
    }
}
