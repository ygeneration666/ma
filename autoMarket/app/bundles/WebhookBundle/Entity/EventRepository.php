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

class EventRepository extends CommonRepository
{
    /**
     * Get a list of events with the webhook
     *
     * @param array $args
     *
     * @return Paginator
     */
    public function getEntitiesByEventType($type)
    {
        $alias = $this->getTableAlias();
        $q = $this->createQueryBuilder($alias)
            ->leftJoin($alias.'.webhook', 'u');

        $q->where(
            $q->expr()->eq($alias . '.event_type', ':type')
        )->setParameter('type', $type);

        // only find published webhooks
        $q->andWhere($q->expr()->eq('u.isPublished', ':published'))
            ->setParameter('published', 1);

        return $q->getQuery()->getResult();
    }
}