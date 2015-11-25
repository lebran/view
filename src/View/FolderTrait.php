<?php
namespace Lebran\View;

trait FolderTrait
{
    protected static $extension = 'php';

    protected static $separator = '::';

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

    public function addFolders(array $folders){
        $this->folders = array_merge($this->folders, array_map([$this, 'escapePath']), $folders);
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
        $parts = explode(static::$separator, $template);
        $count = count($parts);
        if (1 === $count) {
            foreach ($this->folders as $folder) {
                if (is_file($folder.$parts[0].'.php')) {
                    return $folder.$parts[0].'.php';
                }
            }
        } else if (count($parts) === 2 && is_file($this->folders[$parts[0]].$parts[1].'.php')) {
            return $this->folders[$parts[0]].$parts[1].'.php';
        }

        throw new Exception(
            'The template name "'.$template.'" is not valid. '.
            'Do not use the folder namespace separator "::" more than once.'
        );
    }

    protected function escapePath($path)
    {
        return trim(trim($path), '/');
    }
}