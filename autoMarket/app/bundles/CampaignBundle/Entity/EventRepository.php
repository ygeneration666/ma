<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\CampaignBundle\Entity;

use Mautic\CoreBundle\Entity\CommonRepository;
use Mautic\LeadBundle\Entity\Lead as RealLead;

/**
 * EventRepository
 */
class EventRepository extends CommonRepository
{

    /**
     * Get a list of entities
     *
     * @param array $args
     *
     * @return Paginator
     */
    public function getEntities($args = array())
    {
        $q = $this
            ->createQueryBuilder('e')
            ->select('e, ec, ep')
            ->join('e.campaign', 'c')
            ->leftJoin('e.children', 'ec')
            ->leftJoin('e.parent', 'ep');

        $args['qb'] = $q;

        return parent::getEntities($args);
    }

    /**
     * Get array of published events based on type
     * @param       $type
     * @param array $campaigns
     * @param null  $leadId             If included, only events that have not been triggered by the lead yet will be included
     * @param bool  $positivePathOnly   If negative, all events including those with a negative path will be returned
     *
     * @return array
     */
    public function getPublishedByType($type, array $campaigns = null, $leadId = null, $positivePathOnly = true)
    {
        $q = $this->createQueryBuilder('e')
            ->select('c, e, ec, ep, ecc')
            ->join('e.campaign', 'c')
            ->leftJoin('e.children', 'ec')
            ->leftJoin('e.parent', 'ep')
            ->leftJoin('ec.campaign', 'ecc')
            ->orderBy('e.order');

        //make sure the published up and down dates are good
        $expr = $this->getPublishedByDateExpression($q, 'c');

        $expr->add(
            $q->expr()->eq('e.type', ':type')
        );

        $q->where($expr)
            ->setParameter('type', $type);

        if (!empty($campaigns)) {
            $q->andWhere($q->expr()->in('c.id', ':campaigns'))
                ->setParameter('campaigns', $campaigns);
        }

        if ($leadId != null) {
            // Events that aren't fired yet
            $dq = $this->_em->createQueryBuilder();
            $dq->select('ellev.id')
                ->from('MauticCampaignBundle:LeadEventLog', 'ell')
                ->leftJoin('ell.event', 'ellev')
                ->leftJoin('ell.lead', 'el')
                ->where('ellev.id = e.id')
                ->andWhere(
                    $dq->expr()->eq('el.id', ':leadId')
                );

            $q->andWhere('e.id NOT IN('.$dq->getDQL().')')
                ->setParameter('leadId', $leadId);
        }

        if ($positivePathOnly) {
            $q->andWhere(
                $q->expr()->orX(
                    $q->expr()->neq(
                        'e.decisionPath',
                        $q->expr()->literal('no')
                    ),
                    $q->expr()->isNull('e.decisionPath')
                )
            );

        }

        $results = $q->getQuery()->getArrayResult();

        //group them by campaign
        $events = array();
        foreach ($results as $r) {
            $events[$r['campaign']['id']][$r['id']] = $r;
        }

        return $events;
    }

    /**
     * Get array of events by parent
     *
     * @param      $parentId
     * @param null $decisionPath
     *
     * @return array
     */
    public function getEventsByParent($parentId, $decisionPath = null)
    {
        $q = $this->_em->createQueryBuilder();

        $q->select('e')
            ->from('MauticCampaignBundle:Event', 'e', 'e.id')
            ->where(
                $q->expr()->eq('IDENTITY(e.parent)', (int) $parentId)
            );

        if ($decisionPath != null) {
            $q->andWhere(
                $q->expr()->eq('e.decisionPath', ':decisionPath')
            )
                ->setParameter('decisionPath', $decisionPath);
        }

        return $q->getQuery()->getArrayResult();
    }

