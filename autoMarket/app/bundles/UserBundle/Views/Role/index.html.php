<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

$view->extend('MauticCoreBundle:Default:content.html.php');
$view['slots']->set('mauticContent', 'role');
$view['slots']->set('headerTitle', $view['translator']->trans('mautic.user.roles'));

$view['slots']->set('actions', $view->render('MauticCoreBundle:Helper:page_actions.html.php', array(
    'templateButtons' => array(
        'new' => $permissions['create']
    ),
    'routeBase' => 'role',
    'langVar'   => 'user.role'
)));
?>

<?php echo $view->render('MauticCoreBundle:Helper:list_toolbar.html.php', array(
    'searchValue' => $searchValue,
    'searchHelp'  => 'mautic.user.role.help.searchcommands',
    'action'      => $currentRoute,
    'langVar'     => 'user.role',
    'routeBase'   => 'role',
    'templateButtons' => array(
        'delete' => $permissions['delete']
    )
)); ?>

<div class="page-list">
    <?php $view['slots']->output('_content'); ?>
</div>