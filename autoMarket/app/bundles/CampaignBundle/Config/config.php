<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

return array(
    'routes'   => array(
        'main' => array(
            'mautic_campaignevent_action' => array(
                'path'       => '/campaigns/events/{objectAction}/{objectId}',
                'controller' => 'MauticCampaignBundle:Event:execute'
            ),
            'mautic_campaignsource_action'      => array(
                'path'       => '/campaigns/sources/{objectAction}/{objectId}',
                'controller' => 'MauticCampaignBundle:Source:execute'
            ),
            'mautic_campaign_index'       => array(
                'path'       => '/campaigns/{page}',
                'controller' => 'MauticCampaignBundle:Campaign:index'
            ),
            'mautic_campaign_leads'       => array(
                'path'       => '/campaigns/view/{objectId}/leads/{page}',
                'controller' => 'MauticCampaignBundle:Campaign:leads',
            ),
            'mautic_campaign_action'      => array(
                'path'       => '/campaigns/{objectAction}/{objectId}',
                'controller' => 'MauticCampaignBundle:Campaign:execute'
            )
        ),
        'api'  => array(
            'mautic_api_getcampaigns' => array(
                'path'       => '/campaigns',
                'controller' => 'MauticCampaignBundle:Api\CampaignApi:getEntities'
            ),
            'mautic_api_getcampaign'  => array(
                'path'       => '/campaigns/{id}',
                'controller' => 'MauticCampaignBundle:Api\CampaignApi:getEntity'
            ),
            'mautic_api_campaignaddlead' => array(
                'path'       => '/campaigns/{id}/lead/add/{leadId}',
                'controller' => 'MauticCampaignBundle:Api\CampaignApi:addLead',
                'method'     => 'POST'
            ),
            'mautic_api_campaignremovelead' => array(
                'path'       => '/campaigns/{id}/lead/remove/{leadId}',
                'controller' => 'MauticCampaignBundle:Api\CampaignApi:removeLead',
                'method'     => 'POST'
            )
        )
    ),

    'menu'     => array(
        'main' => array(
            'priority' => 10,
            'items'    => array(
                'mautic.campaign.campaigns' => array(
                    'route'     => 'mautic_campaign_index',
                    'id'        => 'mautic_campaigns_root',
                    'iconClass' => 'fa-clock-o',
                    'access'    => 'campaign:campaigns:view'
                )
            )
        )
    ),

    'categories' => array(
        'campaign' => null
    ),

    'services' => array(
        'events' => array(
            'mautic.campaign.subscriber'                => array(
                'class' => 'Mautic\CampaignBundle\EventListener\CampaignSubscriber'
            ),
            'mautic.campaign.leadbundle.subscriber'     => array(
                'class' => 'Mautic\CampaignBundle\EventListener\LeadSubscriber'
            ),
            'mautic.campaign.calendarbundle.subscriber' => array(
                'class' => 'Mautic\CampaignBundle\EventListener\CalendarSubscriber'
            ),
            'mautic.campaign.pointbundle.subscriber'    => array(
                'class' => 'Mautic\CampaignBundle\EventListener\PointSubscriber'
            ),
            'mautic.campaign.search.subscriber'         => array(
                'class' => 'Mautic\CampaignBundle\EventListener\SearchSubscriber'
            )
        ),
        'forms'  => array(
            'mautic.campaign.type.form'                 => array(
                'class'     => 'Mautic\CampaignBundle\Form\Type\CampaignType',
                'arguments' => 'mautic.factory',
                'alias'     => 'campaign'
            ),
            'mautic.campaignrange.type.action'          => array(
                'class' => 'Mautic\CampaignBundle\Form\Type\EventType',
                'alias' => 'campaignevent'
            ),
            'mautic.campaign.type.campaignlist'         => array(
                'class'     => 'Mautic\CampaignBundle\Form\Type\CampaignListType',
                'arguments' => 'mautic.factory',
                'alias'     => 'campaign_list'
            ),
            'mautic.campaign.type.trigger.leadchange'   => array(
                'class' => 'Mautic\CampaignBundle\Form\Type\CampaignEventLeadChangeType',
                'alias' => 'campaignevent_leadchange'
            ),
            'mautic.campaign.type.action.addremovelead' => array(
                'class' => 'Mautic\CampaignBundle\Form\Type\CampaignEventAddRemoveLeadType',
                'alias' => 'campaignevent_addremovelead'
            ),
            'mautic.campaign.type.canvassettings'       => array(
                'class' => 'Mautic\CampaignBundle\Form\Type\EventCanvasSettingsType',
                'alias' => 'campaignevent_canvassettings'
            ),
            'mautic.campaign.type.leadsource'           => array(
                'class'     => 'Mautic\CampaignBundle\Form\Type\CampaignLeadSourceType',
                'arguments' => 'mautic.factory',
                'alias'     => 'campaign_leadsource'
            ),
        )
    )
);
