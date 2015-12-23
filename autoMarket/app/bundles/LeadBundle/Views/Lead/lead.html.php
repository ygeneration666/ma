<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

/** @var \Mautic\LeadBundle\Entity\Lead $lead */
/** @var array $fields */
$view->extend('MauticCoreBundle:Default:content.html.php');

$isAnonymous = $lead->isAnonymous();

$flag = (!empty($fields['core']['country'])) ? $view['assets']->getCountryFlag($fields['core']['country']['value']) : '';

$leadName       = ($isAnonymous) ? $view['translator']->trans($lead->getPrimaryIdentifier()) : $lead->getPrimaryIdentifier();
$leadActualName = $lead->getName();
$leadCompany    = $lead->getCompany();

$view['slots']->set('mauticContent', 'lead');

$avatar = '';
if (!$isAnonymous) {
    $img    = $view['lead_avatar']->getAvatar($lead);
    $avatar = '<span class="pull-left img-wrapper img-rounded mr-10" style="width:33px"><img src="' . $img . '" alt="" /></span>';
}

$view['slots']->set('headerTitle',
       $avatar . '<div class="pull-left mt-5"><span class="span-block">' . $leadName . '</span><span class="span-block small ml-sm">' . $lead->getSecondaryIdentifier() . '</span></div>');

$groups = array_keys($fields);
$edit   = $security->hasEntityAccess($permissions['lead:leads:editown'], $permissions['lead:leads:editother'], $lead->getOwner());

$buttons = array();

if ($edit) {
    $buttons[] = array(
        'attr' => array(
            'href' => $view['router']->generate( 'mautic_lead_action', array('objectId' => $lead->getId(), 'objectAction' => 'edit'))
        ),
        'btnText'   => $view['translator']->trans('mautic.core.form.edit'),
        'iconClass' => 'fa fa-pencil-square-o'
    );

    $buttons[] = array(
        'attr'      => array(
            'id'          => 'addNoteButton',
            'data-toggle' => 'ajaxmodal',
            'data-target' => '#MauticSharedModal',
            'data-header' => $view['translator']->trans('mautic.lead.note.header.new'),
            'href'        => $view['router']->generate('mautic_leadnote_action', array('leadId' => $lead->getId(), 'objectAction' => 'new', 'leadId' => $lead->getId()))
        ),
        'btnText'   => $view['translator']->trans('mautic.lead.add.note'),
        'iconClass' => 'fa fa-file-o'
    );
}

if (!empty($fields['core']['email']['value'])) {
    $buttons[] = array(
        'attr'      => array(
            'id'          => 'sendEmailButton',
            'data-toggle' => 'ajaxmodal',
            'data-target' => '#MauticSharedModal',
            'data-header' => $view['translator']->trans('mautic.lead.email.send_email.header', array('%email%' => $fields['core']['email']['value'])),
            'href'        => $view['router']->generate('mautic_lead_action', array('objectId' => $lead->getId(), 'objectAction' => 'email'))
        ),
        'btnText'   => $view['translator']->trans('mautic.lead.email.send_email'),
        'iconClass' => 'fa fa-send'
    );
}

$buttons[] = array(
    'attr' => array(
        'data-toggle' => 'ajaxmodal',
        'data-target' => '#MauticSharedModal',
        'data-header' => $view['translator']->trans('mautic.lead.lead.header.lists', array('%name%' => $lead->getPrimaryIdentifier())),
        'data-footer' => 'false',
        'href' => $view['router']->generate( 'mautic_lead_action', array("objectId" => $lead->getId(), "objectAction" => "list")),
    ),
    'btnText'   => $view['translator']->trans('mautic.lead.lead.lists'),
    'iconClass' => 'fa fa-list'
);

if ($security->isGranted('campaign:campaigns:edit')) {
    $buttons[] = array(
        'attr'      => array(
            'data-toggle' => 'ajaxmodal',
            'data-target' => '#MauticSharedModal',
            'data-header' => $view['translator']->trans('mautic.lead.lead.header.campaigns', array('%name%' => $lead->getPrimaryIdentifier())),
            'data-footer' => 'false',
            'href'        => $view['router']->generate('mautic_lead_action', array("objectId" => $lead->getId(), "objectAction" => "campaign"))
        ),
        'btnText'   => $view['translator']->trans('mautic.campaign.campaigns'),
        'iconClass' => 'fa fa-clock-o'
    );
}

