<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\LeadBundle\Helper;

/**
 * Class PointEventHelper
 *
 * @package Mautic\LeadBundle\Helper
 */
class PointEventHelper
{
    /**
     * @param $event
     * @param $factory
     */
    public static function changeLists ($event, $factory, $lead)
    {
        $properties = $event['properties'];

        /** @var \Mautic\LeadBundle\Model\LeadModel $leadModel */
        $leadModel  = $factory->getModel('lead');
        $addTo      = $properties['addToLists'];
        $removeFrom = $properties['removeFromLists'];

        $somethingHappened = false;

        if (!empty($addTo)) {
            $leadModel->addToLists($lead, $addTo);
            $somethingHappened = true;
        }

        if (!empty($removeFrom)) {
            $leadModel->removeFromLists($lead, $removeFrom);
            $somethingHappened = true;
        }

        return $somethingHappened;
    }
}