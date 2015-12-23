<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

$view->extend('MauticCoreBundle:Default:content.html.php');
$view['slots']->set('mauticContent', 'form');

$header = ($activeForm->getId()) ?
    $view['translator']->trans('mautic.form.form.header.edit',
        array('%name%' => $view['translator']->trans($activeForm->getName()))) :
    $view['translator']->trans('mautic.form.form.header.new');
$view['slots']->set("headerTitle", $header);

$formId       = $form['sessionId']->vars['data'];
$isStandalone = $activeForm->isStandalone();
?>
<?php echo $view['form']->start($form); ?>
<div class="box-layout">
    <div class="col-md-9 height-auto bg-white">
        <div class="row">
            <div class="col-xs-12">
                <!-- tabs controls -->
                <ul class="bg-auto nav nav-tabs pr-md pl-md">
                    <li class="active"><a href="#details-container" role="tab" data-toggle="tab"><?php echo $view['translator']->trans('mautic.core.details'); ?></a></li>
                    <li id="fields-tab"><a href="#fields-container" role="tab" data-toggle="tab"><?php echo $view['translator']->trans('mautic.form.tab.fields'); ?></a></li>
                    <li<?php if (!$isStandalone) echo ' class="hide"'; ?> id="actions-tab"><a href="#actions-container" role="tab" data-toggle="tab"><?php echo $view['translator']->trans('mautic.form.tab.actions'); ?></a></li>
                </ul>
                <!--/ tabs controls -->
                <div class="tab-content pa-md">
                    <div class="tab-pane fade in active bdr-w-0" id="details-container">
                        <div class="row">
                            <div class="col-md-6">
                                <?php
                                echo $view['form']->row($form['name']);
                                echo $view['form']->row($form['description']);
                                ?>
                            </div>
                            <div class="col-md-6">
                                <?php
                                echo $view['form']->row($form['postAction']);
                                echo $view['form']->row($form['postActionProperty']);
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade bdr-w-0" id="fields-container">
                        <?php echo $view->render('MauticFormBundle:Builder:style.html.php'); ?>
                        <div id="mauticforms_fields">
                            <div class="available-fields mb-md">
                                <p><?php echo $view['translator']->trans('mautic.form.form.addfield'); ?></p>
                                <div class="dropdown">
                                    <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
                                        <?php echo $view['translator']->trans('mautic.form.field.add'); ?>
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu" role="menu">
                                        <?php foreach ($fields as $fieldType => $field): ?>
                                            <li id="field_<?php echo $fieldType; ?>">
                                                <a class="list-group-item" data-toggle="ajaxmodal" data-target="#formComponentModal" href="<?php echo $view['router']->generate('mautic_formfield_action', array('objectAction' => 'new', 'type' => $fieldType, 'tmpl' => 'field', 'formId' => $formId)); ?>">
                                                    <div>
                                                        <?php echo $field; ?>
                                                    </div>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                            <?php
                            foreach ($formFields as $field):
                                if (!empty($field['isCustom'])):
                                    $params   = $field['customParameters'];
                                    $template = $params['template'];
                                else:
                                    $template = 'MauticFormBundle:Field:' . $field['type'] . '.html.php';
                                endif;
                                echo $view->render($template, array(
                                    'field'   => $field,
                                    'inForm'  => true,
                                    'id'      => $field['id'],
                                    'deleted' => in_array($field['id'], $deletedFields),
                                    'formId'  => $formId
                                ));
                            endforeach;
                            ?>
                            <?php if (!count($formFields)): ?>
                            <div class="alert alert-info" id="form-field-placeholder">
                                <p><?php echo $view['translator']->trans('mautic.form.form.addfield'); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="tab-pane fade bdr-w-0<?php if (!$isStandalone) echo ' hide'; ?> " id="actions-container">
                        <div id="mauticforms_actions">
                            <div class="available-actions mb-md">
                                <p><?php echo $view['translator']->trans('mautic.form.form.addaction'); ?></p>
                                <div class="dropdown">
                                    <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
                                        <?php echo $view['translator']->trans('mautic.form.action.add'); ?>
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu" role="menu">
                                        <?php foreach ($actions as $group => $action): ?>
                                            <li role="presentation" class="dropdown-header">
                                                <?php echo $view['translator']->trans($group); ?>
                                            </li>
                                            <?php foreach ($action as $k => $e): ?>
                                                <li id="action_<?php echo $k; ?>">
                                                    <a data-toggle="ajaxmodal" data-target="#formComponentModal" class="list-group-item" href="<?php echo $view['router']->generate('mautic_formaction_action', array('objectAction' => 'new', 'type' => $k, 'tmpl'=> 'action', 'formId' => $formId)); ?>">
                                                        <div data-toggle="tooltip" title="<?php echo $view['translator']->trans($e['description']); ?>">
                                                            <span><?php echo $view['translator']->trans($e['label']); ?></span>
                                                        </div>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                            <?php
                            foreach ($formActions as $action):
                                $template = (isset($action['settings']['template'])) ? $action['settings']['template'] :
                                    'MauticFormBundle:Action:generic.html.php';
                                echo $view->render($template, array(
                                    'action'  => $action,
                                    'inForm'  => true,
                                    'id'      => $action['id'],
                                    'deleted' => in_array($action['id'], $deletedActions),
                                    'formId'  => $formId
                                ));
                            endforeach;
                            ?>
                            <?php if (!count($formActions)): ?>
                                <div class="alert alert-info" id="form-action-placeholder">
                                    <p><?php echo $view['translator']->trans('mautic.form.form.addaction'); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 bg-white height-auto bdr-l">
        <div class="pr-lg pl-lg pt-md pb-md">
            <?php
            echo $view['form']->row($form['category']);
            echo $view['form']->row($form['isPublished']);
            echo $view['form']->row($form['publishUp']);
            echo $view['form']->row($form['publishDown']);
            echo $view['form']->row($form['inKioskMode']);
            echo $view['form']->row($form['renderStyle']);
            echo $view['form']->row($form['template']);
            ?>
        </div>
    </div>
</div>
<?php

echo $view['form']->end($form);

if ($activeForm->getFormType() === null || !empty($forceTypeSelection)):
    echo $view->render('MauticCoreBundle:Helper:form_selecttype.html.php',
        array(
            'item'               => $activeForm,
            'mauticLang'         => array(
                'newStandaloneForm' => 'mautic.form.type.standalone.header',
                'newCampaignForm'   => 'mautic.form.type.campaign.header'
            ),
            'typePrefix'         => 'form',
            'cancelUrl'          => 'mautic_form_index',
            'header'             => 'mautic.form.type.header',
            'typeOneHeader'      => 'mautic.form.type.campaign.header',
            'typeOneIconClass'   => 'fa-cubes',
            'typeOneDescription' => 'mautic.form.type.campaign.description',
            'typeOneOnClick'     => "Mautic.selectFormType('campaign');",
            'typeTwoHeader'      => 'mautic.form.type.standalone.header',
            'typeTwoIconClass'   => 'fa-list',
            'typeTwoDescription' => 'mautic.form.type.standalone.description',
            'typeTwoOnClick'     => "Mautic.selectFormType('standalone');"
        ));
endif;

$view['slots']->append('modal', $this->render('MauticCoreBundle:Helper:modal.html.php', array(
    'id'           => 'formComponentModal',
    'header'       => false,
    'footerButtons'=> true
)));
?>
