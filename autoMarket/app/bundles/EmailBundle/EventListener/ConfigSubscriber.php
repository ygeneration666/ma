<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\EmailBundle\EventListener;

use Mautic\ConfigBundle\ConfigEvents;
use Mautic\ConfigBundle\Event\ConfigEvent;
use Mautic\ConfigBundle\Event\ConfigBuilderEvent;
use Mautic\CoreBundle\EventListener\CommonSubscriber;

/**
 * Class ConfigSubscriber
 *
 * @package Mautic\CoreBundle\EventListener
 */
class ConfigSubscriber extends CommonSubscriber
{

    /**
     * @return array
     */
    static public function getSubscribedEvents()
    {
        return array(
            ConfigEvents::CONFIG_ON_GENERATE    => array('onConfigGenerate', 0),
            ConfigEvents::CONFIG_PRE_SAVE       => array('onConfigBeforeSave', 0)
        );
    }

    public function onConfigGenerate(ConfigBuilderEvent $event)
    {
        $event->addForm(array(
            'bundle'        => 'EmailBundle',
            'formAlias'     => 'emailconfig',
            'formTheme'     => 'MauticEmailBundle:FormTheme\Config',
            'parameters'    => $event->getParametersFromConfig('MauticEmailBundle')
        ));
    }

    public function onConfigBeforeSave(ConfigEvent $event)
    {
        $event->unsetIfEmpty(
            array(
                'mailer_password'
            )
        );

        $data = $event->getConfig('emailconfig');

        // Get the original data so that passwords aren't lost
        $monitoredEmail = $this->factory->getParameter('monitored_email');
        if (isset($data['monitored_email'])) {
            foreach ($data['monitored_email'] as $key => $monitor) {
                if (empty($monitor['password']) && !empty($monitoredEmail[$key]['password'])) {
                    $data['monitored_email'][$key]['password'] = $monitoredEmail[$key]['password'];
                }

                if ($key != 'general') {
                    if (empty($monitor['host']) || empty($monitor['address']) || empty($monitor['folder'])) {
                        $data['monitored_email'][$key]['override_settings'] = '';
                        $data['monitored_email'][$key]['address']           = '';
                        $data['monitored_email'][$key]['host']              = '';
                        $data['monitored_email'][$key]['user']              = '';
                        $data['monitored_email'][$key]['password']          = '';
                        $data['monitored_email'][$key]['ssl']               = '1';
                        $data['monitored_email'][$key]['port']              = '993';
                    }
                }
            }

            $event->setConfig($data, 'emailconfig');
        }
    }
}
