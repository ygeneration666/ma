<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\FormBundle\Controller;

use Mautic\CoreBundle\Controller\FormController as CommonFormController;
use Mautic\FormBundle\Entity\Field;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class FieldController
 */
class FieldController extends CommonFormController
{

    /**
     * Generates new form and processes post data
     *
     * @return JsonResponse
     */
    public function newAction ()
    {
        $success = 0;
        $valid   = $cancelled = false;
        $method  = $this->request->getMethod();
        $session = $this->factory->getSession();

        if ($method == 'POST') {
            $formField          = $this->request->request->get('formfield');
            $fieldType          = $formField['type'];
            $formId             = $formField['formId'];
        } else {
            $fieldType = $this->request->query->get('type');
            $formId    = $this->request->query->get('formId');
            $formField = array(
                'type'   => $fieldType,
                'formId' => $formId
            );
        }

        //ajax only for form fields
        if (!$fieldType ||
            !$this->request->isXmlHttpRequest() ||
            !$this->factory->getSecurity()->isGranted(array('form:forms:editown', 'form:forms:editother', 'form:forms:create'), 'MATCH_ONE')
        ) {
            return $this->modalAccessDenied();
        }

        //fire the form builder event
        $customComponents = $this->factory->getModel('form.form')->getCustomComponents();

        $customParams = (isset($customComponents['fields'][$fieldType])) ? $customComponents['fields'][$fieldType] : false;


        // Only show the lead fields not already used
        $usedLeadFields = $session->get('mautic.form.'.$formId.'.fields.leadfields', array());
        $testLeadFields = array_flip($usedLeadFields);
        $leadFields     = $this->factory->getModel('lead.field')->getFieldList();
        foreach ($leadFields as &$group) {
            $group = array_diff_key($group, $testLeadFields);
        }

        $form = $this->get('form.factory')->create('formfield', $formField, array(
            'action'           => $this->generateUrl('mautic_formfield_action', array('objectAction' => 'new')),
            'customParameters' => $customParams,
            'leadFields'       => $leadFields
        ));
        $form->get('formId')->setData($formId);

        if (!empty($customParams)) {
            $formField['isCustom']         = true;
            $formField['customParameters'] = $customParams;
        }

        //Check for a submitted form and process it
        if ($method == 'POST') {
            if (!$cancelled = $this->isFormCancelled($form)) {
                if ($valid = $this->isFormValid($form)) {
                    $success = 1;

                    //form is valid so process the data
                    $keyId = 'new' . hash('sha1', uniqid(mt_rand()));

                    //save the properties to session
                    $fields          = $session->get('mautic.form.'.$formId.'.fields.modified', array());
                    $formData        = $form->getData();
                    $formField       = array_merge($formField, $formData);
                    $formField['id'] = $keyId;

                    // Get aliases in order to generate a new one for the new field
                    $aliases = array();
                    foreach ($fields as $f) {
                        $aliases[] = $f['alias'];
                    }
                    $formField['alias'] = $this->factory->getModel('form.field')->generateAlias($formField['label'], $aliases);

                    if (empty($formField['alias'])) {
                        // Likely a bogus label so generate random alias for column name
                        $formField['alias'] = uniqid('f_');
                    }

                    // Force required for captcha
                    if ($formField['type'] == 'captcha') {
                        $formField['isRequired'] = true;
                    }

                    // Add it to the next to last assuming the last is the submit button
                    if (count($fields)) {
                        $lastField = end($fields);
                        $lastKey   = key($fields);
                        array_pop($fields);

                        $fields[$keyId]   = $formField;
                        $fields[$lastKey] = $lastField;
                    } else {
                        $fields[$keyId] = $formField;
                    }

                    $session->set('mautic.form.'.$formId.'.fields.modified', $fields);

                    // Keep track of used lead fields
                    if (!empty($formData['leadField'])) {
                        $usedLeadFields[$keyId] = $formData['leadField'];
                    } else {
                        unset($usedLeadFields[$keyId]);
                    }
                    $session->set('mautic.form.'.$formId.'.fields.leadfields', $usedLeadFields);
                } else {
                    $success = 0;
                }
            }
        }

        $viewParams = array('type' => $fieldType);
        if ($cancelled || $valid) {
            $closeModal = true;
        } else {
            $closeModal                = false;
            $viewParams['tmpl']        = 'field';
            $viewParams['form']        = (isset($customParams['formTheme'])) ? $this->setFormTheme($form, 'MauticFormBundle:Builder:field.html.php', $customParams['formTheme']) : $form->createView();
            $viewParams['fieldHeader'] = (!empty($customParams)) ? $this->get('translator')->trans($customParams['label']) : $this->get('translator')->transConditional('mautic.core.type.' . $fieldType, 'mautic.form.field.type.' . $fieldType);
        }

        $passthroughVars = array(
            'mauticContent' => 'formField',
            'success'       => $success,
            'route'         => false
        );

        if (!empty($keyId)) {
            //prevent undefined errors
            $entity    = new Field();
            $blank     = $entity->convertToArray();
            $formField = array_merge($blank, $formField);

            $passthroughVars['fieldId']   = $keyId;
            $template                     = (!empty($customParams)) ? $customParams['template'] : 'MauticFormBundle:Field:' . $fieldType . '.html.php';
            $passthroughVars['fieldHtml'] = $this->renderView($template, array(
                'inForm' => true,
                'field'  => $formField,
                'id'     => $keyId,
                'formId'  => $formId
            ));
        }

        if ($closeModal) {
            //just close the modal
            $passthroughVars['closeModal'] = 1;
            $response                      = new JsonResponse($passthroughVars);
            $response->headers->set('Content-Length', strlen($response->getContent()));

            return $response;
        }

        return $this->ajaxAction(array(
            'contentTemplate' => 'MauticFormBundle:Builder:' . $viewParams['tmpl'] . '.html.php',
            'viewParameters'  => $viewParams,
            'passthroughVars' => $passthroughVars
        ));
    }

