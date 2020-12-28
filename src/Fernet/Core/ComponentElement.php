<?php

declare(strict_types=1);

namespace Fernet\Core;

use Fernet\Framework;

class ComponentElement
{
    private $component;

    private static int $counter = 0;

    public function __construct($classOrObject, array $params = [], string $childContent = '')
    {
        $component = \is_string($classOrObject) ?
            $this->getObject($classOrObject) :
            $classOrObject;
        foreach ($params as $key => $value) {
            $component->$key = $value;
        }
        $component->childContent = $childContent;
        $this->component = $component;
    }

    public function getComponent()
    {
        return $this->component;
    }

    private function getObject(string $class)
    {
        if (class_exists($class)) {
            return Framework::get($class);
        }
        $namespaces = Framework::getOption('componentNamespaces');
        foreach ($namespaces as $namespace) {
            $classWithNamespace = $namespace.'\\'.$class;
            if (class_exists($classWithNamespace)) {
                return Framework::get($classWithNamespace);
            }
        }
        throw new NotFoundException(sprintf('Component "%s" not defined in ["%s"]', $class, implode('", "', $namespaces)));
    }

    public function call($method, $args)
    {
        if (!method_exists($this->component, $method)) {
            throw new NotFoundException(sprintf('Method "%s" not found in component "%s"', $method, \get_class($this->component)));
        }

        return \call_user_func_array([$this->component, $method], $args);
    }

    public function render(): string
    {
        $content = (string) $this->component;
        $content = (new ReplaceComponents())->replace($content);
        $content = (new ReplaceAttributes())->replace($content, $this->component);
        if (!Framework::getOption('enableJs')
            || (isset($this->component->preventWrapper)) && $this->component->preventWrapper) {
            return $content;
        }
        $id = static::$counter++;

        return "<div id=\"_fernet_component_$id\" class=\"_fernet_component\">$content</div>";
    }
}
