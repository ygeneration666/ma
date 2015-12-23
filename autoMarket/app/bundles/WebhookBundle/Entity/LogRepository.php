<?php
/**
 * @package     Allyde mPower Social Bundle
 * @copyright   Allyde, Inc. All rights reserved
 * @author      Allyde, Inc
 * @link        http://allyde.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\WebhookBundle\Entity;

use Mautic\CoreBundle\Entity\CommonRepository;

class LogRepository extends CommonRepository
{
    /*
     * Retains a rolling number of log records for a webhook id
     *
     * @param $id int (for Webhooks)
     *
     * @return int
     */
    public function removeOldLogs($webhook_id)
    {
        // if no idea was sent (the hook was deleted) then return a count of 0
        if (! $webhook_id) {
            return false;
        }

        $qb = $this->_em->getConnection()->createQueryBuilder();

        $count = $qb->select('count(id) as log_count')
            ->from(MAUTIC_TABLE_PREFIX . 'webhook_logs', $this->getTableAlias())
            ->where('webhook_id = ' . $webhook_id)
            ->execute()->fetch();

        $totalCount = $count['log_count'];

        if ($totalCount >= $this->factory->getParameter('webhook_log_max', 10)) {

            $qb = $this->_em->getConnection()->createQueryBuilder();

            $id = $qb->select('id')
                ->from(MAUTIC_TABLE_PREFIX . 'webhook_logs', $this->getTableAlias())
                ->where('webhook_id = ' . $webhook_id)
                ->orderBy('date_added', 'ASC')->setMaxResults(1)
                ->execute()->fetch();

            $qb = $this->_em->getConnection()->createQueryBuilder();

            $qb->delete(MAUTIC_TABLE_PREFIX . 'webhook_logs')
                ->where($qb->expr()->in('id', $id))
                ->execute();
        }
    }
}