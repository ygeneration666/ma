<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\LeadBundle\EventListener;

use Mautic\ApiBundle\Event\RouteEvent;
use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\CoreBundle\CoreEvents;
use Mautic\CoreBundle\Event as MauticEvents;

/**
 * Class SearchSubscriber
 *
 * @package Mautic\LeadBundle\EventListener
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
    public function onGlobalSearch (MauticEvents\GlobalSearchEvent $event)
    {
        $str = $event->getSearchString();
        if (empty($str)) {
            return;
        }

        $anonymous = $this->translator->trans('mautic.lead.lead.searchcommand.isanonymous');
        $mine      = $this->translator->trans('mautic.core.searchcommand.ismine');
        $filter    = array("string" => $str, "force" => '');

        //only show results that are not anonymous so as to not clutter up things
        if (strpos($str, "$anonymous") === false) {
            $filter['force'] = " !$anonymous";
        }

        $permissions = $this->security->isGranted(
            array('lead:leads:viewown', 'lead:leads:viewother'),
            'RETURN_ARRAY'
        );

        if ($permissions['lead:leads:viewown'] || $permissions['lead:leads:viewother']) {
            //only show own leads if the user does not have permission to view others
            if (!$permissions['lead:leads:viewother']) {
                $filter['force'] .= " $mine";
            }

            $results = $this->factory->getModel('lead.lead')->getEntities(
                array(
                    'limit'          => 5,
                    'filter'         => $filter,
                    'withTotalCount' => true
                ));

            $count = $results['count'];

            if ($count > 0) {
                $leads       = $results['results'];
                $leadResults = array();

                foreach ($leads as $lead) {
                    $leadResults[] = $this->templating->renderResponse(
                        'MauticLeadBundle:SubscribedEvents\Search:global.html.php',
                        array('lead' => $lead)
                    )->getContent();
                }

                if ($results['count'] > 5) {
                    $leadResults[] = $this->templating->renderResponse(
                        'MauticLeadBundle:SubscribedEvents\Search:global.html.php',
                        array(
                            'showMore'     => true,
                            'searchString' => $str,
                            'remaining'    => ($results['count'] - 5)
                        )
                    )->getContent();
                }
                $leadResults['count'] = $results['count'];
                $event->addResults('mautic.lead.leads', $leadResults);
            }
        }
    }

    /**
     * @param MauticEvents\CommandListEvent $event
     */
    public function onBuildCommandList (MauticEvents\CommandListEvent $event)
    {
        if ($this->security->isGranted(array('lead:leads:viewown', 'lead:leads:viewother'), "MATCH_ONE")) {
            $event->addCommands(
                'mautic.lead.leads',
                $this->factory->getModel('lead.lead')->getCommandList()
            );
        }
    }
}