if ($security->hasEntityAccess($permissions['lead:leads:deleteown'], $permissions['lead:leads:deleteother'], $lead->getOwner())) {
    $buttons[] = array(
        'confirm'      => array(
            'message'       => $view["translator"]->trans('mautic.lead.lead.form.confirmdelete', array('%name%' => $lead->getName() . ' (' . $lead->getId() . ')')),
            'confirmAction' => $view['router']->generate('mautic_lead_action', array_merge(array('objectAction' => 'delete', 'objectId' => $lead->getId()))),
            'template'      => 'delete'
        )
    );
}

if (($security->hasEntityAccess($permissions['lead:leads:deleteown'], $permissions['lead:leads:deleteother'], $lead->getOwner())) && $edit) {

    $buttons[] = array(
        'attr' => array(
            'data-toggle' => 'ajaxmodal',
            'data-target' => '#MauticSharedModal',
            'data-header' => $view['translator']->trans('mautic.lead.lead.header.merge', array('%name%' => $lead->getPrimaryIdentifier())),
            'href' => $view['router']->generate( 'mautic_lead_action', array("objectId" => $lead->getId(), "objectAction" => "merge"))
        ),
        'btnText'   => $view['translator']->trans('mautic.lead.merge'),
        'iconClass' => 'fa fa-user'
    );
}


$view['slots']->set('actions', $view->render('MauticCoreBundle:Helper:page_actions.html.php', array(
    'item'       => $lead,
    'routeBase'  => 'lead',
    'langVar'    => 'lead.lead',
    'customButtons' => $buttons
)));
?>

