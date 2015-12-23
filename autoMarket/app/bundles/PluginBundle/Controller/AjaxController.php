<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\PluginBundle\Controller;

use Mautic\CoreBundle\Controller\AjaxController as CommonAjaxController;
use Mautic\CoreBundle\Helper\InputHelper;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AjaxController
 *
 * @package Mautic\PluginBundle\Controller
 */
class AjaxController extends CommonAjaxController
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function setIntegrationFilterAction (Request $request)
    {
        $session     = $this->factory->getSession();
        $pluginFilter = InputHelper::int($this->request->get('plugin'));
        $session->set('mautic.integrations.filter', $pluginFilter);

        return $this->sendJsonResponse(array('success' => 1));
    }

    /**
     * Get the HTML for list of fields
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function getIntegrationLeadFieldsAction (Request $request)
    {
        $integration = $request->request->get('integration');
        $settings    = $request->request->get('settings');
        $dataArray   = array('success' => 0);

        if (!empty($integration) && !empty($settings)) {
            /** @var \Mautic\PluginBundle\Helper\IntegrationHelper $helper */
            $helper = $this->factory->getHelper('integration');
            /** @var \Mautic\PluginBundle\Integration\AbstractIntegration $object */
            $object = $helper->getIntegrationObject($integration);

            if ($object) {
                $integrationFields = $object->getFormLeadFields($settings);

                if (!empty($integrationFields)) {
                    // Get a list of custom form fields
                    $leadFields = $this->factory->getModel('plugin')->getLeadFields();
                    list ($specialInstructions, $alertType) = $object->getFormNotes('leadfield_match');

                    $form = $this->createForm('integration_fields', array(), array(
                        'lead_fields'          => $leadFields,
                        'integration_fields'   => $integrationFields,
                        'csrf_protection'      => false,
                        'special_instructions' => $specialInstructions,
                        'alert_type'           => $alertType
                    ));

                    $form = $this->setFormTheme($form, 'MauticCoreBundle:Helper:blank_form.html.php', 'MauticPluginBundle:FormTheme\Integration');

                    $html = $this->render('MauticCoreBundle:Helper:blank_form.html.php', array(
                        'form'      => $form,
                        'function'  => 'row',
                        'variables' => array(
                            'integration' => $object
                        )
                    ))->getContent();

                    if (!isset($settings['prefix'])) {
                        $prefix = 'integration_details[featureSettings][leadFields]';
                    } else {
                        $prefix = $settings['prefix'];
                    }

                    $idPrefix = str_replace(array('][', '[', ']'), '_', $prefix);
                    if (substr($idPrefix, -1) == '_') {
                        $idPrefix = substr($idPrefix, 0, -1);
                    }

                    $html = preg_replace('/integration_fields\[(.*?)\]/', $prefix . '[$1]', $html);
                    $html = str_replace('integration_fields', $idPrefix, $html);

                    $dataArray['success'] = 1;
                    $dataArray['html']    = $html;
                }
            }
        }

        return $this->sendJsonResponse($dataArray);
    }


    /**
     * Get the HTML for integration properties
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function getIntegrationConfigAction (Request $request)
    {
        $integration = $request->request->get('integration');
        $settings    = $request->request->get('settings');
        $dataArray   = array('success' => 0);

        if (!empty($integration) && !empty($settings)) {
            /** @var \Mautic\PluginBundle\Helper\IntegrationHelper $helper */
            $helper = $this->factory->getHelper('integration');
            /** @var \Mautic\PluginBundle\Integration\AbstractIntegration $object */
            $object = $helper->getIntegrationObject($integration);

            if ($object) {
                $objectSettings = $object->getIntegrationSettings();
                $defaults       = $objectSettings->getFeatureSettings();

                $form           = $this->createForm('integration_config', $defaults, array(
                    'integration'     => $object,
                    'csrf_protection' => false
                ));

                $form = $this->setFormTheme($form, 'MauticCoreBundle:Helper:blank_form.html.php', 'MauticPluginBundle:FormTheme\Integration');

                $html = $this->render('MauticCoreBundle:Helper:blank_form.html.php', array(
                    'form'      => $form,
                    'function'  => 'widget',
                    'variables' => array(
                        'integration' => $object
                    )
                ))->getContent();

                $prefix   = str_replace('[integration]', '[config]', $settings['name']);
                $idPrefix = str_replace(array('][', '[', ']'), '_', $prefix);
                if (substr($idPrefix, -1) == '_') {
                    $idPrefix = substr($idPrefix, 0, -1);
                }

                $html = preg_replace('/integration_config\[(.*?)\]/', $prefix . '[$1]', $html);
                $html = str_replace('integration_config', $idPrefix, $html);

                $dataArray['success'] = 1;
                $dataArray['html']    = $html;
            }
        }

        return $this->sendJsonResponse($dataArray);
    }
}