    /**
     * Generates edit form and processes post data
     *
     * @param int $objectId
     *
     * @return JsonResponse
     */
    public function editAction ($objectId)
    {
        $session   = $this->factory->getSession();
        $method    = $this->request->getMethod();
        $formId    = ($method == "POST") ? $this->request->request->get('formfield[formId]', '', true) : $this->request->query->get('formId');
        $fields    = $session->get('mautic.form.'.$formId.'.fields.modified', array());
        $success   = 0;
        $valid     = $cancelled = false;
        $formField = (array_key_exists($objectId, $fields)) ? $fields[$objectId] : null;

        if ($formField !== null) {
            $fieldType = $formField['type'];

            //ajax only for form fields
            if (!$fieldType ||
                !$this->request->isXmlHttpRequest() ||
                !$this->factory->getSecurity()->isGranted(array('form:forms:editown', 'form:forms:editother', 'form:forms:create'), 'MATCH_ONE')
            ) {
                return $this->modalAccessDenied();
            }

            //set custom params from event if applicable
            $customParams = (!empty($formField['isCustom'])) ? $formField['customParameters'] : array();

            // Only show the lead fields not already used
            $usedLeadFields = $session->get('mautic.form.'.$formId.'.fields.leadfields', array());
            $testLeadFields = array_flip($usedLeadFields);
            $currentLeadField = (isset($formField['leadField'])) ? $formField['leadField'] : null;
            if (!empty($currentLeadField) && isset($testLeadFields[$currentLeadField])) {
                unset($testLeadFields[$currentLeadField]);
            }
            $leadFields     = $this->factory->getModel('lead.field')->getFieldList();
            foreach ($leadFields as &$group) {
                $group = array_diff_key($group, $testLeadFields);
            }

            $form = $this->get('form.factory')->create('formfield', $formField, array(
                'action'           => $this->generateUrl('mautic_formfield_action', array('objectAction' => 'edit', 'objectId' => $objectId)),
                'customParameters' => $customParams,
                'leadFields'       => $leadFields
            ));
            $form->get('formId')->setData($formId);

            //Check for a submitted form and process it
            if ($method == 'POST') {
                if (!$cancelled = $this->isFormCancelled($form)) {
                    if ($valid = $this->isFormValid($form)) {
                        $success = 1;

                        //form is valid so process the data

                        //save the properties to session
                        $session  = $this->factory->getSession();
                        $fields   = $session->get('mautic.form.'.$formId.'.fields.modified');
                        $formData = $form->getData();

                        //overwrite with updated data
                        $formField = array_merge($fields[$objectId], $formData);

                        if (strpos($objectId, 'new') !== false) {
                            // Get aliases in order to generate update for this one
                            $aliases = array();
                            foreach ($fields as $k => $f) {
                                if ($k != $objectId) {
                                    $aliases[] = $f['alias'];
                                }
                            }
                            $formField['alias'] = $this->factory->getModel('form.field')->generateAlias($formField['label'], $aliases);
                        }

                        $fields[$objectId] = $formField;
                        $session->set('mautic.form.'.$formId.'.fields.modified', $fields);

                        // Keep track of used lead fields
                        if (!empty($formData['leadField'])) {
                            $usedLeadFields[$objectId] = $formData['leadField'];
                        } else {
                            unset($usedLeadFields[$objectId]);
                        }
                        $session->set('mautic.form.'.$formId.'.fields.leadfields', $usedLeadFields);
                    }
                }
            }

            $viewParams = array('type' => $fieldType);
            if ($cancelled || $valid) {
                $closeModal = true;
            } else {
                $closeModal                = false;
                $viewParams['tmpl']        = 'field';
                $viewParams['form']        = (isset($customParams['formTheme'])) ? $this->setFormTheme($form, 'MauticFormBundle:Builder:field.html.php', $customParams['formTheme']) : $form->createView();
                $viewParams['fieldHeader'] = (!empty($customParams)) ? $this->get('translator')->trans($customParams['label']) : $this->get('translator')->transConditional('mautic.core.type.' . $fieldType, 'mautic.form.field.type.' . $fieldType);
            }

            $passthroughVars = array(
                'mauticContent' => 'formField',
                'success'       => $success,
                'route'         => false
            );

            $passthroughVars['fieldId'] = $objectId;
            if (!empty($customParams)) {
                $template = $customParams['template'];
            } else {
                $template = 'MauticFormBundle:Field:' . $fieldType . '.html.php';
            }

            //prevent undefined errors
            $entity    = new Field();
            $blank     = $entity->convertToArray();
            $formField = array_merge($blank, $formField);

            $passthroughVars['fieldHtml'] = $this->renderView($template, array(
                'inForm' => true,
                'field'  => $formField,
                'id'     => $objectId,
                'formId'  => $formId
            ));

            if ($closeModal) {
                //just close the modal
                $passthroughVars['closeModal'] = 1;
                $response                      = new JsonResponse($passthroughVars);
                $response->headers->set('Content-Length', strlen($response->getContent()));

                return $response;
            }

            return $this->ajaxAction(array(
                'contentTemplate' => 'MauticFormBundle:Builder:' . $viewParams['tmpl'] . '.html.php',
                'viewParameters'  => $viewParams,
                'passthroughVars' => $passthroughVars
            ));
        }

        $response = new JsonResponse(array('success' => 0));
        $response->headers->set('Content-Length', strlen($response->getContent()));

        return $response;
    }

