<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

$view->extend('MauticCoreBundle:Default:content.html.php');
$view['slots']->set('mauticContent', 'report');
$view['slots']->set("headerTitle", $view['translator']->trans('mautic.report.reports'));

$view['slots']->set('actions', $view->render('MauticCoreBundle:Helper:page_actions.html.php', array(
    'templateButtons' => array(
        'new' => $permissions['report:reports:create']
    ),
    'routeBase' => 'report',
    'langVar'   => 'report.report'
)));
?>

<div class="panel panel-default bdr-t-wdh-0 mb-0">
    <?php echo $view->render('MauticCoreBundle:Helper:list_toolbar.html.php', array(
        'searchValue' => $searchValue,
        'searchHelp'  => 'mautic.report.report.help.searchcommands',
        'action'      => $currentRoute,
        'langVar'     => 'report.report',
        'routeBase'   => 'report',
        'templateButtons' => array(
            'delete' => $permissions['report:reports:deleteown'] || $permissions['report:reports:deleteother']
        )
    )); ?>
    <div class="page-list">
        <?php $view['slots']->output('_content'); ?>
    </div>
</div>
