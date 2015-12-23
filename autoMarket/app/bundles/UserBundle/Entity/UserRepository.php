<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\UserBundle\Entity;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Mautic\CoreBundle\Entity\CommonRepository;
use Mautic\CoreBundle\Helper\DateTimeHelper;

/**
 * UserRepository
 */
class UserRepository extends CommonRepository
{

    /**
     * Find user by username or email
     *
     * @param $identifier
     *
     * @return array|null
     */
    public function findByIdentifier($identifier)
    {
        $q = $this->createQueryBuilder('u')
            ->where('u.username = :identifier OR u.email = :identifier')
            ->setParameter('identifier', $identifier);

        $result = $q->getQuery()->getResult();

        return ($result != null) ? $result[0] : null;
    }

    /**
     * @param $user
     */
    public function setLastLogin($user)
    {
        $now      = new DateTimeHelper();
        $datetime = $now->toUtcString();
        $conn     = $this->_em->getConnection();
        $conn->update(MAUTIC_TABLE_PREFIX . 'users', array(
            'last_login'  => $datetime,
            'last_active' => $datetime
        ), array('id' => (int) $user->getId()));
    }

    /**
     * @param $user
     */
    public function setLastActive($user)
    {
        $now = new DateTimeHelper();
        $conn = $this->_em->getConnection();
        $conn->update(MAUTIC_TABLE_PREFIX . 'users', array('last_active' => $now->toUtcString()), array('id' => (int) $user->getId()));
    }

    /**
     * @param $userId
     * @param $status
     */
    public function setOnlineStatus($userId, $status)
    {
        $conn = $this->_em->getConnection();
        $conn->update(MAUTIC_TABLE_PREFIX . 'users', array('online_status' => $status), array('id' => (int) $userId));
    }

    /**
     * Last active updates every 2 minutes. If it didn't get updated, it means the user closed their browser and are thus
     * now offline
     *
     */
    public function updateOnlineStatuses()
    {
        $dt    = new DateTimeHelper();
        $offlineDelay = $dt->getUtcDateTime();
        $offlineDelay->setTimestamp(strtotime('15 minutes ago'));

        $q = $this->_em->createQueryBuilder()
            ->update('MauticUserBundle:User', 'u')
            ->set('u.onlineStatus', ':status')
            ->where('u.lastActive <= :delay')
            ->setParameter('delay', $offlineDelay)
            ->setParameter('status', 'offline');
        $q->getQuery()->execute();
    }

    /**
     * Checks to ensure that a username and/or email is unique
     *
     * @param $params
     *
     * @return array
     */
    public function checkUniqueUsernameEmail($params) {
        $q = $this->createQueryBuilder('u');

        if (isset($params['email'])) {
            $q->where('u.username = :email OR u.email = :email')
                ->setParameter("email", $params['email']);
        }

        if (isset($params['username'])) {
            $q->orWhere('u.username = :username OR u.email = :username')
                ->setParameter("username", $params['username']);
        }

        return $q->getQuery()->getResult();
    }

    /**
     * Get a list of users
     *
     * @param array $args
     *
     * @return Paginator
     */
    public function getEntities($args = array())
    {
        $q = $this
            ->createQueryBuilder('u')
            ->select('u, r')
            ->leftJoin('u.role', 'r');

        $args['qb'] = $q;

        return parent::getEntities($args);
    }

    /**
     * Get a list of users for an autocomplete input
     *
     * @param string $search
     * @param int    $limit
     * @param int    $start
     * @param array  $permissionLimiter
     *
     * @return array
     */
    public function getUserList($search = '', $limit = 10, $start = 0, $permissionLimiter = array())
    {
        $q = $this->_em->createQueryBuilder();

        $q->select('partial u.{id, firstName, lastName}')
            ->from('MauticUserBundle:User', 'u')
            ->leftJoin('u.role', 'r')
            ->leftJoin('r.permissions', 'p');

        if (!empty($search)) {
            $q->where(
                $q->expr()->orX(
                    $q->expr()->like('u.firstName', ':search'),
                    $q->expr()->like('u.lastName', ':search'),
                    $q->expr()->like(
                        $q->expr()->concat('u.firstName',
                            $q->expr()->concat(
                                $q->expr()->literal(' '),
                                'u.lastName'
                            )
                        ),
                        ':search'
                    )
                )
            )
            ->setParameter('search', "{$search}%");
        }

        if (!empty($permissionLimiter)) {
            //only get users with a role that has some sort of access to set permissions
            $expr = $q->expr()->andX();
            foreach ($permissionLimiter as $bundle => $level) {
                $expr->add(
                    $q->expr()->andX(
                        $q->expr()->eq('p.bundle', $q->expr()->literal($bundle)),
                        $q->expr()->eq('p.name', $q->expr()->literal($level))
                    )
                );
            }
            $expr = $q->expr()->orX(
                $q->expr()->eq('r.isAdmin', ':true'),
                $expr
            );
            $q->andWhere($expr);
        }

        $q->andWhere('u.isPublished = :true')
            ->setParameter('true', true, 'boolean')
            ->orderBy('u.firstName, u.lastName');

        if (!empty($limit)) {
            $q->setFirstResult($start)
                ->setMaxResults($limit);
        }

        return $q->getQuery()->getArrayResult();
    }