    /**
     * Deletes the entity
     *
     * @param int $objectId
     *
     * @return JsonResponse
     */
    public function deleteAction ($objectId)
    {
        $session = $this->factory->getSession();
        $formId  = $this->request->query->get('formId');
        $fields  = $session->get('mautic.form.'.$formId.'.fields.modified', array());
        $delete  = $session->get('mautic.form.'.$formId.'.fields.deleted', array());

        //ajax only for form fields
        if (!$this->request->isXmlHttpRequest() ||
            !$this->factory->getSecurity()->isGranted(array('form:forms:editown', 'form:forms:editother', 'form:forms:create'), 'MATCH_ONE')
        ) {
            return $this->accessDenied();
        }

        $formField = (array_key_exists($objectId, $fields)) ? $fields[$objectId] : null;

        if ($this->request->getMethod() == 'POST' && $formField !== null) {
            //set custom params from event if applicable
            $customParams = (!empty($formField['isCustom'])) ? $formField['customParameters'] : array();

            //add the field to the delete list
            if (!in_array($objectId, $delete)) {
                $delete[] = $objectId;
                $session->set('mautic.form.'.$formId.'.fields.deleted', $delete);
            }

            if (!empty($customParams)) {
                $template = $customParams['template'];
            } else {
                $template = 'MauticFormBundle:Field:' . $formField['type'] . '.html.php';
            }

            //prevent undefined errors
            $entity    = new Field();
            $blank     = $entity->convertToArray();
            $formField = array_merge($blank, $formField);

            $dataArray = array(
                'mauticContent' => 'formField',
                'success'       => 1,
                'target'        => '#mauticform_' . $objectId,
                'route'         => false,
                'fieldId'       => $objectId,
                'fieldHtml'     => $this->renderView($template, array(
                    'inForm'  => true,
                    'field'   => $formField,
                    'id'      => $objectId,
                    'deleted' => true,
                    'formId'  => $formId
                ))
            );
        } else {
            $dataArray = array('success' => 0);
        }

        $response = new JsonResponse($dataArray);
        $response->headers->set('Content-Length', strlen($response->getContent()));

        return $response;
    }

