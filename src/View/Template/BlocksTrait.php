<?php
namespace Lebran\View\Template;

trait BlocksTrait
{
    /**
     * @var array Stack for parent files.
     */
    protected $layouts = [];

    /**
     * @var array Stack for blocks.
     */
    protected $stack = [];

    /**
     * @var array An array of blocks content.
     */
    protected $blocks = [];

    /**
     * @var array Stack for parent hashes.
     */
    protected $parents = [];

    /**
     * Set the template's layout.
     *
     * @param string $layout The name of layout.
     *
     * @return void
     */
    protected function extend($layout)
    {
        $this->layouts[] = $layout;
    }

    /**
     * Returns the content for a section block.
     *
     * @param string $name    The name of block.
     * @param string $default Default block content.
     */
    public function output($name, $default = '')
    {
        if (array_key_exists($name, $this->blocks)) {
            echo $this->blocks[$name];
        } else {
            echo $default;
        }
    }


    /**
     * Start a new section block.
     *
     * @param string $name The name of block.
     * @param string $content
     */
    public function block($name, $content = null)
    {
        if($content){
            if (0 === count($this->layouts)) {
                echo $content;
            } else {
                $this->blocks[$name] = $content;
            }
        } else {
            $this->stack[] = $name;
            ob_start();
        }
    }

    /**
     * End the last section block.
     *
     * @param bool $last True - print block.
     */
    public function end($last = false)
    {
        $name = array_pop($this->stack);
        $view = ob_get_clean();
        if (array_key_exists($name, $this->parents) && array_key_exists($name, $this->blocks)) {
            $this->blocks[$name] = $view = str_replace($this->parents[$name], $view, $this->blocks[$name]);
        }

        if (array_key_exists($name, $this->blocks)) {
            if ($last || 0 === count($this->layouts)) {
                echo $this->blocks[$name];
            }
        } else {
            if ($last || 0 === count($this->layouts)) {
                echo $view;
            } else {
                $this->blocks[$name] = $view;
            }
        }
    }

    /**
     * Returns the parent block content.
     *
     * @return void
     */
    public function parent()
    {
        $key = end($this->stack);
        echo $this->parents[$key] = hash('sha256', $key);
    }
}