    /**
     * Get the top level events for a campaign
     *
     * @param $id
     * @param $includeDecisions
     *
     * @return array
     */
    public function getRootLevelEvents($id, $includeDecisions = false)
    {
        $q = $this->_em->createQueryBuilder();

        $q->select('e')
            ->from('MauticCampaignBundle:Event', 'e', 'e.id')
            ->where(
                $q->expr()->andX(
                    $q->expr()->eq('IDENTITY(e.campaign)', (int) $id),
                    $q->expr()->isNull('e.parent')
                )
            );

        if (!$includeDecisions) {
            $q->andWhere(
                $q->expr()->neq('e.eventType', $q->expr()->literal('decision'))
            );
        }

        $results = $q->getQuery()->getArrayResult();

        return $results;
    }

    /**
     * Gets ids of leads who have already triggered the event
     *
     * @param $events
     * @param $leadId
     *
     * @return array
     */
    public function getEventLogLeads($events, $leadId = null)
    {
        $q = $this->_em->getConnection()->createQueryBuilder();

        $q->select('distinct(e.lead_id)')
            ->from(MAUTIC_TABLE_PREFIX.'campaign_lead_event_log', 'e')
            ->where(
                $q->expr()->in('e.event_id', $events)
            )
            ->setParameter('false', false, 'boolean');

        if ($leadId) {
            $q->andWhere(
                $q->expr()->eq('e.lead_id', (int) $leadId)
            );
        }

        $results = $q->execute()->fetchAll();

        $log = array();
        foreach ($results as $r) {
            $log[] = $r['lead_id'];
        }

        unset($results);

        return $log;
    }

    /**
     * Get an array of events that have been triggered by this lead
     *
     * @param $leadId
     *
     * @return array
     */
    public function getLeadTriggeredEvents($leadId)
    {
        $q = $this->_em->createQueryBuilder()
            ->select('e, c, l')
            ->from('MauticCampaignBundle:Event', 'e')
            ->join('e.campaign', 'c')
            ->join('e.log', 'l');

        //make sure the published up and down dates are good
        $q->where($q->expr()->eq('IDENTITY(l.lead)', (int) $leadId));

        $results = $q->getQuery()->getArrayResult();

        $return = array();
        foreach ($results as $r) {
            $return[$r['id']] = $r;
        }

        return $return;
    }

    /**
     * Get a list of scheduled events
     *
     * @param      $campaignId
     * @param bool $count
     * @param int  $limit
     *
     * @return array|bool
     */
    public function getScheduledEvents($campaignId, $count = false, $limit = 0)
    {
        $date = new \Datetime();

        $q = $this->_em->createQueryBuilder()
            ->from('MauticCampaignBundle:LeadEventLog', 'o');

        $q->where(
            $q->expr()->andX(
                $q->expr()->eq('IDENTITY(o.campaign)', (int) $campaignId),
                $q->expr()->eq('o.isScheduled', ':true'),
                $q->expr()->lte('o.triggerDate', ':now')
            )
        )
            ->setParameter('now', $date)
            ->setParameter('true', true, 'boolean');

        if ($count) {
            $q->select('count(o) as event_count');

            $results = $results = $q->getQuery()->getArrayResult();
            $count   = $results[0]['event_count'];

            return $count;
        }

        $q->select('o')
            ->orderBy('o.triggerDate', 'DESC');

        if ($limit) {
            $q->setFirstResult(0)
                ->setMaxResults($limit);
        }

        $results = $q->getQuery()->getArrayResult();

        // Organize by lead
        $leads = array();
        foreach ($results as $e) {
            $leads[$e['lead_id']][$e['event_id']] = $e;
        }
        unset($results);

        return $leads;
    }

    /**
     * @param $campaignId
     *
     * @return array
     */
    public function getCampaignEvents($campaignId)
    {
        $q = $this->_em->createQueryBuilder();
        $q->select('e, IDENTITY(e.parent)')
            ->from('MauticCampaignBundle:Event', 'e', 'e.id')
            ->where(
                $q->expr()->eq('IDENTITY(e.campaign)', (int) $campaignId)
            )
            ->orderBy('e.order', 'ASC');

        $results = $q->getQuery()->getArrayResult();

        // Fix the parent ID
        $events = array();
        foreach ($results as $id => $r) {
            $r[0]['parent_id'] = $r[1];
            $events[$id]       = $r[0];
        }
        unset($results);

        return $events;
    }

