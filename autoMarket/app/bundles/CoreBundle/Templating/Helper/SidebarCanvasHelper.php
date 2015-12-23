<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\CoreBundle\Templating\Helper;

use Mautic\CoreBundle\CoreEvents;
use Mautic\CoreBundle\Event\SidebarCanvasEvent;
use Mautic\CoreBundle\Factory\MauticFactory;
use Symfony\Component\Templating\Helper\Helper;

/**
 * Class SidebarCanvasHelper
 */
class SidebarCanvasHelper extends Helper
{
    private $canvases = array('left', 'main', 'right');
    private $content  = array();

    /**
     * @param MauticFactory $factory
     */
    public function __construct(MauticFactory $factory)
    {
        $this->factory = $factory;
    }

    public function renderCanvasContent($templating)
    {
        $dispatcher = $this->factory->getDispatcher();

        if ($dispatcher->hasListeners(CoreEvents::BUILD_CANVAS_CONTENT)) {
            $event = new SidebarCanvasEvent($templating);
            $dispatcher->dispatch(CoreEvents::BUILD_CANVAS_CONTENT, $event);
            $this->content = $event->getCanvasContent();
        }

        $adminMenuContent = $templating['knp_menu']->render('admin', array("menu" => "admin"));

        if (!empty($adminMenuContent)) {
            $settingsMenu = array(
                'header'  => 'mautic.core.settings',
                'content' => '<nav class="nav-sidebar">' . $adminMenuContent . '</nav>',
                'footer'  => ''
            );

            if (empty($this->content['main'])) {
                //insert settings menu
                $this->content['main'] = $settingsMenu;
            } else {
                $this->content['left'] = $settingsMenu;
            }
        }

        $hasContent = false;
        foreach ($this->canvases as $canvas) {
            if (!isset($this->content[$canvas])) {
                $this->content[$canvas] = false;
            }

            if ($this->content[$canvas]) {
                $hasContent = true;
            }
        }

        if (!$hasContent) {
            $this->content['main'] = array(
                'header'  => false,
                'content' => '<img class="img-responsive mt-lg" style="margin-right: auto; margin-left: auto;" src="'.MautibotHelper::get('wave').'" />',
                'footer'  => ''
            );
        }
    }

    /**
     * @return mixed
     */
    public function getLeftContent()
    {
        return $this->content['left'];
    }

    /**
     * @return mixed
     */
    public function getRightContent()
    {
        return $this->content['right'];
    }

    /**
     * @return mixed
     */
    public function getMainContent()
    {
        return $this->content['main'];
    }

    /**
     * @return array
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'canvas';
    }
}
