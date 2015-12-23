<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\AssetBundle\Entity;

use Mautic\CoreBundle\Entity\CommonRepository;
use Mautic\CoreBundle\Helper\DateTimeHelper;
use Mautic\CoreBundle\Helper\GraphHelper;

/**
 * Class DownloadRepository
 *
 * @package Mautic\AssetBundle\Entity
 */
class DownloadRepository extends CommonRepository
{

    /**
     * Get a count of unique downloads for the current tracking ID
     *
     * @param $assetId
     * @param $trackingId
     *
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getDownloadCountForTrackingId($assetId, $trackingId)
    {
        $count = $this->createQueryBuilder('d')
            ->select('count(d.id) as num')
            ->where('IDENTITY(d.asset) = ' .$assetId)
            ->andWhere('d.trackingId = :id')
            ->setParameter('id', $trackingId)
            ->getQuery()
            ->getSingleResult();

        return (int) $count['num'];
    }

    /**
     * Get a lead's page downloads
     *
     * @param integer $leadId
     * @param array   $options
     *
     * @return array
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getLeadDownloads($leadId, array $options = array())
    {
        $query = $this->createQueryBuilder('d')
            ->select('IDENTITY(d.asset) AS asset_id, d.dateDownload')
            ->where('d.lead = ' . $leadId);

        if (!empty($options['ipIds'])) {
            $query->orWhere('d.ipAddress IN (' . implode(',', $options['ipIds']) . ')');
        }

        if (isset($options['filters']['search']) && $options['filters']['search']) {
            $query->leftJoin('d.asset', 'a')
                ->andWhere($query->expr()->like('a.title', $query->expr()->literal('%' . $options['filters']['search'] . '%')));
        }

        return $query->getQuery()
            ->getArrayResult();
    }

    /**
     * Get hit count per day for last 30 days
     *
     * @param integer $assetId
     * @param integer $amount of units
     * @param char $unit: php.net/manual/en/dateinterval.construct.php#refsect1-dateinterval.construct-parameters
     *
     * @return array
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getDownloads($assetId, $amount = 30, $unit = 'D')
    {
        $data = GraphHelper::prepareDatetimeLineGraphData($amount, $unit, array('downloaded'));

        $query = $this->createQueryBuilder('d');

        $query->select('IDENTITY(d.asset), d.dateDownload')
            ->where($query->expr()->eq('IDENTITY(d.asset)', (int) $assetId))
            ->andwhere($query->expr()->gte('d.dateDownload', ':date'))
            ->setParameter('date', $data['fromDate']);

        $downloads = $query->getQuery()->getArrayResult();

        return GraphHelper::mergeLineGraphData($data, $downloads, $unit, 0, 'dateDownload');
    }

    /**
     * Get list of assets ordered by it's download count
     *
     * @param QueryBuilder $query
     * @param integer $limit
     * @param integer $offset
     *
     * @return array
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getMostDownloaded($query, $limit = 10, $offset = 0)
    {
        $query->select('a.title, a.id, count(ad.id) as downloads')
            ->groupBy('a.id, a.title')
            ->orderBy('downloads', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        $results = $query->execute()->fetchAll();

        return $results;
    }

    /**
     * Get list of asset referrals ordered by it's count
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
        $query->select('ad.referer, count(ad.referer) as downloads')
            ->groupBy('ad.referer')
            ->orderBy('downloads', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        $results = $query->execute()->fetchAll();

        return $results;
    }

    /**
     * Get pie graph data for http statuses
     *
     * @param QueryBuilder $query
     *
     * @return array
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getHttpStatuses($query)
    {
        $query->select('ad.code as status, count(ad.code) as count')
            ->groupBy('ad.code')
            ->orderBy('count', 'DESC');

        $results = $query->execute()->fetchAll();

        $colors = GraphHelper::$colors;
        $graphData = array();
        $i = 0;
        foreach($results as $result) {
            if (!isset($colors[$i])) {
                $i = 0;
            }
            $color = $colors[$i];
            $graphData[] = array(
                'label' => $result['status'],
                'color' => $colors[$i]['color'],
                'highlight' => $colors[$i]['highlight'],
                'value' => (int) $result['count']
            );
            $i++;
        }

        return $graphData;
    }

    /**
     * @param           $pageId
     * @param \DateTime $fromDate
     *
     * @return mixed
     */
    public function getDownloadCountsByPage($pageId, \DateTime $fromDate = null)
    {
        $q = $this->_em->getConnection()->createQueryBuilder();
        $q->select('count(distinct(a.tracking_id)) as count, a.source_id as id, p.title as name, p.hits as total')
            ->from(MAUTIC_TABLE_PREFIX.'asset_downloads', 'a')
            ->join('a', MAUTIC_TABLE_PREFIX.'pages', 'p', 'a.source_id = p.id');

        if (is_array($pageId)) {
            $q->where($q->expr()->in('p.id', $pageId))
                ->groupBy('p.id, a.source_id, p.title, p.hits');

        } else {
            $q->where($q->expr()->eq('p.id', ':page'))
                ->setParameter('page', (int) $pageId);
        }

        $q->andWhere('a.source = "page"')
            ->andWhere('a.code = 200');

        if ($fromDate != null) {
            $dh = new DateTimeHelper($fromDate);
            $q->andWhere($q->expr()->gte('a.date_download', ':date'))
                ->setParameter('date', $dh->toUtcString());
        }

        $results = $q->execute()->fetchAll();

        $downloads = array();
        foreach ($results as $r) {
            $downloads[$r['id']] = $r;
        }

        return $downloads;
    }