    /**
     * Get array of events with stats
     *
     * @param array $args
     *
     * @return array
     */
    public function getEvents($args = array())
    {
        $q = $this->createQueryBuilder('e')
            ->select('e, ec, ep')
            ->join('e.campaign', 'c')
            ->leftJoin('e.children', 'ec')
            ->leftJoin('e.parent', 'ep')
            ->orderBy('e.order');

        if (!empty($args['campaigns'])) {
            $q->andWhere($q->expr()->in('e.campaign', ':campaigns'))
                ->setParameter('campaigns', $args['campaigns']);
        }

        if (isset($args['positivePathOnly'])) {
            $q->andWhere(
                $q->expr()->orX(
                    $q->expr()->neq(
                        'e.decisionPath',
                        $q->expr()->literal('no')
                    ),
                    $q->expr()->isNull('e.decisionPath')
                )
            );

        }

        $events = $q->getQuery()->getArrayResult();

        return $events;
    }

    /**
     * @param $campaignId
     *
     * @return array
     */
    public function getCampaignActionAndConditionEvents($campaignId)
    {
        $q = $this->_em->createQueryBuilder();
        $q->select('e')
            ->from('MauticCampaignBundle:Event', 'e', 'e.id')
            ->where($q->expr()->eq('IDENTITY(e.campaign)', (int) $campaignId))
            ->andWhere($q->expr()->in('e.eventType', array('action', 'condition')));

        $events = $q->getQuery()->getArrayResult();

        return $events;
    }

    /**
     * Get the non-action log
     *
     * @param       $campaignId
     * @param array $leads
     * @param array $havingEvents
     * @param array $excludeEvents
     *
     * @return array
     */
    public function getEventLog($campaignId, $leads = array(), $havingEvents = array(), $excludeEvents = array())
    {
        $q = $this->_em->getConnection()->createQueryBuilder();

        $q->select('e.lead_id, e.event_id, e.date_triggered, e.is_scheduled')
            ->groupBy('e.lead_id, e.event_id, e.date_triggered, e.is_scheduled')
            ->from(MAUTIC_TABLE_PREFIX.'campaign_lead_event_log', 'e')
            ->where(
                $q->expr()->eq('e.campaign_id', (int) $campaignId)
            )
            ->groupBy('e.lead_id, e.event_id, e.date_triggered, e.is_scheduled');

        if (!empty($leads)) {
            $q->andWhere(
                $q->expr()->in('e.lead_id', $leads)
            );
        }

        if (!empty($havingEvents)) {
            $dq = $this->_em->getConnection()->createQueryBuilder();

            $dq->select('count(eh.event_id)')
                ->from(MAUTIC_TABLE_PREFIX.'campaign_lead_event_log', 'eh')
                ->where(
                    $dq->expr()->eq('eh.lead_id', 'e.lead_id'),
                    $dq->expr()->in('eh.event_id', $havingEvents)
                );

            $q->having(
                sprintf('(%s) > 0', $dq->getSQL())
            );
        }

        if (!empty($excludeEvents)) {
            $dq = $this->_em->getConnection()->createQueryBuilder();

            $dq->select('count(eh.event_id)')
                ->from(MAUTIC_TABLE_PREFIX.'campaign_lead_event_log', 'eh')
                ->where(
                    $dq->expr()->eq('eh.lead_id', 'e.lead_id'),
                    $dq->expr()->in('eh.event_id', $excludeEvents)
                );

            $q->andHaving(
                sprintf('(%s) = 0', $dq->getSQL())
            );
        }

        $results = $q->execute()->fetchAll();

        $log = array();
        foreach ($results as $r) {
            $leadId  = $r['lead_id'];
            $eventId = $r['event_id'];

            unset($r['lead_id']);
            unset($r['event_id']);

            $log[$leadId][$eventId] = $r;
        }

        unset($results);

        return $log;
    }

    /**
     * Null event parents in preparation for deleting a campaign
     *
     * @param $campaignId
     */
    public function nullEventParents($campaignId)
    {
        $this->_em->getConnection()->update(
            MAUTIC_TABLE_PREFIX.'campaign_events',
            array('parent_id' => null),
            array('campaign_id' => (int) $campaignId)
        );
    }
}
