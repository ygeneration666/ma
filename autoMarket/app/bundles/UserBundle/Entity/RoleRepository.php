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

/**
 * RoleRepository
 */
class RoleRepository extends CommonRepository
{

    /**
     * Get a list of roles
     *
     * @param array $args
     *
     * @return Paginator
     */
    public function getEntities($args = array())
    {
        $q = $this->createQueryBuilder('r');

        $args['qb'] = $q;

        return parent::getEntities($args);
    }

    /**
     * Get a list of roles
     *
     * @param string $search
     * @param int    $limit
     * @param int    $start
     *
     * @return array
     */
    public function getRoleList($search = '', $limit = 10, $start = 0)
    {
        $q = $this->_em->createQueryBuilder();

        $q->select('partial r.{id, name}')
            ->from('MauticUserBundle:Role', 'r');

        if (!empty($search)) {
            $q->where('r.name LIKE :search')
                ->setParameter('search', "{$search}%");
        }

        $q->orderBy('r.name');

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
            $q->expr()->like('r.name',  ':'.$unique),
            $q->expr()->like('r.description', ':'.$unique)
        );

        if ($filter->not) {
            $q->expr()->not($expr);
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
            case $this->translator->trans('mautic.user.user.searchcommand.isadmin');
                $expr = $q->expr()->eq("r.isAdmin", 1);
                $returnParameter = false;
                break;
            case $this->translator->trans('mautic.core.searchcommand.name'):
                $expr = $q->expr()->like("r.name", ':'.$unique);
                break;
        }

        $string  = ($filter->strict) ? $filter->string : "%{$filter->string}%";
        if ($filter->not) {
            $expr = $q->expr()->not($expr);
        }
        return array(
            $expr,
            ($returnParameter) ? array("$unique" => $string) : array()
        );

    }

    /**
     * {@inheritdoc}
     */
    public function getSearchCommands()
    {
        return array(
            'mautic.user.user.searchcommand.isadmin',
            'mautic.core.searchcommand.name'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultOrder()
    {
        return array(
            array('r.name', 'ASC')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getTableAlias()
    {
        return 'r';
    }
}
