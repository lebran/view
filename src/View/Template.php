<?php
namespace Lebran\View;

use Lebran\View;
use Lebran\View\Template\BlocksTrait;
use Lebran\View\Extension\ExtensionInterface;

class Template
{
    use BlocksTrait;

    /**
     * @var View The name of last rendered template.
     */
    protected $view;

    /**
     * @var string The name of last rendered template.
     */
    protected $template;

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
            $this->template = array_pop($this->layouts);
            return $this->make();
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
        return $this->view->call($method, $parameters);
    }

    /**
     * Magic method used to get extension.
     *
     * @param string $name The name of extension.
     *
     * @return ExtensionInterface Extension object.
     * @throws \Lebran\View\Exception
     */
    public function __get($name)
    {
        return $this->view->getExtension($name);
    }
}