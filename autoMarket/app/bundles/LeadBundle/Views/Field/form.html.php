<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

$view->extend('MauticCoreBundle:Default:content.html.php');
$view['slots']->set('mauticContent', 'leadfield');
$userId = $form->vars['data']->getId();
if (!empty($userId)) {
    $isNew   = false;
    $field   = $form->vars['data']->getLabel();
    $header  = $view['translator']->trans('mautic.lead.field.header.edit', array("%name%" => $field));
} else {
    $isNew  = true;
    $header = $view['translator']->trans('mautic.lead.field.header.new');
}
$view['slots']->set("headerTitle", $header);

$selectTemplate      = $view['form']->row($form['properties_select_template']);
$defaultTemplate     = $view['form']->widget($form['default_template']);
$defaultBoolTemplate = $view['form']->widget($form['default_bool_template']);
?>

<?php echo $view['form']->start($form); ?>
<div class="box-layout">
    <!-- container -->
    <div class="col-md-8 bg-auto height-auto bdr-r">
        <div class="pa-md">
            <div class="row">
                <div class="col-md-6">
                    <?php echo $view['form']->row($form['label']); ?>
                </div>
                <div class="col-md-6">
                    <?php echo $view['form']->row($form['alias']); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <?php echo $view['form']->row($form['type']); ?>
                </div>
                <div class="col-md-6">
                    <?php echo $view['form']->row($form['defaultValue']); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <?php echo $view['form']->row($form['group']); ?>
                </div>
                <div class="col-md-6">
                    <?php echo $view['form']->row($form['order']); ?>
                </div>
            </div>
            <?php
            $type          = $form['type']->vars['data'];
            $properties    = $form['properties']->vars['data'];
            $errors        = count($form['properties']->vars['errors']);
            $feedbackClass = (!empty($errors)) ? " has-error" : "";
            ?>

            <div class="row">
                <div class="form-group  col-xs-12 col-sm-8 col-md-6<?php echo $feedbackClass; ?>">
                    <div id="leadfield_properties">
                        <?php
                        switch ($type):
                        case 'boolean':
                            echo $view->render('MauticLeadBundle:Field:properties_boolean.html.php', array(
                                'yes' => isset($properties['yes']) ? $properties['yes'] : '',
                                'no'  => isset($properties['no'])  ? $properties['no'] : ''
                            ));
                            break;
                        case 'number':
                            echo $view->render('MauticLeadBundle:Field:properties_number.html.php', array(
                                'roundMode' => isset($properties['roundmode']) ? $properties['roundmode'] : '',
                                'precision' => isset($properties['precision']) ? $properties['precision'] : ''
                            ));
                            break;
                        case 'select':
                        case 'lookup':
                            echo $view->render('MauticLeadBundle:Field:properties_select.html.php', array(
                                'form'           => $form['properties'],
                                'selectTemplate' => $selectTemplate
                            ));
                            break;
                        endswitch;
                        ?>
                    </div>
                    <?php echo $view['form']->errors($form['properties']); ?>
                </div>
            </div>
            <?php $form['properties']->setRendered(); ?>
        </div>
    </div>
    <div class="col-md-4 bg-white height-auto">
        <div class="pr-lg pl-lg pt-md pb-md">
            <div class="row">
                <div class="col-md-6">
                    <?php echo $view['form']->row($form['isPublished']); ?>
                </div>
                <div class="col-md-6">
                    <?php echo $view['form']->row($form['isRequired']); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <?php echo $view['form']->row($form['isVisible']); ?>
                </div>
                <div class="col-md-6">
                    <?php echo $view['form']->row($form['isShortVisible']); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <?php echo $view['form']->row($form['isListable']); ?>
                </div>
                <div class="col-md-6">
                    <?php echo $view['form']->row($form['isPubliclyUpdatable']); ?>
                </div>
            </div>
            <?php echo $view['form']->rest($form); ?>
        </div>
    </div>
</div>
<?php echo $view['form']->end($form); ?>

<?php if ($isNew): ?>
<div id="field-templates" class="hide">
    <div class="default">
        <?php echo $defaultTemplate; ?>
    </div>
    <div class="default_bool">
        <?php echo $defaultBoolTemplate; ?>
    </div>
<?php
    echo $view->render('MauticLeadBundle:Field:properties_number.html.php');
    echo $view->render('MauticLeadBundle:Field:properties_boolean.html.php');
    echo $view->render('MauticLeadBundle:Field:properties_number.html.php');
    echo $view->render('MauticLeadBundle:Field:properties_select.html.php', array(
        'selectTemplate' => $selectTemplate
    ));
?>
</div>
<?php endif; ?>