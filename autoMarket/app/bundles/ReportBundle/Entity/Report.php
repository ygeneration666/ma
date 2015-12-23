<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\ReportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mautic\ApiBundle\Serializer\Driver\ApiMetadataDriver;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Mautic\CoreBundle\Entity\FormEntity;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * Class Report
 *
 * @package Mautic\ReportBundle\Entity
 */
class Report extends FormEntity
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var bool
     */
    private $system = false;

    /**
     * @var string
     */
    private $source;

    /**
     * @var array
     */
    private $columns = array();

    /**
     * @var array
     */
    private $filters = array();

    /**
     * @var array
     */
    private $tableOrder = array();

    /**
     * @var array
     */
    private $graphs = array();

    public function __clone()
    {
        $this->id = null;

        parent::__clone();
    }

    /**
     * @param ORM\ClassMetadata $metadata
     */
    public static function loadMetadata (ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable('reports')
            ->setCustomRepositoryClass('Mautic\ReportBundle\Entity\ReportRepository');

        $builder->addIdColumns();

        $builder->addField('system', 'boolean');

        $builder->addField('source', 'string');

        $builder->createField('columns', 'array')
            ->nullable()
            ->build();

        $builder->createField('filters', 'array')
            ->nullable()
            ->build();

        $builder->createField('tableOrder', 'array')
            ->columnName('table_order')
            ->nullable()
            ->build();

        $builder->createField('graphs', 'array')
            ->nullable()
            ->build();


    }

    /**
     * @param ClassMetadata $metadata
     */
    public static function loadValidatorMetadata (ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('name', new NotBlank(array(
            'message' => 'mautic.core.name.required'
        )));
    }

    /**
     * Prepares the metadata for API usage
     *
     * @param $metadata
     */
    public static function loadApiMetadata(ApiMetadataDriver $metadata)
    {
        $metadata->setGroupPrefix('report')
            ->addListProperties(
                'id',
                'name',
                'description',
                'system'
            )
            ->addProperties(
                array(
                    'source',
                    'columns',
                    'filters',
                    'tableOrder',
                    'graphs'
                )
            )
            ->build();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId ()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Report
     */
    public function setName ($name)
    {
        $this->isChanged('name', $name);
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName ()
    {
        return $this->name;
    }

    /**
     * Set system
     *
     * @param string $system
     *
     * @return Report
     */
    public function setSystem ($system)
    {
        $this->isChanged('system', $system);
        $this->system = $system;

        return $this;
    }

    /**
     * Get system
     *
     * @return integer
     */
    public function getSystem ()
    {
        return $this->system;
    }

    /**
     * Set source
     *
     * @param string $source
     *
     * @return Report
     */
    public function setSource ($source)
    {
        $this->isChanged('source', $source);
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return string
     */
    public function getSource ()
    {
        return $this->source;
    }

    /**
     * Set columns
     *
     * @param string $columns
     *
     * @return Report
     */
    public function setColumns ($columns)
    {
        $this->isChanged('columns', $columns);
        $this->columns = $columns;

        return $this;
    }

    /**
     * Get columns
     *
     * @return string
     */
    public function getColumns ()
    {
        return $this->columns;
    }

    /**
     * Set filters
     *
     * @param string $filters
     *
     * @return Report
     */
    public function setFilters ($filters)
    {
        $this->isChanged('filters', $filters);
        $this->filters = $filters;

        return $this;
    }

    /**
     * Get filters
     *
     * @return string
     */
    public function getFilters ()
    {
        return $this->filters;
    }

    /**
     * @return mixed
     */
    public function getDescription ()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription ($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getTableOrder ()
    {
        return $this->tableOrder;
    }

    /**
     * @param array $tableOrder
     */
    public function setTableOrder (array $tableOrder)
    {
        $this->tableOrder = $tableOrder;
    }

    /**
     * @return mixed
     */
    public function getGraphs ()
    {
        return $this->graphs;
    }

    /**
     * @param array $graphs
     */
    public function setGraphs (array $graphs)
    {
        $this->graphs = $graphs;
    }
}