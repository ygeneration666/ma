<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\CampaignBundle\EventListener;

use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\CoreBundle\CoreEvents;
use Mautic\CoreBundle\Event as MauticEvents;

/**
 * Class SearchSubscriber
 *
 * @package Mautic\CampaignBundle\EventListener
 */
class SearchSubscriber extends CommonSubscriber
{

    /**
     * @return array
     */
    static public function getSubscribedEvents ()
    {
        return array(
            CoreEvents::GLOBAL_SEARCH        => array('onGlobalSearch', 0),
            CoreEvents::BUILD_COMMAND_LIST   => array('onBuildCommandList', 0)
        );
    }

    /**
     * @param MauticEvents\GlobalSearchEvent $event
     */
    public function onGlobalSearch(MauticEvents\GlobalSearchEvent $event)
    {
        if ($this->security->isGranted('campaign:campaigns:view')) {
            $str = $event->getSearchString();
            if (empty($str)) {
                return;
            }

            $campaigns = $this->factory->getModel('campaign')->getEntities(
                array(
                    'limit'  => 5,
                    'filter' => $str
                ));

            if (count($campaigns) > 0) {
                $campaignResults = array();
                foreach ($campaigns as $campaign) {
                    $campaignResults[] = $this->templating->renderResponse(
                        'MauticCampaignBundle:SubscribedEvents\Search:global.html.php',
                        array(
                            'campaign'  => $campaign
                        )
                    )->getContent();
                }
                if (count($campaigns) > 5) {
                    $campaignResults[] = $this->templating->renderResponse(
                        'MauticCampaignBundle:SubscribedEvents\Search:global.html.php',
                        array(
                            'showMore'     => true,
                            'searchString' => $str,
                            'remaining'    => (count($campaigns) - 5)
                        )
                    )->getContent();
                }
                $campaignResults['count'] = count($campaigns);
                $event->addResults('mautic.campaign.campaigns', $campaignResults);
            }
        }
    }

    /**
     * @param MauticEvents\CommandListEvent $event
     */
    public function onBuildCommandList(MauticEvents\CommandListEvent $event)
    {
        $security   = $this->security;
        if ($security->isGranted('campaign:campaigns:view')) {
            $event->addCommands(
                'mautic.campaign.campaigns',
                $this->factory->getModel('campaign')->getCommandList()
            );
        }
    }
}