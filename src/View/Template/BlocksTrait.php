<?php
namespace Lebran\View\Template;

use Lebran\View\Exception;

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
     * @param bool $print True - print block.
     * @throws Exception
     */
    public function end($print = false)
    {
        if(empty($name = array_pop($this->stack))){
            throw new Exception('');
        }

        $html = ob_get_clean();
        if (array_key_exists($name, $this->parents)) {
            $this->blocks[$name] = str_replace($this->parents[$name], $html, $this->blocks[$name]);
            unset($this->parents[$name]);
        }

        if($print || 0 === count($this->layouts)){
            echo array_key_exists($name, $this->blocks)? $this->blocks[$name]:$html;
        } else {
            if(!array_key_exists($name, $this->blocks)){
                $this->blocks[$name] = $html;
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