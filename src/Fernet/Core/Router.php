<?php

namespace Fernet\Core;

use Fernet\Framework;
use Symfony\Component\HttpFoundation\Request;

class Router
{
    private $uri;
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function route()
    {
        $prefix = Framework::getOption('urlPrefix');
        $regexp = "@^$prefix([^/]+)/(.+)/?$@";
        if (preg_match($regexp, $this->request->getPathInfo(), $matches)) {
            $class = Helper::pascalCase($matches[1]);
            $method = Helper::camelCase($matches[2]);
            $component = new ComponentElement($class);
            $this->bind($component->getComponent());

            return $component->call($method, $this->getArgs());
        }
    }

    private function getArgs(): array
    {
        $args = [$this->request];
        $params = $this->request->query->get('fernet-params', []);
        foreach ($params as $param) {
            $args[] = unserialize($param);
        }

        return $args;
    }

    private function bind(object $component): void
    {
        foreach ($this->request->request->get('fernet-bind', []) as $key => $value) {
            $var = &$component;
            foreach (explode('.', $key) as $attr) {
                $var = &$var->$attr;
            }
            $var = $value;
        }
    }
}
