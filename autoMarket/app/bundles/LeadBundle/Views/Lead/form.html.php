<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
$view->extend('MauticCoreBundle:Default:content.html.php');
$header = ($lead->getId()) ?
    $view['translator']->trans('mautic.lead.lead.header.edit',
        array('%name%' => $view['translator']->trans($lead->getPrimaryIdentifier()))) :
    $view['translator']->trans('mautic.lead.lead.header.new');
$view['slots']->set('headerTitle', $header);
$view['slots']->set('mauticContent', 'lead');

$groups = array_keys($fields);
sort($groups);

$img = $view['lead_avatar']->getAvatar($lead);
?>
<?php echo $view['form']->start($form); ?>
<!-- start: box layout -->
<div class="box-layout">
    <!-- step container -->
    <div class="col-md-3 bg-white height-auto">
        <div class="pr-lg pl-lg pt-md pb-md">
            <div class="media">
                <div class="media-body">
                    <img class="img-rounded img-bordered img-responsive media-object" src="<?php echo $img; ?>" alt="">
                </div>
            </div>

            <div class="row mt-xs">
                <div class="col-sm-12">
                    <?php echo $view['form']->label($form['preferred_profile_image']); ?>
                    <?php echo $view['form']->widget($form['preferred_profile_image']); ?>
                </div>
                <div class="col-sm-12<?php if ($view['form']->containsErrors($form['custom_avatar'])) echo ' has-error'; ?>" id="customAvatarContainer" style="<?php if ($form['preferred_profile_image']->vars['data'] != 'custom') echo 'display: none;'; ?>">
                    <?php echo $view['form']->widget($form['custom_avatar']); ?>
                    <?php echo $view['form']->errors($form['custom_avatar']); ?>
                </div>
            </div>

            <hr />

            <ul class="list-group list-group-tabs">
                <?php $step = 1; ?>
                <?php foreach ($groups as $g): ?>
                    <?php if (!empty($fields[$g])): ?>
                        <li class="list-group-item <?php if ($step === 1) echo "active"; ?>">
                            <a href="#<?php echo $g; ?>" class="steps" data-toggle="tab">
                                <?php echo $view['translator']->trans('mautic.lead.field.group.' . $g); ?>
                            </a>
                        </li>
                        <?php $step++; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <!--/ step container -->

    <!-- container -->
    <div class="col-md-9 bg-auto height-auto bdr-l">
        <div class="tab-content">
            <!-- pane -->
            <?php
            foreach ($groups as $key => $group):
            if (isset($fields[$group])):
                $groupFields = $fields[$group];
                if (!empty($groupFields)): ?>
                <div class="tab-pane fade<?php if ($key === 0) echo ' in active'; ?> bdr-rds-0 bdr-w-0" id="<?php echo $group; ?>">
                    <div class="pa-md bg-auto bg-light-xs bdr-b">
                        <h4 class="fw-sb"><?php echo $view['translator']->trans('mautic.lead.field.group.' . $group); ?></h4>
                    </div>
                    <div class="pa-md">
                    <?php if ($group == 'core'): ?>

                        <div class="form-group mb-0">
                            <label class="control-label mb-xs"><?php echo $view['translator']->trans('mautic.core.name'); ?></label>
                            <div class="row">
                                <div class="col-sm-2">
                                    <?php echo $view['form']->widget($form['title'], array('attr' => array('placeholder' => $form['title']->vars['label']))); ?>
                                </div>
                                <div class="col-sm-3">
                                    <?php echo $view['form']->widget($form['firstname'], array('attr' => array('placeholder' => $form['firstname']->vars['label']))); ?>
                                </div>
                                <div class="col-sm-3">
                                    <?php echo $view['form']->widget($form['lastname'], array('attr' => array('placeholder' => $form['lastname']->vars['label']))); ?>
                                </div>
                            </div>
                        </div>
                        <hr class="mnr-md mnl-md">

                        <div class="form-group mb-0">
                            <label class="control-label mb-xs"><?php echo $form['email']->vars['label']; ?></label>
                            <div class="row">
                                <div class="col-sm-8">
                                    <?php echo $view['form']->widget($form['email'], array('attr' => array('placeholder' => $form['email']->vars['label']))); ?>
                                </div>
                            </div>
                        </div>
                        <hr class="mnr-md mnl-md">

                        <div class="form-group mb-0">
                            <label class="control-label mb-xs"><?php echo $view['translator']->trans('mautic.core.company'); ?></label>
                            <div class="row">
                                <div class="col-sm-4">
                                    <?php echo $view['form']->widget($form['company'], array('attr' => array('placeholder' => $view['translator']->trans('mautic.core.company')))); ?>
                                </div>
                                <div class="col-sm-4">
                                    <?php echo $view['form']->widget($form['position'], array('attr' => array('placeholder' => $form['position']->vars['label']))); ?>
                                </div>
                            </div>
                        </div>
                        <hr class="mnr-md mnl-md">

                        <div class="form-group mb-0">
                            <label class="control-label mb-xs"><?php echo $view['translator']->trans('mautic.lead.field.address'); ?></label>
                            <div class="row mb-xs">
                                <div class="col-sm-8">
                                    <?php echo $view['form']->widget($form['address1'], array('attr' => array('placeholder' => $form['address1']->vars['label']))); ?>
                                </div>
                            </div>
                            <div class="row mb-xs">
                                <div class="col-sm-8">
                                    <?php echo $view['form']->widget($form['address2'], array('attr' => array('placeholder' => $form['address2']->vars['label']))); ?>
                                </div>
                            </div>
                            <div class="row mb-xs">
                                <div class="col-sm-4">
                                    <?php echo $view['form']->widget($form['city'], array('attr' => array('placeholder' => $form['city']->vars['label']))); ?>
                                </div>
                                <div class="col-sm-4">
                                    <?php echo $view['form']->widget($form['state'], array('attr' => array('placeholder' => $form['state']->vars['label']))); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <?php echo $view['form']->widget($form['zipcode'], array('attr' => array('placeholder' => $form['zipcode']->vars['label']))); ?>
                                </div>
                                <div class="col-sm-4">
                                    <?php echo $view['form']->widget($form['country'], array('attr' => array('placeholder' => $form['country']->vars['label']))); ?>
                                </div>
                            </div>
                        </div>
                        <hr class="mnr-md mnl-md">
                        <?php endif; ?>

                        <div class="row">
                        <?php foreach ($groupFields as $alias => $field): ?>
                        <?php if ($form[$alias]->isRendered()) continue; ?>
                            <div class="col-sm-8">
                                <?php echo $view['form']->row($form[$alias]); ?>
                            </div>
                        <?php endforeach; ?>
                        </div>

                        <?php if ($group == 'core'): ?>

                        <hr class="mnr-md mnl-md">

                        <div class="row">
                            <div class="col-sm-4">
                                <?php echo $view['form']->label($form['owner']); ?>
                                <?php echo $view['form']->widget($form['owner']); ?>
                            </div>
                            <div class="col-sm-4">
                                <?php echo $view['form']->label($form['tags']); ?>
                                <?php echo $view['form']->widget($form['tags']); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php
                endif;
                endif;
                endforeach;
            ?>
            <!--/ #pane -->
        </div>
    </div>
    <!--/ end: container -->
</div>
<?php echo $view['form']->end($form); ?>
<!--/ end: box layout -->
