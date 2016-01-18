<?php
/**
 * Created by PhpStorm.
 * User: mindkicker
 * Date: 08.01.16
 * Time: 15:37
 */

namespace Lebran\View;

class Template
{
    /**
     * @var string The name of last rendered template.
     */
    protected $template;

    public function __construct($view, $template, $data)
    {
        $this->view = $view;
        $this->template = $template;
        $this->data = $data;
    }

    /**
     * Render the template and layout.
     *
     * @param string $template The name of template.
     * @param array  $data     An array of data.
     *
     * @return string Rendered template.
     * @throws \Lebran\View\Exception
     */
    public function make()
    {
        extract($this->data);
        ob_start();

        include $this->view->resolvePath($this->template);

        if (0 === count($this->layouts)) {
            return ob_get_clean();
        } else {
            $this->blocks[]
            return $this->make(array_pop($this->layouts));
        }
    }

    public function render($template, array $data = [])
    {
        return $this->view->render($template, $data);
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