<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
namespace Mautic\AssetBundle\EventListener;

use Mautic\AssetBundle\AssetEvents;
use Mautic\AssetBundle\Event\AssetEvent;
use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\PointBundle\Event\PointBuilderEvent;
use Mautic\PointBundle\PointEvents;

/**
 * Class PointSubscriber
 *
 * @package Mautic\AssetBundle\EventListener
 */
class PointSubscriber extends CommonSubscriber
{

    /**
     * @return array
     */
    static public function getSubscribedEvents()
    {
        return array(
            PointEvents::POINT_ON_BUILD    => array('onPointBuild', 0),
            AssetEvents::ASSET_ON_DOWNLOAD => array('onAssetDownload', 0)
        );
    }

    /**
     * @param PointBuilderEvent $event
     */
    public function onPointBuild(PointBuilderEvent $event)
    {
        $action = array(
            'group'       => 'mautic.asset.actions',
            'label'       => 'mautic.asset.point.action.download',
            'description' => 'mautic.asset.point.action.download_descr',
            'callback'    => array('\\Mautic\\AssetBundle\\Helper\\PointActionHelper', 'validateAssetDownload'),
            'formType'    => 'pointaction_assetdownload'
        );

        $event->addAction('asset.download', $action);
    }

    /**
     * Trigger point actions for asset download
     *
     * @param AssetEvent $event
     */
    public function onAssetDownload(AssetEvent $event)
    {
        $this->factory->getModel('point')->triggerAction('asset.download', $event->getAsset());
    }
}