    /**
     * Get download count by email by linking emails that have been associated with a page hit that has the
     * same tracking ID as an asset download tracking ID and thus assumed happened in the same session
     *
     * @param           $emailId
     * @param \DateTime $fromDate
     *
     * @return mixed
     */
    public function getDownloadCountsByEmail($emailId, \DateTime $fromDate = null)
    {
        //link email to page hit tracking id to download tracking id
        $q = $this->_em->getConnection()->createQueryBuilder();
        $q->select('count(distinct(a.tracking_id)) as count, e.id, e.subject as name, e.variant_sent_count as total')
            ->from(MAUTIC_TABLE_PREFIX.'asset_downloads', 'a')
            ->join('a', MAUTIC_TABLE_PREFIX.'emails', 'e', 'a.email_id = e.id');

        if (is_array($emailId)) {
            $q->where($q->expr()->in('e.id', $emailId))
                ->groupBy('e.id, e.subject, e.variant_sent_count');
        } else {
            $q->where($q->expr()->eq('e.id', ':email'))
                ->setParameter('email', (int) $emailId);
        }

        $q->andWhere('a.code = 200');

        if ($fromDate != null) {
            $dh = new DateTimeHelper($fromDate);
            $q->andWhere($q->expr()->gte('a.date_download', ':date'))
                ->setParameter('date', $dh->toUtcString());
        }

        $results = $q->execute()->fetchAll();

        $downloads = array();
        foreach ($results as $r) {
            $downloads[$r['id']] = $r;
        }

        return $downloads;
    }

    /**
     * @param $leadId
     * @param $newTrackingId
     * @param $oldTrackingId
     */
    public function updateLeadByTrackingId($leadId, $newTrackingId, $oldTrackingId)
    {
        $q = $this->_em->getConnection()->createQueryBuilder();
        $q->update(MAUTIC_TABLE_PREFIX . 'asset_downloads')
            ->set('lead_id', (int) $leadId)
            ->set('tracking_id', ':newTrackingId')
            ->where(
                $q->expr()->eq('tracking_id', ':oldTrackingId')
            )
            ->setParameters(array(
                'newTrackingId' => $newTrackingId,
                'oldTrackingId' => $oldTrackingId
            ))
            ->execute();
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
        $q->update(MAUTIC_TABLE_PREFIX . 'asset_downloads')
            ->set('lead_id', (int) $toLeadId)
            ->where('lead_id = ' . (int) $fromLeadId)
            ->execute();
    }
}