<!-- start: box layout -->
<div class="box-layout">
    <!-- left section -->
    <div class="col-md-9 bg-white height-auto">
        <div class="bg-auto">
            <!--/ lead detail header -->

            <!-- lead detail collapseable -->
            <div class="collapse" id="lead-details">
                <ul class="pt-md nav nav-tabs pr-md pl-md" role="tablist">
                <?php $step = 0; ?>
                <?php foreach ($groups as $g): ?>
                    <?php if (!empty($fields[$g])): ?>
                        <li class="<?php if ($step === 0) echo "active"; ?>">
                            <a href="#<?php echo $g; ?>" class="group" data-toggle="tab">
                                <?php echo $view['translator']->trans('mautic.lead.field.group.' . $g); ?>
                            </a>
                        </li>
                        <?php $step++; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
                </ul>

                <!-- start: tab-content -->
                <div class="tab-content pa-md bg-white">
                    <?php $i = 0; ?>
                    <?php foreach ($groups as $group): ?>
                        <div class="tab-pane fade <?php echo $i == 0 ? 'in active' : ''; ?> bdr-w-0" id="<?php echo $group; ?>">
                            <div class="pr-md pl-md pb-md">
                                <div class="panel shd-none mb-0">
                                    <table class="table table-bordered table-striped mb-0">
                                        <tbody>
                                            <?php foreach ($fields[$group] as $field): ?>
                                                <tr>
                                                    <td width="20%"><span class="fw-b"><?php echo $field['label']; ?></span></td>
                                                    <td>
                                                        <?php if ($group == 'core' && $field['alias'] == 'country' && !empty($flag)): ?>
                                                            <img class="mr-sm" src="<?php echo $flag; ?>" alt="" style="max-height: 24px;" />
                                                            <span class="mt-1"><?php echo $field['value']; ?>
                                                        <?php else: ?>
                                                            <?php echo $field['value']; ?>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php $i++; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <!--/ lead detail collapseable -->
        </div>

        <div class="bg-auto bg-dark-xs">
            <!-- lead detail collapseable toggler -->
            <div class="hr-expand nm">
                <span data-toggle="tooltip" title="<?php echo $view['translator']->trans('mautic.core.details'); ?>">
                    <a href="javascript:void(0)" class="arrow text-muted collapsed" data-toggle="collapse" data-target="#lead-details"><span class="caret"></span> <?php echo $view['translator']->trans('mautic.core.details'); ?></a>
                </span>
            </div>
            <!--/ lead detail collapseable toggler -->

            <?php if (!$isAnonymous): ?>
            <div class="pa-md">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="panel">
                            <div class="panel-body box-layout">
                                <div class="col-xs-4 va-m">
                                    <h5 class="text-white dark-md fw-sb mb-xs">
                                        <?php echo $view['translator']->trans('mautic.lead.field.header.engagements'); ?>
                                    </h5>
                                </div>
                                <div class="col-xs-4 text-center">
                                    <div id="engagement-legend" class="legend-container"></div>
                                </div>
                                <div class="col-xs-4 va-t text-right">
                                    <h3 class="text-white dark-sm"><span class="fa fa-eye"></span></h3>
                                </div>
                            </div>
                            <div class="pt-0 pl-15 pb-10 pr-15">
                                <div>
                                    <canvas class="chart" id="chart-engagement" height="250"></canvas>
                                </div>
                            </div>
                            <div id="chart-engagement-data" class="hide"><?php echo json_encode($engagementData); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <!-- tabs controls -->
            <ul class="nav nav-tabs pr-md pl-md mt-10">
                <li class="active">
                    <a href="#history-container" role="tab" data-toggle="tab">
                        <span class="label label-primary mr-sm" id="HistoryCount">
                            <?php echo count($events); ?>
                        </span>
                        <?php echo $view['translator']->trans('mautic.lead.lead.tab.history'); ?>
                    </a>
                </li>
                <li class="">
                    <a href="#notes-container" role="tab" data-toggle="tab">
                        <span class="label label-primary mr-sm" id="NoteCount">
                            <?php echo $noteCount; ?>
                        </span>
                        <?php echo $view['translator']->trans('mautic.lead.lead.tab.notes'); ?>
                    </a>
                </li>
                <?php if (!$isAnonymous): ?>
                <li class="">
                    <a href="#social-container" role="tab" data-toggle="tab">
                        <span class="label label-primary mr-sm" id="SocialCount">
                            <?php echo count($socialProfiles); ?>
                        </span>
                        <?php echo $view['translator']->trans('mautic.lead.lead.tab.social'); ?>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
            <!--/ tabs controls -->
        </div>

        <!-- start: tab-content -->
        <div class="tab-content pa-md">
            <!-- #history-container -->
            <div class="tab-pane fade in active bdr-w-0" id="history-container">
                <?php echo $view->render('MauticLeadBundle:Lead:historyfilter.html.php', array('eventTypes' => $eventTypes, 'eventFilters' => $eventFilters, 'lead' => $lead, 'icons' => $icons)); ?>
                <div id="timeline-container">
                    <?php echo $view->render('MauticLeadBundle:Lead:history.html.php', array('events' => $events, 'icons' => $icons)); ?>
                </div>
            </div>
            <!--/ #history-container -->

            <!-- #notes-container -->
            <div class="tab-pane fade bdr-w-0" id="notes-container">
                <?php echo $leadNotes; ?>
            </div>
            <!--/ #notes-container -->

            <!-- #social-container -->
            <?php if (!$isAnonymous): ?>
            <div class="tab-pane fade bdr-w-0" id="social-container">
                <?php echo $view->render('MauticLeadBundle:Social:index.html.php', array('socialProfiles' => $socialProfiles, 'lead' => $lead, 'socialProfileUrls' => $socialProfileUrls)); ?>
            </div>
            <?php endif; ?>
            <!--/ #social-container -->
        </div>
        <!--/ end: tab-content -->
    </div>
    <!--/ left section -->

    <!-- right section -->
    <div class="col-md-3 bg-white bdr-l height-auto">
        <!-- form HTML -->
        <div class="panel bg-transparent shd-none bdr-rds-0 bdr-w-0 mb-0">
            <?php if (!$lead->isAnonymous()): ?>
            <div class="lead-avatar-panel">
                <div class="avatar-collapser hr-expand nm">
                    <a href="javascript:void(0)" class="arrow text-muted text-center<?php echo ($avatarPanelState == 'expanded') ? '' : ' collapsed'; ?>" data-toggle="collapse" data-target="#lead-avatar-block"><span class="caret"></span></a>
                </div>
                <div class="collapse<?php echo ($avatarPanelState == 'expanded') ? ' in' : ''; ?>" id="lead-avatar-block">
                    <img class="img-responsive" src="<?php echo $img; ?>" alt="<?php echo $leadName; ?> "/>
                    <div class="pa-sm">
                        <?php if ($leadActualName && $leadCompany): ?>
                        <h2>
                            <div>
                                <?php echo $leadName; ?>
                            </div>
                            <div class="mt-xs span-block small">
                                <?php echo $leadCompany; ?>
                            </div>
                        <?php elseif ($leadActualName || $leadCompany): ?>
                        <h2>
                            <?php echo ($leadActualName) ? $leadActualName : $leadCompany; ?>
                        </h2>
                        <?php endif; ?>
                    </div>
                    <hr />
                </div>
            </div>

            <?php endif; ?>
            <div class="mt-sm points-panel text-center">
                <?php
                $color = $lead->getColor();
                $style = !empty($color) ? ' style="font-color: ' . $color . ' !important;"' : '';
                ?>
                <h1 <?php echo $style; ?>>
                    <?php echo $view['translator']->transChoice('mautic.lead.points.count', $lead->getPoints(), array('%points%' => $lead->getPoints())); ?>
                </h1>
                <hr />
            </div>
            <?php if ($doNotContact) : ?>
                <div id="bounceLabel<?php echo $doNotContact['id']; ?>">
                    <div class="panel-heading text-center">
                        <h4 class="fw-sb">
                            <?php if ($doNotContact['unsubscribed']): ?>
                                <span class="label label-<?php echo (!empty($doNotContact['manual']) ? 'warning' : 'danger'); ?>" data-toggle="tooltip" title="<?php echo $doNotContact['comments']; ?>">
                                    <?php echo $view['translator']->trans('mautic.lead.do.not.contact'); ?>
                                    <?php if (!empty($doNotContact['manual'])): ?>
                                    <i class="fa fa-times has-click-event" onclick="Mautic.removeBounceStatus(this, <?php echo $doNotContact['id']; ?>);"></i>
                                    <?php endif; ?>
                                </span>
                            <?php elseif ($doNotContact['bounced']): ?>
                            <span class="label label-warning" data-toggle="tooltip" title="<?php echo $doNotContact['comments']; ?>">
                                <?php echo $view['translator']->trans('mautic.lead.do.not.contact_bounced'); ?>
                                <span data-toggle="tooltip" data-placement="bottom" title="<?php echo $view['translator']->trans('mautic.lead.remove_bounce_status'); ?>"><i class="fa fa-times has-click-event" onclick="Mautic.removeBounceStatus(this, <?php echo $doNotContact['id']; ?>);"></i></span>
                            </span>
                            <?php endif; ?>
                        </h4>
                    </div>
                    <hr />
                </div>
            <?php endif; ?>
            <div class="panel-heading">
                <div class="panel-title">
                    <?php echo $view['translator']->trans('mautic.lead.field.header.contact'); ?>
                </div>
            </div>
            <div class="panel-body pt-sm">
                <h6 class="fw-sb">
                    <?php echo $view['translator']->trans('mautic.lead.field.address'); ?>
                </h6>
                <address class="text-muted">
                    <?php echo $fields['core']['address1']['value']; ?><br>
                    <?php if (!empty($fields['core']['address2']['value'])) : echo $fields['core']['address2']['value'] . '<br>'; endif ?>
                    <?php echo $lead->getLocation(); ?> <?php echo $fields['core']['zipcode']['value']; ?><br>
                    <abbr title="Phone">P:</abbr> <?php echo $fields['core']['phone']['value']; ?>
                </address>

                <h6 class="fw-sb"><?php echo $view['translator']->trans('mautic.core.type.email'); ?></h6>
                <p class="text-muted"><?php echo $fields['core']['email']['value']; ?></p>

                <h6 class="fw-sb"><?php echo $view['translator']->trans('mautic.lead.field.type.tel.home'); ?></h6>
                <p class="text-muted"><?php echo $fields['core']['phone']['value']; ?></p>

                <h6 class="fw-sb"><?php echo $view['translator']->trans('mautic.lead.field.type.tel.mobile'); ?></h6>
                <p class="text-muted mb-0"><?php echo $fields['core']['mobile']['value']; ?></p>
            </div>
        </div>
        <!--/ form HTML -->

        <?php if ($upcomingEvents) : ?>
        <hr class="hr-w-2" style="width:50%">

        <div class="panel bg-transparent shd-none bdr-rds-0 bdr-w-0">
            <div class="panel-heading">
                <div class="panel-title"><?php echo $view['translator']->trans('mautic.lead.lead.upcoming.events'); ?></div>
            </div>
            <div class="panel-body pt-sm">
                <ul class="media-list media-list-feed">
                    <?php foreach ($upcomingEvents as $event) : ?>
                    <li class="media">
                        <div class="media-object pull-left mt-xs">
                            <span class="figure"></span>
                        </div>
                        <div class="media-body">
                            <?php $link = '<a href="' . $view['router']->generate('mautic_campaign_action', array("objectAction" => "view", "objectId" => $event['campaign_id'])) . '" data-toggle="ajax">' . $event['campaign_name'] . '</a>'; ?>
                            <?php echo $view['translator']->trans('mautic.lead.lead.upcoming.event.triggered.at', array('%event%' => $event['event_name'], '%link%' => $link)); ?>
                            <p class="fs-12 dark-sm"><?php echo $view['date']->toFull($event['triggerDate']); ?></p>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>
        <div class="pa-sm">
            <?php $tags = $lead->getTags(); ?>
            <?php foreach ($tags as $tag): ?>
            <h5 class="pull-left mt-xs mr-xs"><span class="label label-success"><?php echo $tag->getTag(); ?></span></h5>
            <?php endforeach; ?>
            <div class="clearfix"></div>
        </div>
    </div>
    <!--/ right section -->
</div>
<!--/ end: box layout -->
