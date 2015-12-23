<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\CoreBundle\Menu;

use Mautic\CoreBundle\Factory\MauticFactory;
use Mautic\CoreBundle\Security\Permissions\CorePermissions;
use Mautic\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Templating\Helper\Helper;
use Knp\Menu\Matcher\MatcherInterface;
use Knp\Menu\ItemInterface;

/**
 * Class MenuHelper
 */
class MenuHelper extends Helper
{

    /**
     * @var MauticFactory
     */
    private $factory;

    /**
     * @param MauticFactory $factory
     */
    public function __construct(MauticFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @return mixed
     */
    protected function getUser()
    {
        return $this->factory->getUser();
    }

    /**
     * @return mixed
     */
    protected function getSecurity()
    {
        return $this->factory->getSecurity();
    }

    /**
     * @return Request
     */
    protected function getRequest()
    {
       return $this->factory->getRequest();
    }

    /**
     * @param      $name
     */
    protected function getParameter($name)
    {
        return $this->factory->getParameter($name);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'menu_helper';
    }

    /**
     * Parses attributes for the menu view
     *
     * @param $attributes
     * @param $overrides
     *
     * @return string
     */
    public function parseAttributes($attributes, $overrides = array())
    {
        if (!is_array($attributes)) {
            $attributes = array();
        }

        $attributes = array_merge($attributes, $overrides);

        $string = '';
        foreach ($attributes as $name => $value) {
            $name  = trim($name);
            $value = trim($value);
            if ($name == $value) {
                $string .= " $name";
            } else {
                $string .= " $name=\"$value\"";
            }
        }

        return $string;
    }

    /**
     * Concats the appropriate classes for menu links
     *
     * @param ItemInterface    $item
     * @param MatcherInterface $matcher
     * @param array            $options
     */
    public function buildClasses(ItemInterface &$item, MatcherInterface &$matcher, $options)
    {

        $showChildren = ($item->hasChildren() && $item->getDisplayChildren());
        $isAncestor   = $matcher->isAncestor($item, $options["matchingDepth"]);
        $isCurrent    = $matcher->isCurrent($item);

        $class   = $item->getAttribute("class");
        $classes = ($class) ? " {$class}" : "";
        $classes .= ($isCurrent) ? " {$options["currentClass"]}" : "";
        $classes .= ($isAncestor) ? " {$options["ancestorClass"]}" : "";
        $classes .= ($isAncestor && $this->invisibleChildSelected($item, $matcher)) ? " {$options["currentClass"]}" : "";
        $classes .= ($item->actsLikeFirst()) ? " {$options["firstClass"]}" : "";
        $classes .= ($item->actsLikeLast()) ? " {$options["lastClass"]}" : "";
        $item->setAttribute("class", trim($classes));
    }

    /**
     * @param ItemInterface    $menu
     * @param MatcherInterface $matcher
     *
     * @return bool
     */
    public function invisibleChildSelected($menu, MatcherInterface $matcher)
    {
        /** @var ItemInterface $item */
        foreach ($menu as $item) {
            if ($matcher->isCurrent($item)) {
                return ($item->isDisplayed()) ? false : true;
            }
        }

        return false;
    }

    /**
     * Converts menu config into something KNP menus expects
     *
     * @param $items
     */
    public function createMenuStructure(&$items, $depth = 0)
    {
        foreach ($items as $k => &$i) {
            if (!is_array($i) || empty($i)) {
                continue;
            }

            if (isset($i['bundle'])) {
                // Category shortcut
                $bundleName = $i['bundle'];
                $i = array(
                    'access'          => $bundleName . ':categories:view',
                    'route'           => 'mautic_category_index',
                    'id'              => 'mautic_'.$bundleName.'category_index',
                    'routeParameters' => array('bundle' => $bundleName),
                );
            }

            // Check to see if menu is restricted
            if (isset($i['access'])) {
                if ($i['access'] == 'admin') {
                    if (!$this->getUser()->isAdmin()) {
                        unset($items[$k]);
                        continue;
                    }
                } elseif (!$this->getSecurity()->isGranted($i['access'], 'MATCH_ONE')) {
                    unset($items[$k]);
                    continue;
                }
            }

            if (isset($i['checks'])) {
                $passChecks = true;
                foreach ($i['checks'] as $checkGroup => $checks) {
                    foreach ($checks as $name => $value) {
                        if ($checkGroup == 'parameters') {
                            if ($this->getParameter($name) != $value) {
                                $passChecks = false;
                                break;
                            }
                        } elseif ($checkGroup == 'request') {
                            if ($this->getRequest()->get($name) != $value) {
                                $passChecks = false;
                                break;
                            }
                        }
                    }
                }
                if (!$passChecks) {
                    unset($items[$k]);
                    continue;
                }
            }

            //Set ID to route name
            if (!isset($i['id']) && isset($i['route'])) {
                $i['id'] = $i['route'];
            }

            //Set link attributes
            $i['linkAttributes'] = array(
                'data-menu-link' => $i['id'],
                'id'             => $i['id']
            );

            $i['extras'] = array();


            $i['extras']['depth'] = $depth;


            //Set the icon class for the menu item
            if (!empty($i['iconClass'])) {
                $i['extras']['iconClass'] = $i['iconClass'];
            }

            //Set the actual route name so that it's available to the menu template
            if (isset($i['route'])) {
                $i['extras']['routeName'] = $i['route'];
            }

            //Repeat for sub items
            if (isset($i['children'])) {
                $this->createMenuStructure($i['children'], $depth + 1);
            }
        }
    }
}
