<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

$fields    = $form->children;
$fieldKeys = array_keys($fields);
$template = '<div class="col-md-6">{content}</div>';
?>

<?php if (count(array_intersect($fieldKeys, array('mailer_from_name', 'mailer_from_email', 'mailer_transport', 'mailer_spool_type')))): ?>
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $view['translator']->trans('mautic.email.config.header.mail'); ?></h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <?php echo $view['form']->rowIfExists($fields, 'mailer_from_name', $template); ?>
                <?php echo $view['form']->rowIfExists($fields, 'mailer_from_email', $template); ?>
            </div>
            <div class="row">
                <?php echo $view['form']->rowIfExists($fields, 'mailer_return_path', $template); ?>
            </div>

            <?php if (isset($fields['mailer_from_name']) || isset($fields['mailer_from_email'])): ?>
                <hr class="text-muted" />
            <?php endif; ?>

            <?php if (isset($fields['mailer_transport'])): ?>
                <div class="row">
                    <div class="col-sm-6">
                        <?php echo $view['form']->row($fields['mailer_transport']); ?>
                    </div>
                    <div class="col-sm-6 pt-lg mt-3" id="mailerTestButtonContainer" data-hide-on='{"config_emailconfig_mailer_transport":["sendmail","mail"]}'>
                        <div class="button_container">
                            <?php echo $view['form']->widget($fields['mailer_test_connection_button']); ?>
                            <?php echo $view['form']->widget($fields['mailer_test_send_button']); ?>
                            <span class="fa fa-spinner fa-spin hide"></span>
                        </div>
                        <div class="col-md-9 help-block"></div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="row">
                <?php echo $view['form']->rowIfExists($fields, 'mailer_host', $template); ?>
                <?php echo $view['form']->rowIfExists($fields, 'mailer_port', $template); ?>
            </div>

            <div class="row">
                <?php echo $view['form']->rowIfExists($fields, 'mailer_encryption', $template); ?>
                <?php echo $view['form']->rowIfExists($fields, 'mailer_auth_mode', $template); ?>
            </div>

            <div class="row">
                <?php echo $view['form']->rowIfExists($fields, 'mailer_user', $template); ?>
                <?php echo $view['form']->rowIfExists($fields, 'mailer_password', $template); ?>
            </div>

            <?php if (isset($fields['mailer_transport'])): ?>
                <hr class="text-muted" />
            <?php endif; ?>

            <div class="row">
                <?php echo $view['form']->rowIfExists($fields, 'mailer_spool_type', $template); ?>
                <?php echo $view['form']->rowIfExists($fields, 'mailer_spool_path', $template); ?>
            </div>

            <div class="row">
                <?php echo $view['form']->rowIfExists($fields, 'mailer_spool_msg_limit', $template); ?>
                <?php echo $view['form']->rowIfExists($fields, 'mailer_spool_time_limit', $template); ?>
            </div>

            <div class="row">
                <?php echo $view['form']->rowIfExists($fields, 'mailer_spool_recover_timeout', $template); ?>
                <?php echo $view['form']->rowIfExists($fields, 'mailer_spool_clear_timeout', $template); ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (isset($fields['monitored_email'])): ?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $view['translator']->trans('mautic.email.config.header.monitored_email'); ?></h3>
    </div>
    <div class="panel-body">
        <?php if (function_exists('imap_open')): ?>
        <?php echo $view['form']->widget($form['monitored_email']); ?>
        <?php else: ?>
            <div class="alert alert-info"><?php echo $view['translator']->trans('mautic.email.imap_extension_missing'); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $view['translator']->trans('mautic.email.config.header.message'); ?></h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <?php echo $view['form']->rowIfExists($fields, 'unsubscribe_text', $template); ?>
            <?php echo $view['form']->rowIfExists($fields, 'webview_text', $template); ?>
        </div>
        <div class="row">
            <?php echo $view['form']->rowIfExists($fields, 'unsubscribe_message', $template); ?>
            <?php echo $view['form']->rowIfExists($fields, 'resubscribe_message', $template); ?>
        </div>
    </div>
</div>
