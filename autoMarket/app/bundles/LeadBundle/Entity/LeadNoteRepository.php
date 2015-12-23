<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\LeadBundle\Entity;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Mautic\CoreBundle\Entity\CommonRepository;
use Doctrine\ORM\Query;

/**
 * LeadNoteRepository
 */
class LeadNoteRepository extends CommonRepository
{

    /**
     * {@inhertidoc}
     *
     * @param array      $args
     * @return Paginator
     */
    public function getEntities($args = array())
    {
        $q = $this
            ->createQueryBuilder('n')
            ->select('n');
        $args['qb'] = $q;

        return parent::getEntities($args);
    }

    /**
     * @param $leadId
     * @param $filter
     * @param $noteTypes
     *
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getNoteCount($leadId, $filter = null, $noteTypes = null)
    {
        $q = $this
            ->createQueryBuilder('n');
        $q->select('count(n.id) as note_count')
            ->where($q->expr()->eq('IDENTITY(n.lead)', ':lead'))
            ->setParameter('lead', $leadId);

        if ($filter != null) {
            $q->andWhere(
                $q->expr()->like('n.text', ":filter")
            )->setParameter('filter', '%'.$filter.'%');
        }

        if ($noteTypes != null) {
            $q->andWhere(
                $q->expr()->in('n.type', ":noteTypes")
            )->setParameter('noteTypes', $noteTypes);
        }

        $results = $q->getQuery()->getArrayResult();
        return $results[0]['note_count'];
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getTableAlias()
    {
        return 'n';
    }


    /**
     * @param QueryBuilder $q
     * @param              $filter
     * @return array
     */
    protected function addCatchAllWhereClause(&$q, $filter)
    {
        $unique  = $this->generateRandomParameterName(); //ensure that the string has a unique parameter identifier
        $string  = ($filter->strict) ? $filter->string : "%{$filter->string}%";

        $expr = $q->expr()->like('n.text', ":$unique");

        if ($filter->not) {
            $q->expr()->not($expr);
        }

        return array(
            $expr,
            array("$unique" => $string)
        );
    }

    /**
     * @param QueryBuilder $q
     * @param              $filter
     * @return array
     */
    protected function addSearchCommandWhereClause(&$q, $filter)
    {
        $command         = $filter->command;
        $string          = $filter->string;
        $unique          = $this->generateRandomParameterName();
        $returnParameter = true; //returning a parameter that is not used will lead to a Doctrine error
        $expr            = false;

        switch ($command) {
            case $this->translator->trans('mautic.lead.note.searchcommand.type'):
                switch($string) {
                    case $this->translator->trans('mautic.lead.note.searchcommand.general'):
                        $filter->string = 'general';
                        break;
                    case $this->translator->trans('mautic.lead.note.searchcommand.call'):
                        $filter->string = 'call';
                        break;
                    case $this->translator->trans('mautic.lead.note.searchcommand.email'):
                        $filter->string = 'email';
                        break;
                    case $this->translator->trans('mautic.lead.note.searchcommand.meeting'):
                        $filter->string = 'meeting';
                        break;
                }
                $expr = $q->expr()->eq('n.type', ":$unique");
                $filter->strict = true;
                break;
        }

        $string  = ($filter->strict) ? $filter->string : "%{$filter->string}%";
        if ($expr && $filter->not) {
            $expr = $q->expr()->not($expr);
        }
        return array(
            $expr,
            ($returnParameter) ? array("$unique" => $string) : array()
        );
    }

    /**
     * @return array
     */
    public function getSearchCommands()
    {
        return array(
            'mautic.lead.note.searchcommand.type' => array(
                'mautic.lead.note.searchcommand.general',
                'mautic.lead.note.searchcommand.call',
                'mautic.lead.note.searchcommand.email',
                'mautic.lead.note.searchcommand.meeting'
            )
        );
    }

    /**
     * Updates lead ID (e.g. after a lead merge)
     *
     * @param $fromLeadId
     * @param $toLeadId
     */
    public function updateLead($fromLeadId, $toLeadId)
    {
        $this->_em->getConnection()->createQueryBuilder()
            ->update(MAUTIC_TABLE_PREFIX . 'lead_notes')
            ->set('lead_id', (int)$toLeadId)
            ->where('lead_id = ' . (int)$fromLeadId)
            ->execute();
    }
}
