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
use Mautic\CampaignBundle\Event as Events;
use Mautic\CampaignBundle\CampaignEvents;

/**
 * Class CampaignSubscriber
 *
 * @package Mautic\CampaignBundle\EventListener
 */
class CampaignSubscriber extends CommonSubscriber
{

    /**
     * @return array
     */
    static public function getSubscribedEvents()
    {
        return array(
            CampaignEvents::CAMPAIGN_POST_SAVE     => array('onCampaignPostSave', 0),
            CampaignEvents::CAMPAIGN_POST_DELETE   => array('onCampaignDelete', 0),
            CampaignEvents::CAMPAIGN_ON_BUILD      => array('onCampaignBuild', 0)
        );
    }

    /**
     * Add an entry to the audit log
     *
     * @param Events\CampaignEvent $event
     */
    public function onCampaignPostSave(Events\CampaignEvent $event)
    {
        $campaign = $event->getCampaign();
        $details = $event->getChanges();

        //don't set leads
        unset($details['leads']);

        if (!empty($details)) {
            $log = array(
                "bundle"    => "campaign",
                "object"    => "campaign",
                "objectId"  => $campaign->getId(),
                "action"    => ($event->isNew()) ? "create" : "update",
                "details"   => $details,
                "ipAddress" => $this->factory->getIpAddressFromRequest()
            );
            $this->factory->getModel('core.auditLog')->writeToLog($log);
        }
    }

    /**
     * Add a delete entry to the audit log
     *
     * @param Events\CampaignEvent $event
     */
    public function onCampaignDelete(Events\CampaignEvent $event)
    {
        $campaign = $event->getCampaign();
        $log = array(
            "bundle"     => "campaign",
            "object"     => "campaign",
            "objectId"   => $campaign->deletedId,
            "action"     => "delete",
            "details"    => array('name' => $campaign->getName()),
            "ipAddress"  => $this->factory->getIpAddressFromRequest()
        );
        $this->factory->getModel('core.auditLog')->writeToLog($log);
    }

    /**
     * Add event triggers and actions
     *
     * @param Events\CampaignBuilderEvent $event
     */
    public function onCampaignBuild(Events\CampaignBuilderEvent $event)
    {
        //Add action to actually add/remove lead to a specific lists
        $addRemoveLeadAction = array(
            'label'           => 'mautic.campaign.event.addremovelead',
            'description'     => 'mautic.campaign.event.addremovelead_descr',
            'formType'        => 'campaignevent_addremovelead',
            'formTypeOptions' => array(
                'include_this' => true
            ),
            'callback'        => '\Mautic\CampaignBundle\Helper\CampaignEventHelper::addRemoveLead'
        );
        $event->addAction('campaign.addremovelead', $addRemoveLeadAction);
    }
}
