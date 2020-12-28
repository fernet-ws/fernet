<?php

declare(strict_types=1);

namespace Fernet;

use Exception;
use Fernet\Component\FernetShowError;
use Fernet\Core\ComponentElement;
use Fernet\Core\Helper;
use Fernet\Core\NotFoundException;
use Fernet\Core\Router;
use League\Container\Container;
use League\Container\ReflectionContainer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class Framework
{
    const DEFAULT_OPTIONS = [
        'devMode' => false,
        'enableJs' => true,
        'urlPrefix' => '/',
        'componentNamespaces' => [
            'App\\Component',
            'Fernet\\Component',
        ],
        'error404' => 'Fernet\\Component\\Error404',
        'error500' => 'Fernet\\Component\\Error500',
    ];
    const DEFAULT_ENV_PREFIX = 'FERNET_';

    private static self $instance;

    private Container $container;
    private array $options;

    private function __construct(array $options)
    {
        $this->container = new Container();
        $this->container->delegate((new ReflectionContainer())->cacheResolutions());
        $this->options = $options;
    }

    public static function setUp($envPrefix = self::DEFAULT_ENV_PREFIX): void
    {
        $options = static::DEFAULT_OPTIONS;
        foreach ($_ENV as $key => $value) {
            if (0 === strpos($key, $envPrefix)) {
                $key = substr($key, \strlen($envPrefix));
                $key = Helper::camelCase($key);
                $options[$key] = \is_bool($options[$key]) ?
                    filter_var($value, FILTER_VALIDATE_BOOLEAN) :
                    $value;
            }
        }
        static::$instance = new self($options);
    }

    public static function __callStatic(string $name, array $arguments)
    {
        return \call_user_func_array([static::$instance, "_$name"], $arguments);
    }

    public function _get(string $class): object
    {
        return $this->container->get($class);
    }

    public function _getContainer(): Container
    {
        return $this->container;
    }

    public function _getOption(string $option)
    {
        if (isset($this->options[$option])) {
            return $this->options[$option];
        }
    }

    public function _setOption(string $option, $value): self
    {
        $this->options[$option] = $value;

        return $this;
    }

    public function _addOption(string $option, $value): self
    {
        $this->options[$option][] = $value;

        return $this;
    }

    public function _run($component): Response
    {
        try {
            $request = Request::createFromGlobals();
            $this->container->add(Request::class, $request);
            $router = $this->_get(Router::class);
            $response = $router->route();
            if (!$response) {
                $response = new Response(
                    (new ComponentElement($component))->render(),
                    Response::HTTP_OK
                );
            }
        } catch (NotFoundException $e) {
            return new Response(
                $this->_showError($e, 'error404'),
                Response::HTTP_NOT_FOUND
            );
        } catch (Exception $e) {
            $response = new Response(
                $this->showError($e, 'error500'),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
        $response->prepare($request);

        return $response;
    }

    private function _showError(Exception $e, $type = 'error500'): string
    {
        $element = $this->_getOption('devMode') ?
            new ComponentElement(FernetShowError::class, ['exception' => $e]) :
            new ComponentElement($this->_getOption($type));

        return $element->render();
    }
}
