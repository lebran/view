<?php
/**
 * Created by PhpStorm.
 * User: mindkicker
 * Date: 13.01.16
 * Time: 15:56
 */

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
     * Start a new section block.
     *
     * @param string $name The name of block.
     */
    public function block($name)
    {
        $this->stack[] = $name;
        ob_start();
    }

    /**
     * End the last section block.
     *
     * @param bool $last True - print block.
     */
    public function endblock($last = false)
    {
        $name = array_pop($this->stack);
        $view = ob_get_clean();
        if (array_key_exists($name, $this->parents) && array_key_exists($name, $this->blocks)) {
            $this->blocks[$name] = $view = str_replace($this->parents[$name], $view, $this->blocks[$name]);
        }

        if (array_key_exists($name, $this->blocks)) {
            if ($last) {
                echo $this->blocks[$name];
            }
        } else {
            if ($last) {
                echo $view;
            } else {
                $this->blocks[$name] = $view;
            }
        }
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