    /**
     * @param string $search
     * @param int    $limit
     * @param int    $start
     *
     * @return array
     */
    public function getPositionList($search = '', $limit = 10, $start = 0)
    {
        $q = $this->_em->createQueryBuilder()
            ->select('u.position')
            ->distinct()
            ->from('MauticUserBundle:User', 'u')
            ->where("u.position != ''")
            ->andWhere("u.position IS NOT NULL");
        if (!empty($search)) {
            $q->andWhere('u.position LIKE :search')
                ->setParameter('search', "{$search}%");
        }

        $q->orderBy('u.position');

        if (!empty($limit)) {
            $q->setFirstResult($start)
                ->setMaxResults($limit);
        }

        return $q->getQuery()->getArrayResult();
    }

    /**
     * {@inheritdoc}
     */
    protected function addCatchAllWhereClause(&$q, $filter)
    {
        $unique  = $this->generateRandomParameterName(); //ensure that the string has a unique parameter identifier
        $string  = ($filter->strict) ? $filter->string : "%{$filter->string}%";

        $expr = $q->expr()->orX(
            $q->expr()->like('u.username',  ':'.$unique),
            $q->expr()->like('u.email',     ':'.$unique),
            $q->expr()->like('u.firstName', ':'.$unique),
            $q->expr()->like('u.lastName',  ':'.$unique),
            $q->expr()->like('u.position',  ':'.$unique),
            $q->expr()->like('r.name',  ':'.$unique)
        );

        if ($filter->not) {
            $expr = $q->expr()->not($expr);
        }
        return array(
            $expr,
            array("$unique" => $string)
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function addSearchCommandWhereClause(&$q, $filter)
    {
        $command         = $filter->command;
        $unique          = $this->generateRandomParameterName();
        $returnParameter = true; //returning a parameter that is not used will lead to a Doctrine error
        $expr            = false;
        switch ($command) {
            case $this->translator->trans('mautic.core.searchcommand.ispublished'):
                $expr            = $q->expr()->eq("u.isPublished", ":$unique");
                $forceParameters = array($unique => true);

                break;
            case $this->translator->trans('mautic.core.searchcommand.isunpublished'):
                $expr            = $q->expr()->eq("u.isPublished", ":$unique");
                $forceParameters = array($unique => false);

                break;
            case $this->translator->trans('mautic.user.user.searchcommand.isadmin');
                $expr            = $q->expr()->eq("r.isAdmin", ":$unique");
                $forceParameters = array($unique => true);
                break;
            case $this->translator->trans('mautic.core.searchcommand.email'):
                $expr = $q->expr()->like("u.email", ':' . $unique);
                break;
            case $this->translator->trans('mautic.user.user.searchcommand.position'):
                $expr = $q->expr()->like("u.position", ':' . $unique);
                break;
            case $this->translator->trans('mautic.user.user.searchcommand.username'):
                $expr = $q->expr()->like("u.username", ':' . $unique);
                break;
            case $this->translator->trans('mautic.user.user.searchcommand.role'):
                $expr = $q->expr()->like("r.name", ':' . $unique);
                break;
            case $this->translator->trans('mautic.core.searchcommand.name'):
                $expr = $q->expr()->orX(
                    $q->expr()->like('u.firstName', ':' . $unique),
                    $q->expr()->like('u.lastName', ':' . $unique)
                );
                break;
        }

        if (!empty($forceParameters)) {
            $parameters = $forceParameters;
        } elseif (!$returnParameter) {
            $parameters = array();
        } else {
            $string     = ($filter->strict) ? $filter->string : "%{$filter->string}%";
            $parameters = array("$unique" => $string);
        }

        return array($expr, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchCommands()
    {
         return array(
            'mautic.core.searchcommand.email',
            'mautic.core.searchcommand.ispublished',
            'mautic.core.searchcommand.isunpublished',
            'mautic.user.user.searchcommand.isadmin',
            'mautic.core.searchcommand.name',
            'mautic.user.user.searchcommand.position',
            'mautic.user.user.searchcommand.role',
            'mautic.user.user.searchcommand.username'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultOrder()
    {
        return array(
            array('u.lastName', 'ASC'),
            array('u.firstName', 'ASC'),
            array('u.username', 'ASC')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getTableAlias()
    {
        return 'u';
    }
}
