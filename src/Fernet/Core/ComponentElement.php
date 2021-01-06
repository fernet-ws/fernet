<?php

declare(strict_types=1);

namespace Fernet\Core;

use Fernet\Framework;
use Monolog\Logger;

class ComponentElement
{
    private object $component;

    private static int $idCounter = 0;

    /** @noinspection PhpUnhandledExceptionInspection */
    public function __construct($classOrObject, array $params = [], string $childContent = '')
    {
        $component = \is_string($classOrObject) ?
            $this->getObject($classOrObject) :
            $classOrObject;
        if (!method_exists($component, '__toString')) {
            $class = \get_class($component);
            throw new Exception("Component \"$class\" needs to implement __toString method");
        }
        foreach ($params as $key => $value) {
            $component->$key = $value;
        }
        $component->childContent = $childContent;
        $this->component = $component;
    }

    public function getComponent(): object
    {
        return $this->component;
    }

    private function getFromContainer(string $class): object
    {
        return Framework::getInstance()->getContainer()->get($class);
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    private function getObject(string $class): object
    {
        // TODO: Cache this
        if (class_exists($class)) {
            return $this->getFromContainer($class);
        }
        $namespaces = Framework::config('componentNamespaces');
        foreach ($namespaces as $namespace) {
            $classWithNamespace = $namespace.'\\'.$class;
            if (class_exists($classWithNamespace)) {
                return $this->getFromContainer($classWithNamespace);
            }
        }
        throw new NotFoundException(sprintf('Component "%s" not defined in ["%s"]', $class, implode('", "', $namespaces)));
    }

    /**
     * @param $method
     * @param $args
     * @return mixed
     * @throws NotFoundException
     */
    public function call($method, $args)
    {
        if (!method_exists($this->component, $method)) {
            throw new NotFoundException(sprintf('Method "%s" not found in component "%s"', $method, \get_class($this->component)));
        }

        return \call_user_func_array([$this->component, $method], $args);
    }

    public function render(): string
    {
        $class = \get_class($this->component);
        $this->getFromContainer(Logger::class)->debug("Rendering \"$class\"");
        $content = (string) $this->component;
        $content = (new ReplaceComponents())->replace($content);
        $content = (new ReplaceAttributes())->replace($content, $this->component);
        if (
            (isset($this->component->preventWrapper) && $this->component->preventWrapper)
            || !Framework::config('enableJs')
            ) {
            return $content;
        }
        $id = static::$idCounter++;

        return "<div id=\"_fernet_component_$id\" class=\"_fernet_component\">$content</div>";
    }
}
