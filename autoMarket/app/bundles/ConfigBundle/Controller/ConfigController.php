<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\ConfigBundle\Controller;

use Mautic\CoreBundle\Controller\FormController;
use Mautic\ConfigBundle\Event\ConfigEvent;
use Mautic\ConfigBundle\Event\ConfigBuilderEvent;
use Mautic\ConfigBundle\ConfigEvents;
use Mautic\CoreBundle\Helper\EncryptionHelper;
use Symfony\Component\Form\FormError;

/**
 * Class ConfigController
 */
class ConfigController extends FormController
{

    /**
     * Controller action for editing the application configuration
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction ()
    {
        //admin only allowed
        if (!$this->factory->getUser()->isAdmin()) {
            return $this->accessDenied();
        }

        $event      = new ConfigBuilderEvent($this->factory);
        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->dispatch(ConfigEvents::CONFIG_ON_GENERATE, $event);
        $formConfigs            = $event->getForms();
        $formThemes             = $event->getFormThemes();
        $doNotChange            = $this->factory->getParameter('security.restrictedConfigFields');
        $doNotChangeDisplayMode = $this->factory->getParameter('security.restrictedConfigFields.displayMode', 'remove');

        $this->mergeParamsWithLocal($formConfigs, $doNotChange);

        /* @type \Mautic\ConfigBundle\Model\ConfigModel $model */
        $model = $this->factory->getModel('config');

        // Create the form
        $action = $this->generateUrl('mautic_config_action', array('objectAction' => 'edit'));
        $form   = $model->createForm($formConfigs, $this->get('form.factory'), array(
            'action'                 => $action,
            'doNotChange'            => $doNotChange,
            'doNotChangeDisplayMode' => $doNotChangeDisplayMode
        ));

        /** @var \Mautic\InstallBundle\Configurator\Configurator $configurator */
        $configurator = $this->get('mautic.configurator');
        $isWritabale  = $configurator->isFileWritable();

        // Check for a submitted form and process it
        if ($this->request->getMethod() == 'POST') {
            if (!$cancelled = $this->isFormCancelled($form)) {
                $isValid = false;
                if ($isWritabale && $isValid = $this->isFormValid($form)) {

                    // Bind request to the form
                    $post     = $this->request->request;
                    $formData = $form->getData();

                    // Dispatch pre-save event. Bundles may need to modify some field values like passwords before save
                    $configEvent = new ConfigEvent($formData, $post);
                    $dispatcher->dispatch(ConfigEvents::CONFIG_PRE_SAVE, $configEvent);
                    $formValues = $configEvent->getConfig();

                    // Prevent these from getting overwritten with empty values
                    $unsetIfEmpty = $configEvent->getPreservedFields();

                    // Merge each bundle's updated configuration into the local configuration
                    foreach ($formValues as $object) {
                        $checkThese = array_intersect(array_keys($object), $unsetIfEmpty);
                        foreach ($checkThese as $checkMe) {
                            if (empty($object[$checkMe])) {
                                unset($object[$checkMe]);
                            }
                        }

                        $configurator->mergeParameters($object);
                    }

                    try {
                        // Ensure the config has a secret key
                        $params = $configurator->getParameters();
                        if (empty($params['secret_key'])) {
                            $configurator->mergeParameters(array('secret_key' => EncryptionHelper::generateKey()));
                        }

                        $configurator->write();

                        $this->addFlash('mautic.config.config.notice.updated');

                        // We must clear the application cache for the updated values to take effect
                        /** @var \Mautic\CoreBundle\Helper\CacheHelper $cacheHelper */
                        $cacheHelper = $this->factory->getHelper('cache');
                        $cacheHelper->clearContainerFile();

                    } catch (\RuntimeException $exception) {
                        $this->addFlash('mautic.config.config.error.not.updated', array('%exception%' => $exception->getMessage()), 'error');
                    }
                } elseif (!$isWritabale) {
                    $form->addError(new FormError(
                        $this->factory->getTranslator()->trans('mautic.config.notwritable')
                    ));
                }
            }

            // If the form is saved or cancelled, redirect back to the dashboard
            if ($cancelled || $isValid) {
                if (!$cancelled && $this->isFormApplied($form)) {
                    return $this->delegateRedirect($this->generateUrl('mautic_config_action', array('objectAction' => 'edit')));
                } else {
                    return $this->delegateRedirect($this->generateUrl('mautic_dashboard_index'));
                }
            }
        }

        $tmpl = $this->request->isXmlHttpRequest() ? $this->request->get('tmpl', 'index') : 'index';

        return $this->delegateView(array(
            'viewParameters'  => array(
                'tmpl'        => $tmpl,
                'security'    => $this->factory->getSecurity(),
                'form'        => $this->setFormTheme($form, 'MauticConfigBundle:Config:form.html.php', $formThemes),
                'formConfigs' => $formConfigs,
                'isWritable'  => $isWritabale
            ),
            'contentTemplate' => 'MauticConfigBundle:Config:form.html.php',
            'passthroughVars' => array(
                'activeLink'    => '#mautic_config_index',
                'mauticContent' => 'config',
                'route'         => $this->generateUrl('mautic_config_action', array('objectAction' => 'edit'))
            )
        ));
    }

    /**
     * Merges default parameters from each subscribed bundle with the local (real) params
     *
     * @param array $forms
     * @param array $doNotChange
     *
     * @return array
     */
    private function mergeParamsWithLocal (&$forms, $doNotChange)
    {
        // Import the current local configuration, $parameters is defined in this file
        $localConfigFile = $this->factory->getLocalConfigFile();

        /** @var $parameters */
        include $localConfigFile;

        $localParams = $parameters;

        foreach ($forms as &$form) {

            // Merge the bundle params with the local params
            foreach ($form['parameters'] as $key => $value) {
                if (in_array($key, $doNotChange)) {
                    unset($form['parameters'][$key]);
                } elseif (array_key_exists($key, $localParams)) {
                    $form['parameters'][$key] = $localParams[$key];
                }
            }
        }
    }
}
