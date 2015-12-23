<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\FormBundle\Entity;

use Doctrine\ORM\Query;
use Mautic\CoreBundle\Entity\CommonRepository;
use Mautic\CoreBundle\Helper\DateTimeHelper;
use Mautic\CoreBundle\Helper\GraphHelper;

/**
 * IpAddressRepository
 */
class SubmissionRepository extends CommonRepository
{

    /**
     * {@inheritdoc}
     */
    public function saveEntity($entity, $flush = true)
    {
        parent::saveEntity($entity, $flush);

        //add the results
        $results                  = $entity->getResults();
        $results['submission_id'] = $entity->getId();
        $form                     = $entity->getForm();
        $results['form_id']       = $form->getId();
        $tableName                = MAUTIC_TABLE_PREFIX . 'form_results_' . $form->getId() . '_' . $form->getAlias();
        if (!empty($results)) {
            $this->_em->getConnection()->insert($tableName, $results);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getEntities($args = array())
    {
        $form  = $args['form'];
        $table = MAUTIC_TABLE_PREFIX . 'form_results_' . $form->getId() . '_' . $form->getAlias();

        //DBAL

        //Get the list of custom fields
        $fq = $this->_em->getConnection()->createQueryBuilder();
        $fq->select('f.id, f.label, f.alias, f.type')
            ->from(MAUTIC_TABLE_PREFIX . 'form_fields', 'f')
            ->where('f.form_id = ' . $form->getId())
            ->andWhere(
                $fq->expr()->notIn('f.type', array("'button'", "'freetext'")),
                $fq->expr()->eq('f.save_result', ':saveResult')
            )
            ->orderBy('f.field_order', 'ASC')
            ->setParameter('saveResult', true);
        $results = $fq->execute()->fetchAll();

        $fields = array();
        foreach ($results as $r) {
            $fields[$r['alias']] = $r;
        }
        unset($results);
        $fieldAliases = array_keys($fields);

        $dq = $this->_em->getConnection()->createQueryBuilder();
        $dq->select('count(r.submission_id) as count')
            ->from($table, 'r')
            ->innerJoin('r', MAUTIC_TABLE_PREFIX . 'form_submissions', 's', 'r.submission_id = s.id')
            ->leftJoin('s', MAUTIC_TABLE_PREFIX . 'ip_addresses', 'i', 's.ip_id = i.id')
        ->where('r.form_id = ' . $form->getId());

        $this->buildWhereClause($dq, $args);

        //get a total count
        $result = $dq->execute()->fetchAll();
        $total  = $result[0]['count'];

        //now get the actual paginated results
        $this->buildOrderByClause($dq, $args);
        $this->buildLimiterClauses($dq, $args);

        $dq->resetQueryPart('select');
        $fieldAliasSql = (!empty($fieldAliases)) ? ', ' . implode(',r.', $fieldAliases) : '';
        $dq->select('r.submission_id' . $fieldAliasSql);
        $results = $dq->execute()->fetchAll();

        //loop over results to put form submission results in something that can be assigned to the entities
        $values = array();

        foreach ($results as $result) {
            $submissionId = $result['submission_id'];
            unset($result['submission_id']);

            $values[$submissionId] = array();
            foreach ($result as $k => $r) {
                if (isset($fields[$k])) {
                    $values[$submissionId][$k] = $fields[$k];
                    $values[$submissionId][$k]['value'] = $r;
                }
            }
        }

        //get an array of IDs for ORM query
        $ids = array_keys($values);

        if (count($ids)) {
            //ORM

            //build the order by id since the order was applied above
            //unfortunately, can't use MySQL's FIELD function since we have to be cross-platform
            $order = '(CASE';
            foreach ($ids as $count => $id) {
                $order .= ' WHEN s.id = ' . $id . ' THEN ' . $count;
                $count++;
            }
            $order .= ' ELSE ' . $count . ' END) AS HIDDEN ORD';

            //ORM - generates lead entities
            $q = $this
                ->createQueryBuilder('s');
            $q->select('s, partial l.{id}, p, i,' . $order)
                ->leftJoin('s.ipAddress', 'i')
                ->leftJoin('s.page', 'p')
                ->leftJoin('s.lead', 'l');

            //only pull the submissions as filtered via DBAL
            $q->where(
                $q->expr()->in('s.id', ':ids')
            )->setParameter('ids', $ids);

            $q->orderBy('ORD', 'ASC');
            $results = $q->getQuery()->getArrayResult();

            foreach ($results as &$r) {
                $r['results'] = $values[$r['id']];
            }
        }

        return (!empty($args['withTotalCount'])) ?
            array(
                'count'   => $total,
                'results' => $results
            ) : $results;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity($id = 0)
    {
        $entity = parent::getEntity($id);

        if ($entity != null) {
            $form      = $entity->getForm();
            $tableName = MAUTIC_TABLE_PREFIX . 'form_results_' . $form->getId() . '_' . $form->getAlias();

            //use DBAL to get entity fields
            $q = $this->_em->getConnection()->createQueryBuilder();
            $q->select('*')
                ->from($tableName, 'r')
                ->where('r.submission_id = :id')
                ->setParameter('id', $id);
            $results = $q->execute()->fetchAll();
            unset($results[0]['submission_id']);
            if (isset($results[0])) {
                $entity->setResults($results[0]);
            }
        }

        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilterExpr(&$q, $filter, $parameterName = null)
    {
        if ($filter['column'] == 's.date_submitted') {
            $date  = $this->factory->getDate($filter['value'], 'Y-m-d')->toUtcString();
            $date1 = $this->generateRandomParameterName();
            $date2 = $this->generateRandomParameterName();
            $parameters = array($date1 => $date . ' 00:00:00', $date2 => $date . ' 23:59:59');
            $expr = $q->expr()->andX(
                $q->expr()->gte('s.date_submitted', ":$date1"),
                $q->expr()->lte('s.date_submitted', ":$date2")
            );
            return array($expr, $parameters);
        } else {
            return parent::getFilterExpr($q, $filter);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultOrder()
    {
        return array(
            array('s.date_submitted', 'ASC')
        );
    }

    /**
     * Fetch the base submission data from the database
     *
     * @param array $options
     *
     * @return array
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getSubmissions(array $options = array())
    {
        $query = $this->_em->getConnection()->createQueryBuilder();
        $query->select('fs.id, fs.form_id, fs.page_id, fs.date_submitted AS "dateSubmitted"')
            ->from(MAUTIC_TABLE_PREFIX . 'form_submissions', 'fs');

        if (!empty($options['ipIds'])) {
            $query->where('fs.ip_id IN (' . implode(',', $options['ipIds']) . ')');
        }

        if (!empty($options['leadId'])) {
            $query->andWhere('fs.lead_id = ' . (int)$options['leadId']);
        }

        if (!empty($options['id'])) {
            $query->andWhere($query->expr()->eq('fs.form_id', ':id'))
            ->setParameter('id', $options['id']);
        }

        if (!empty($options['fromDate'])) {
            $query->andWhere($query->expr()->gte('fs.date_submitted', ':fromDate'))
            ->setParameter('fromDate', $options['fromDate']->format('Y-m-d H:i:s'));
        }

        if (isset($options['filters']['search']) && $options['filters']['search']) {
            $query->leftJoin('fs', MAUTIC_TABLE_PREFIX . 'forms', 'f', 'f.id = fs.form_id')
                ->andWhere($query->expr()->orX(
                    $query->expr()->like('f.name', $query->expr()->literal('%' . $options['filters']['search'] . '%')),
                    $query->expr()->like('f.description', $query->expr()->literal('%' . $options['filters']['search'] . '%'))
            ));
        }

        return $query->execute()->fetchAll();
    }

    public function getSubmissionsSince($formId, $amount = 30, $unit = 'D')
    {
        $data = GraphHelper::prepareDatetimeLineGraphData($amount, $unit, array('submissions'));

        $submissions = $this->getSubmissions(array('id' => $formId, 'fromDate' => $data['fromDate']));

        return GraphHelper::mergeLineGraphData($data, $submissions, $unit, 0, 'dateSubmitted');
    }

    /**
     * Get list of forms ordered by it's count
     *
     * @param QueryBuilder $query
     * @param integer $limit
     * @param integer $offset
     *
     * @return array
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTopReferrers($query, $limit = 10, $offset = 0)
    {
        $query->select('fs.referer, count(fs.referer) as sessions')
            ->groupBy('fs.referer')
            ->orderBy('sessions', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        $results = $query->execute()->fetchAll();

        return $results;
    }

    /**
     * Get list of forms ordered by it's count
     *
     * @param QueryBuilder $query
     * @param integer $limit
     * @param integer $offset
     *
     * @return array
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getMostSubmitted($query, $limit = 10, $offset = 0, $column = 'fs.id', $as = 'submissions')
    {
        $asSelect = ($as) ? ' as ' . $as : '';

        $query->select('f.name as title, f.id, count(distinct ' . $column . ')' . $asSelect)
            ->groupBy('f.id, f.name')
            ->orderBy($as, 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        $results = $query->execute()->fetchAll();

        return $results;
    }

    public function getSubmissionCountsByPage($pageId, \DateTime $fromDate = null)
    {
        $q = $this->_em->getConnection()->createQueryBuilder();
        $q->select('count(distinct(s.tracking_id)) as count, s.page_id as id, p.title as name, p.variant_hits as total')
            ->from(MAUTIC_TABLE_PREFIX.'form_submissions', 's')
            ->join('s', MAUTIC_TABLE_PREFIX.'pages', 'p', 's.page_id = p.id');

        if (is_array($pageId)) {
            $q->where($q->expr()->in('s.page_id', $pageId))
                ->groupBy('s.page_id, p.title, p.variant_hits');

        } else {
            $q->where($q->expr()->eq('s.page_id', ':page'))
                ->setParameter('page', (int) $pageId);
        }

        if ($fromDate != null) {
            $dh = new DateTimeHelper($fromDate);
            $q->andWhere($q->expr()->gte('s.date_submitted', ':date'))
                ->setParameter('date', $dh->toUtcString());
        }

        $results = $q->execute()->fetchAll();

        return $results;
    }

    /**
     * Get submission count by email by linking emails that have been associated with a page hit that has the
     * same tracking ID as a form submission tracking ID and thus assumed happened in the same session
     *
     * @param           $emailId
     * @param \DateTime $fromDate
     *
     * @return mixed
     */
    public function getSubmissionCountsByEmail($emailId, \DateTime $fromDate = null)
    {
        //link email to page hit tracking id to form submission tracking id
        $q = $this->_em->getConnection()->createQueryBuilder();
        $q->select('count(distinct(s.tracking_id)) as count, e.id, e.subject as name, e.variant_sent_count as total')
            ->from(MAUTIC_TABLE_PREFIX.'form_submissions', 's')
            ->join('s', MAUTIC_TABLE_PREFIX.'page_hits', 'h', 's.tracking_id = h.tracking_id')
            ->join('h', MAUTIC_TABLE_PREFIX.'emails', 'e', 'h.email_id = e.id');

        if (is_array($emailId)) {
            $q->where($q->expr()->in('e.id', $emailId))
                ->groupBy('e.id, e.subject, e.variant_sent_count');

        } else {
            $q->where($q->expr()->eq('e.id', ':id'))
                ->setParameter('id', (int) $emailId);
        }

        if ($fromDate != null) {
            $dh = new DateTimeHelper($fromDate);
            $q->andWhere($q->expr()->gte('s.date_submitted', ':date'))
                ->setParameter('date', $dh->toUtcString());
        }

        $results = $q->execute()->fetchAll();

        return $results;
    }

    /**
     * Updates lead ID (e.g. after a lead merge)
     *
     * @param $fromLeadId
     * @param $toLeadId
     */
    public function updateLead($fromLeadId, $toLeadId)
    {
        $q = $this->_em->getConnection()->createQueryBuilder();
        $q->update(MAUTIC_TABLE_PREFIX . 'form_submissions')
            ->set('lead_id', (int) $toLeadId)
            ->where('lead_id = ' . (int) $fromLeadId)
            ->execute();
    }

    /**
     * Validates that an array of submission IDs belong to a specific form
     *
     * @param $ids
     * @param $formId
     *
     * @return array
     */
    public function validateSubmissions($ids, $formId)
    {
        $q = $this->_em->getConnection()->createQueryBuilder();
        $q->select('s.id')
            ->from(MAUTIC_TABLE_PREFIX.'form_submissions', 's')
            ->where(
                $q->expr()->andX(
                    $q->expr()->eq('s.form_id', (int) $formId),
                    $q->expr()->in('s.id', $ids)
                )
            );

        $validIds = array();
        $results  = $q->execute()->fetchAll();

        foreach ($results as $r) {
            $validIds[] = $r['id'];
        }

        return $validIds;
    }

    /**
     * Compare a form result value with defined value for defined lead.
     *
     * @param  integer $lead ID
     * @param  integer $form ID
     * @param  string  $formAlias
     * @param  integer $field alias
     * @param  string  $value to compare with
     * @param  string  $operatorExpr for WHERE clause
     *
     * @return boolean
     */
    public function compareValue($lead, $form, $formAlias, $field, $value, $operatorExpr)
    {
        $tableName = MAUTIC_TABLE_PREFIX . 'form_results_' . $form . '_' . $formAlias;

        //use DBAL to get entity fields
        $q = $this->_em->getConnection()->createQueryBuilder();
        $q->select('s.id')
            ->from($tableName, 'r')
            ->leftJoin('r', MAUTIC_TABLE_PREFIX . 'form_submissions', 's', 's.id = r.submission_id')
            ->where(
                $q->expr()->andX(
                    $q->expr()->eq('s.lead_id', ':lead'),
                    $q->expr()->eq('s.form_id', ':form'),
                    $q->expr()->$operatorExpr('r.' . $field, ':value')
                )
            )
            ->setParameter('lead', (int) $lead)
            ->setParameter('form', (int) $form)
            ->setParameter('value', $value);

        $result = $q->execute()->fetch();

        return !empty($result['id']);
    }
}
