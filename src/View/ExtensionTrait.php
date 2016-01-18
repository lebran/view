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


}