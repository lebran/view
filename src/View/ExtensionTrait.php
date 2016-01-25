<?php
namespace Lebran\View;

use Closure;
use Lebran\View\Extension\ExtensionInterface;

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
     * @return $this View object.
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

    public function call($name, $parameters)
    {
        return call_user_func_array($this->getFunction($name), $parameters);
    }

    public function getExtension($name)
    {
        if(array_key_exists($name, $this->extensions)){
            return $this->extensions[$name];
        }

        throw new Exception('The extension method "'.$name.'" not found.');
    }

    public function getFunction($name)
    {
        if(array_key_exists($name, $this->functions)){
            return $this->functions[$name];
        }

        throw new Exception('The extension function "'.$name.'" not found.');
    }


}