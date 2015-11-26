<?php
namespace Lebran;

use Lebran\Utils\Storage;
use Lebran\View\Exception;
use Lebran\View\FolderTrait;
use Lebran\View\Extension\ExtensionInterface;

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
    use FolderTrait;

    /**
     * @var string The name of last rendered template.
     */
    protected $template;

    /**
     * @var array Stack for parent files.
     */
    protected $layouts = [];

    /**
     * @var string Child content.
     */
    protected $content = '';

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

    /**
     * Render the template and layout.
     *
     * @param string $template The name of template.
     * @param array  $data     An array of data.
     *
     * @return string Rendered template.
     * @throws \Lebran\View\Exception
     */
    public function render($template, array $data = [])
    {
        $this->template = $template;
        $this->storage  = array_merge_recursive($this->storage, $data);

        extract($this->storage);
        ob_start();

        include $this->resolvePath($this->template);

        if (0 === count($this->layouts)) {
            return ob_get_clean();
        } else {
            $this->content = ob_get_clean();
            return $this->render(array_pop($this->layouts));
        }
    }

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
     * Prints child content.
     *
     * @return void
     */
    protected function content()
    {
        echo $this->content;
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