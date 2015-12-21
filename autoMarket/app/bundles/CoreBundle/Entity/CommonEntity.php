<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\CoreBundle\Entity;


class CommonEntity
{
    /**
     * Wrapper function for isProperty methods
     *
     * @param string $name
     * @param        $arguments
     *
     * @throws \InvalidArgumentException
     */
    public function __call($name, $arguments)
    {
        if (strpos($name, 'is') === 0 && method_exists($this, 'get' . ucfirst($name))) {
            return $this->{'get' . ucfirst($name)}();
        } elseif ($name == 'getName' && method_exists($this, 'getTitle')) {
            return $this->getTitle();
        }

        throw new \InvalidArgumentException('Method ' . $name . ' not exists');
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $string = get_called_class();
        if (method_exists($this, 'getId')) {
            $string .= " with ID #" . $this->getId();
        }

        return $string;
    }
}