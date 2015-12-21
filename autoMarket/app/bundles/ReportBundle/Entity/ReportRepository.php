<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\ReportBundle\Entity;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Mautic\CoreBundle\Entity\CommonRepository;

/**
 * ReportRepository
 */
class ReportRepository extends CommonRepository
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
            ->createQueryBuilder('r')
            ->select('r');

        $args['qb'] = $q;

        return parent::getEntities($args);
    }

    /**
     * {@inheritdoc}
     */
    protected function addCatchAllWhereClause(&$q, $filter)
    {
        $unique  = $this->generateRandomParameterName(); //ensure that the string has a unique parameter identifier
        $string  = ($filter->strict) ? $filter->string : "%{$filter->string}%";

        $expr = $q->expr()->orX(
            $q->expr()->like('r.name',  ":$unique")
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
                $expr            = $q->expr()->eq("r.isPublished", ":$unique");
                $forceParameters = array($unique => true);

                break;
            case $this->translator->trans('mautic.core.searchcommand.isunpublished'):
                $expr            = $q->expr()->eq("r.isPublished", ":$unique");
                $forceParameters = array($unique => false);

                break;
            case $this->translator->trans('mautic.core.searchcommand.ismine'):
                $expr            = $q->expr()->eq("IDENTITY(r.createdBy)", $this->currentUser->getId());
                $returnParameter = false;
                break;
        }

        if ($expr && $filter->not) {
            $expr = $q->expr()->not($expr);
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
            'mautic.core.searchcommand.ispublished',
            'mautic.core.searchcommand.isunpublished',
            'mautic.core.searchcommand.ismine'
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
