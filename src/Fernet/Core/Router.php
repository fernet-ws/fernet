<?php

namespace Fernet\Core;

use Fernet\Framework;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Router
{
    private Request $request;
    private Logger $log;

    public function __construct(Request $request, Logger $log)
    {
        $this->request = $request;
        $this->log = $log;
    }


    /**
     * @param $defaultComponent
     * @return mixed
     * @throws NotFoundException
     */
    public function route($defaultComponent): Response
    {
        $response = false;
        $prefix = Framework::config('urlPrefix');
        $regexp = "@^$prefix([^/]+)/(.+)/?$@";
        if (preg_match($regexp, $this->request->getPathInfo(), $matches)) {
            $class = Helper::pascalCase($matches[1]);
            $method = Helper::camelCase($matches[2]);
            $this->log->debug("Route matched", compact('class', 'method'));
            $component = new ComponentElement($class);
            $this->bind($component->getComponent());
            $response = $component->call($method, $this->getArgs());
        }
        if (!$response) {
            $response = new Response(
                (new ComponentElement($defaultComponent))->render(),
                Response::HTTP_OK
            );
        }
        return $response;
    }

    private function getArgs(): array
    {
        $args = [];
        // TODO: Change hardcoded string to constant
        $params = $this->request->query->get('fernet-params', []);
        foreach ($params as $param) {
            // TODO: This is completely unsafe, refactor asap
            $value = @unserialize($param);
            if ($value === false && $param !== serialize(false)) {
                $this->log->error('Error when trying to unserialize param', [$param]);
                $args[] = null;
            } else {
                $args[] = $value;
            }
        }
        $this->log->debug('Arguments passed to component event', [$args]);
        $args[] = $this->request;

        return $args;
    }

    private function bind(object $component): void
    {
        // TODO: Change hardcoded string to constant
        foreach ($this->request->request->get('fernet-bind', []) as $key => $value) {
            $this->log->debug("Binding \"$key\" to", [$value]);
            $var = &$component;
            foreach (explode('.', $key) as $attr) {
                $var = &$var->$attr;
            }
            $var = $value;
        }
    }
}
