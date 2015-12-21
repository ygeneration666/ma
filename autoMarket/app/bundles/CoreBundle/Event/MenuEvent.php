<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\CoreBundle\Event;

use Mautic\CoreBundle\Menu\MenuHelper;
use Mautic\CoreBundle\Security\Permissions\CorePermissions;
use Mautic\UserBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Class MenuEvent
 */
class MenuEvent extends Event
{

    /**
     * @var array
     */
    protected $menuItems = array('children' => array());

    /**
     * @var
     */
    protected $type;

    /**
     * @param CorePermissions $security
     */
    public function __construct(MenuHelper $menuHelper, $type = 'main')
    {
        $this->helper = $menuHelper;
        $this->type   = $type;
    }

    /**
     * @param array $menuItems
     */
    public function setMenuItems(array $menuItems)
    {
        $this->menuItems = $menuItems;
    }

    /**
     * Add items to the menu
     *
     * @param array $items
     *
     * @return void
     */
    public function addMenuItems(array $items)
    {
        $isRoot = isset($items['name']) && ($items['name'] == 'root' || $items['name'] == 'admin');
        if (!$isRoot) {
            $this->helper->createMenuStructure($items);
        }

        if ($isRoot) {
            //make sure the root does not override the children
            if (isset($this->menuItems['children'])) {
                if (isset($items['children'])) {
                    $items['children'] = array_merge_recursive($this->menuItems['children'], $items['children']);
                } else {
                    $items['children'] = $this->menuItems['children'];
                }
            }
            $this->menuItems = $items;
        } else {
            $this->menuItems['children'] = array_merge_recursive($this->menuItems['children'], $items);
        }
    }

    /**
     * Return the menu items
     *
     * @return array
     */
    public function getMenuItems()
    {
        return $this->menuItems;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
