<?php
namespace Lebran\View;

use InvalidArgumentException;

trait FolderTrait
{
    protected $file_extension;

    protected $separator = '::';

    /**
     * @var string Paths to the view folders.
     */
    protected $folders = [];

    /**
     * Adds folder for views.
     *
     * @param string $name   The name of folder.
     * @param string $folder Path to folder.
     *
     * @return object View object.
     */
    public function addFolder($name, $folder)
    {
        $this->folders[$name] = $this->escapePath($folder);
        return $this;
    }

    public function addFolders(array $folders)
    {
        $this->folders = array_merge($this->folders, array_map([$this, 'escapePath']), $folders);
        return $this;
    }

    public function setFileExtension($extension)
    {
        $this->file_extension = trim(trim($extension), '.');
    }

    public function setSeparator($separator)
    {
        if(!is_string($separator)){
            throw new InvalidArgumentException('Separator should be string');
        }
        $this->separator = $separator;
        return $this;
    }

    /**
     * Generates path for template.
     *
     * @param string $template The name of template.
     *
     * @return string Resolved path.
     * @throws \Lebran\View\Exception
     */
    protected function resolvePath($template)
    {
        $parts = explode($this->separator, $template);
        $count = count($parts);

        if (1 === $count) {
            $paths  = $this->folders;
            $folder = $parts[0];
        } else if (2 === $count) {
            $paths  = (array)$this->folders[$parts[0]];
            $folder = $parts[1];
        } else {
            throw new Exception('Do not use the folder namespace separator "'.$this->separator.'" more than once.');
        }

        if (($path = $this->checkPaths($paths, $folder))) {
            return $path;
        }

        throw new Exception('The template "'.$template.'" is not found.');
    }

    protected function escapePath($path)
    {
        return trim(trim($path), '/');
    }

    protected function checkPaths(array $paths, $folder)
    {
        foreach ($paths as $path) {
            if (is_file($full = $path.'/'.$folder.'.'.$this->file_extension?$this->file_extension:'')) {
                return $full;
            }
        }

        return false;
    }
}