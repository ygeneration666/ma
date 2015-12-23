<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\ConfigBundle\Event;

use Mautic\CoreBundle\Event\CommonEvent;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class ConfigEvent
 */
class ConfigEvent extends CommonEvent
{
    /**
     * @var array
     */
    private $preserve = array();

    /**
     * @param array $config
     */
    private $config;

    /**
     * @param \Symfony\Component\HttpFoundation\ParameterBag $post
     */
    private $post;

    /**
     * @param array        $config
     * @param ParameterBag $post
     */
    public function __construct(array $config, ParameterBag $post)
    {
        $this->config = $config;
        $this->post   = $post;
    }

    /**
     * Returns the config array
     *
     * @param string $key
     *
     * @return array
     */
    public function getConfig($key = null)
    {
        if ($key) {
            return (isset($this->config[$key])) ? $this->config[$key] : array();
        }

        return $this->config;
    }

    /**
     * Sets the config array
     *
     * @param array $config
     * @param null  $key
     */
    public function setConfig(array $config, $key = null)
    {
        if ($key) {
            $this->config[$key] = $config;
        } else {
            $this->config = $config;
        }
    }

    /**
     * Returns the POST
     *
     * @return \Symfony\Component\HttpFoundation\ParameterBag
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * Set fields such as passwords that will not overwrite existing values
     * if the current is empty
     *
     * @param array|string $fields
     */
    public function unsetIfEmpty($fields)
    {
        if (!is_array($fields)) {
            $fields = array($fields);
        }

        $this->preserve = array_merge($this->preserve, $fields);
    }

    /**
     * Return array of fields to unset if empty so that existing values are not
     * overwritten if empty
     *
     * @return array
     */
    public function getPreservedFields()
    {
        return $this->preserve;
    }
}
