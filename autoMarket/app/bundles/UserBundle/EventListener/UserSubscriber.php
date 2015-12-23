<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\UserBundle\EventListener;

use Mautic\ApiBundle\Event\RouteEvent;
use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\CoreBundle\Event as MauticEvents;
use Mautic\UserBundle\Event as Events;
use Mautic\UserBundle\UserEvents;

/**
 * Class UserSubscriber
 */
class UserSubscriber extends CommonSubscriber
{

    /**
     * @return array
     */
    static public function getSubscribedEvents()
    {
        return array(
            UserEvents::USER_POST_SAVE      => array('onUserPostSave', 0),
            UserEvents::USER_POST_DELETE    => array('onUserDelete', 0),
            UserEvents::ROLE_POST_SAVE      => array('onRolePostSave', 0),
            UserEvents::ROLE_POST_DELETE    => array('onRoleDelete', 0)
        );
    }

    /**
     * Add a user entry to the audit log
     *
     * @param Events\UserEvent $event
     */
    public function onUserPostSave(Events\UserEvent $event)
    {
        $user = $event->getUser();

        if ($details = $event->getChanges()) {
            $log        = array(
                "bundle"    => "user",
                "object"    => "user",
                "objectId"  => $user->getId(),
                "action"    => ($event->isNew()) ? "create" : "update",
                "details"   => $details,
                "ipAddress" => $this->factory->getIpAddressFromRequest()
            );
            $this->factory->getModel('core.auditLog')->writeToLog($log);
        }
    }

    /**
     * Add a user delete entry to the audit log
     *
     * @param Events\UserEvent $event
     */
    public function onUserDelete(Events\UserEvent $event)
    {
        $user = $event->getUser();
        $log = array(
            "bundle"     => "user",
            "object"     => "user",
            "objectId"   => $user->deletedId,
            "action"     => "delete",
            "details"    => array('name' => $user->getName()),
            "ipAddress"  => $this->factory->getIpAddressFromRequest()
        );
        $this->factory->getModel('core.auditLog')->writeToLog($log);
    }

    /**
     * Add a role entry to the audit log
     *
     * @param Events\RoleEvent $event
     */
    public function onRolePostSave(Events\RoleEvent $event)
    {
        $role = $event->getRole();
        if ($details = $event->getChanges()) {
            $log        = array(
                "bundle"    => "user",
                "object"    => "role",
                "objectId"  => $role->getId(),
                "action"    => ($event->isNew()) ? "create" : "update",
                "details"   => $details,
                "ipAddress" => $this->factory->getIpAddressFromRequest()
            );
            $this->factory->getModel('core.auditLog')->writeToLog($log);
        }
    }

    /**
     * Add a role delete entry to the audit log
     *
     * @param Events\RoleEvent $event
     */
    public function onRoleDelete(Events\RoleEvent $event)
    {
        $role = $event->getRole();
        $log = array(
            "bundle"     => "user",
            "object"     => "role",
            "objectId"   => $role->deletedId,
            "action"     => "delete",
            "details"    => array('name' => $role->getName()),
            "ipAddress"  => $this->factory->getIpAddressFromRequest()
        );
        $this->factory->getModel('core.auditLog')->writeToLog($log);
    }
}
