<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
if ($tmpl == 'index')
    $view->extend('MauticCampaignBundle:Campaign:index.html.php');
?>
<?php if (count($items)): ?>
<div class="table-responsive">
    <table class="table table-hover table-striped table-bordered campaign-list" id="campaignTable">
        <thead>
            <tr>
                <?php
                echo $view->render('MauticCoreBundle:Helper:tableheader.html.php', array(
                    'checkall' => 'true',
                    'target'   => '#campaignTable'
                ));

                echo $view->render('MauticCoreBundle:Helper:tableheader.html.php', array(
                    'sessionVar' => 'campaign',
                    'orderBy'    => 'c.name',
                    'text'       => 'mautic.core.name',
                    'class'      => 'col-campaign-name',
                    'default'    => true
                ));

                echo $view->render('MauticCoreBundle:Helper:tableheader.html.php', array(
                    'sessionVar' => 'campaign',
                    'orderBy'    => 'cat.title',
                    'text'       => 'mautic.core.category',
                    'class'      => 'visible-md visible-lg col-campaign-category'
                ));

                echo $view->render('MauticCoreBundle:Helper:tableheader.html.php', array(
                    'sessionVar' => 'campaign',
                    'orderBy'    => 'c.id',
                    'text'       => 'mautic.core.id',
                    'class'      => 'visible-md visible-lg col-campaign-id'
                ));
                ?>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td>
                    <?php
                    echo $view->render('MauticCoreBundle:Helper:list_actions.html.php', array(
                        'item'      => $item,
                        'templateButtons' => array(
                            'edit'      => $permissions['campaign:campaigns:edit'],
                            'clone'     => $permissions['campaign:campaigns:create'],
                            'delete'    => $permissions['campaign:campaigns:delete'],
                        ),
                        'routeBase' => 'campaign'
                    ));
                    ?>
                </td>
                <td>
                    <div>
                        <?php echo $view->render('MauticCoreBundle:Helper:publishstatus_icon.html.php',array(
                            'item'       => $item,
                            'model'      => 'campaign'
                        )); ?>
                        <a href="<?php echo $view['router']->generate('mautic_campaign_action', array("objectAction" => "view", "objectId" => $item->getId())); ?>" data-toggle="ajax">
                            <?php echo $item->getName(); ?>
                        </a>
                    </div>
                    <?php if ($description = $item->getDescription()): ?>
                        <div class="text-muted mt-4"><small><?php echo $description; ?></small></div>
                    <?php endif; ?>
                </td>
                <td class="visible-md visible-lg">
                    <?php $category = $item->getCategory(); ?>
                    <?php $catName  = ($category) ? $category->getTitle() : $view['translator']->trans('mautic.core.form.uncategorized'); ?>
                    <?php $color    = ($category) ? '#' . $category->getColor() : 'inherit'; ?>
                    <span style="white-space: nowrap;"><span class="label label-default pa-4" style="border: 1px solid #d5d5d5; background: <?php echo $color; ?>;"> </span> <span><?php echo $catName; ?></span></span>
                </td>
                <td class="visible-md visible-lg"><?php echo $item->getId(); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div class="panel-footer">
    <?php echo $view->render('MauticCoreBundle:Helper:pagination.html.php', array(
        "totalItems"      => count($items),
        "page"            => $page,
        "limit"           => $limit,
        "menuLinkId"      => 'mautic_campaign_index',
        "baseUrl"         => $view['router']->generate('mautic_campaign_index'),
        'sessionVar'      => 'campaign'
    )); ?>
</div>
 <?php else: ?>
    <?php echo $view->render('MauticCoreBundle:Helper:noresults.html.php', array('tip' => 'mautic.campaign.noresults.tip')); ?>
<?php endif; ?>
