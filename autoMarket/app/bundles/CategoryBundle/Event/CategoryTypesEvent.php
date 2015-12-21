<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\CategoryBundle\Event;

use Mautic\CoreBundle\Event\CommonEvent;
use Mautic\CategoryBundle\Entity\Category;

/**
 * Class CategoryTypesEvent
 *
 * @package Mautic\CategoryBundle\Event
 */
class CategoryTypesEvent extends CommonEvent
{
    /**
     * @var array $types
     */
    protected $types = array();

    /**
     * Returns the array of Category Types
     *
     * @return array
     */
    public function getCategoryTypes()
    {
        asort($this->types);
        return $this->types;
    }

    /**
     * Adds the category type and label
     *
     * @param string $type
     * @param string $label
     *
     * @return void
     */
    public function addCategoryType($type, $label = null)
    {
        if ($label === null) {
            $label = 'mautic.' . $type . '.' . $type;
        }

        $this->types[$type] = $label;
    }
}
