<?php
/**
 * Created by PhpStorm.
 * User: mindkicker
 * Date: 08.01.16
 * Time: 15:37
 */

namespace Lebran\View;

use Lebran\View;
use Lebran\View\Template\BlocksTrait;

class Template
{
    use BlocksTrait;

    /**
     * @var string The name of last rendered template.
     */
    protected $template;
    /**
     * @var View The name of last rendered template.
     */
    protected $view;
    /**
     * @var string The name of last rendered template.
     */
    protected $data;

    public function __construct($view, $template, $data)
    {
        $this->view = $view;
        $this->template = $template;
        $this->data = $data;
    }

    /**
     * Include view.
     *
     * @param string $template The name of template.
     *
     * @return void
     * @throws \Lebran\View\Exception
     */
    protected function import($template)
    {
        $this->template = $template;
        extract($this->data);
        include $this->view->resolvePath($this->template);
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
            $this->blocks['content'] = ob_get_clean();
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
        return call_user_func_array($this->view->getFunction($method), $parameters);
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
        return $this->view->getExtension($name);
    }
}