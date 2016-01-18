<?php
namespace Lebran;

use Lebran\Utils\Storage;
use Lebran\View\Exception;
use Lebran\View\ExtensionTrait;
use Lebran\View\FolderTrait;
use Lebran\View\Extension\ExtensionInterface;
use Lebran\View\Template;

/**
 * Lebran\Mvc\View is a native PHP template system that's fast and easy to extend.
 * It's inspired by compiled template engines (Twig, Blade, ...).
 *
 *                              Examples
 *  <code>
 *      $view = new Lebran\Mvc\View();
 *      $view->addExtension(new Lebran\Mvc\View\Blocks())
 *              ->addFolder('main', '/templates/main')
 *              ->addFolder('new', '/templates/new')
 *              ->enableShortTags()
 *              ->render('new::news');
 *
 *      // templates/new/news.php
 *
 *      <?php $layout('main::index')?>
 *
 *      <?php $block('data')?>
 *          Some text.
 *      <?php $endblock()?>
 *
 *      // templates/main/index.php
 *
 *      <?php $output('data', 'Some default text')?>
 *
 *  </code>
 *
 * @package    Mvc
 * @subpackage View
 * @version    2.0.0
 * @author     Roman Kritskiy <itoktor@gmail.com>
 * @license    GNU Licence
 * @copyright  2014 - 2015 Roman Kritskiy
 */
class View extends Storage
{
    use FolderTrait, ExtensionTrait;

    /**
     * Initialisation. Prepare extensions.
     *
     * @param array $extensions An array of extensions.
     * @param array $data       An array of data.
     */
    public function __construct(array $extensions = [], array $data = [])
    {
        parent::__construct($data);
        $this->setFileExtension('php');
        foreach ($extensions as $extension) {
            $this->addExtension($extension);
        }
    }

    public function render($template, array $data = [])
    {
        return (new Template($this, $template, array_merge_recursive($data, $this->storage)))->make();
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
        extract($this->storage);
        include $this->resolvePath($this->template);
    }
}