    /**
     * Undeletes the entity
     *
     * @param int $objectId
     *
     * @return JsonResponse
     */
    public function undeleteAction ($objectId)
    {
        $session = $this->factory->getSession();
        $formId  = $this->request->query->get('formId');
        $fields  = $session->get('mautic.form.'.$formId.'.fields.modified', array());
        $delete  = $session->get('mautic.form.'.$formId.'.fields.deleted', array());

        //ajax only for form fields
        if (!$this->request->isXmlHttpRequest() ||
            !$this->factory->getSecurity()->isGranted(array('form:forms:editown', 'form:forms:editother', 'form:forms:create'), 'MATCH_ONE')
        ) {
            return $this->accessDenied();
        }

        $formField = (array_key_exists($objectId, $fields)) ? $fields[$objectId] : null;

        if ($this->request->getMethod() == 'POST' && $formField !== null) {
            //set custom params from event if applicable
            $customParams = (!empty($formField['isCustom'])) ? $formField['customParameters'] : array();

            //add the field to the delete list
            if (in_array($objectId, $delete)) {
                $key = array_search($objectId, $delete);
                unset($delete[$key]);
                $session->set('mautic.form.'.$formId.'.fields.deleted', $delete);
            }

            if (!empty($customParams)) {
                $template = $customParams['template'];
            } else {
                $template = 'MauticFormBundle:Field:' . $formField['type'] . '.html.php';
            }

            //prevent undefined errors
            $entity    = new Field();
            $blank     = $entity->convertToArray();
            $formField = array_merge($blank, $formField);

            $dataArray = array(
                'mauticContent' => 'formField',
                'success'       => 1,
                'target'        => '#mauticform_' . $objectId,
                'route'         => false,
                'fieldId'       => $objectId,
                'fieldHtml'     => $this->renderView($template, array(
                    'inForm'  => true,
                    'field'   => $formField,
                    'id'      => $objectId,
                    'deleted' => false,
                    'formId'  => $formId
                ))
            );
        } else {
            $dataArray = array('success' => 0);
        }

        $response = new JsonResponse($dataArray);
        $response->headers->set('Content-Length', strlen($response->getContent()));

        return $response;
    }
}
