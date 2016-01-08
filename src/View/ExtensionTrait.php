<?php
/**
 * Created by PhpStorm.
 * User: mindkicker
 * Date: 26.11.15
 * Time: 17:55
 */

namespace Lebran\View;

use Closure;
use BadMethodCallException;

trait ExtensionTrait
{
    /**
     * @var array Storage for extensions.
     */
    protected $extensions = [];

    /**
     * @var array Storage for extension methods or functions.
     */
    protected $functions = [];

    /**
     * Adds extensions.
     *
     * @param ExtensionInterface $extension Extension object.
     *
     * @return object View object.
     */
    public function addExtension(ExtensionInterface $extension)
    {
        $this->extensions[$extension->getName()] = $extension;
        foreach ($extension->getMethods() as $key => $value) {
            $this->functions[$key] = [$extension, $value];
        }
        return $this;
    }

    public function addExtensions(array $extensions)
    {
        foreach ($extensions as $extension) {
            $this->addExtension($extension);
        }
        return $this;
    }

    public function addFunction($name, Closure $function)
    {
        $this->functions[$name] = $function;
        return $this;
    }

    /**
     * Magic method used to call extension functions.
     *
     * @param string $method     The name of method.
     * @param array  $parameters The params of method.
     *
     * @return mixed Method response.
     * @throws \Lebran\View\Exception
     */
    public function __call($method, $parameters)
    {
        if (array_key_exists($method, $this->functions)) {
            return call_user_func_array($this->functions[$method], $parameters);
        } else {
            throw new BadMethodCallException('The extension method or function "'.$method.'" not found.');
        }
    }

    /**
     * Magic method used to get extension.
     *
     * @param string $name The name of extension.
     *
     * @return object Extension object.
     * @throws \Lebran\View\Exception
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->extensions)) {
            return $this->extensions[$name];
        } else {
            throw new Exception('The extension "'.$name.'" not found.');
        }
    }

    public function __set($name, $extension)
    {
        $this->addExtension($name, $extension);
    }
}