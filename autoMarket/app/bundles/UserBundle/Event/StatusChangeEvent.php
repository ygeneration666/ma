<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\UserBundle\Event;

use Mautic\CampaignBundle\Entity\Event;
use Mautic\CoreBundle\Factory\MauticFactory;

/**
 * Class StatusChangeEvent
 *
 * @package Mautic\UserBundle\Event
 */
class StatusChangeEvent extends Event
{
    /**
     * @var MauticFactory
     */
    private $factory;

    /**
     * @var \Mautic\UserBundle\Entity\User|null
     */
    private $user;

    /**
     * @param MauticFactory $factory
     */
    public function __construct(MauticFactory $factory)
    {
        $this->factory = $factory;
        $this->user    = $factory->getUser();
    }

    /**
     * @return MauticFactory
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * @return \Mautic\UserBundle\Entity\User|null
     */
    public function getUser()
    {
        return $this->user;
    }
}