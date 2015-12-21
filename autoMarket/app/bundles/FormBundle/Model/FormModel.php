<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\FormBundle\Model;

use Mautic\CoreBundle\Helper\InputHelper;
use Mautic\CoreBundle\Model\FormModel as CommonFormModel;
use Mautic\FormBundle\Entity\Action;
use Mautic\FormBundle\Entity\Field;
use Mautic\FormBundle\Entity\Form;
use Mautic\FormBundle\Event\FormBuilderEvent;
use Mautic\FormBundle\Event\FormEvent;
use Mautic\FormBundle\FormEvents;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

/**
 * Class FormModel
 */
class FormModel extends CommonFormModel
{

    /**
     * {@inheritdoc}
     *
     * @return \Mautic\FormBundle\Entity\FormRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository('MauticFormBundle:Form');
    }

    /**
     * {@inheritdoc}
     */
    public function getPermissionBase()
    {
        return 'form:forms';
    }

    /**
     * {@inheritdoc}
     */
    public function getNameGetter()
    {
        return "getName";
    }

    /**
     * {@inheritdoc}
     */
    public function createForm($entity, $formFactory, $action = null, $options = array())
    {
        if (!$entity instanceof Form) {
            throw new MethodNotAllowedHttpException(array('Form'));
        }
        $params = (!empty($action)) ? array('action' => $action) : array();
        return $formFactory->create('mauticform', $entity, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity($id = null)
    {
        if ($id === null) {
            return new Form();
        }

        return parent::getEntity($id);
    }

    /**
     * {@inheritdoc}
     *
     * @return bool|FormEvent|void
     * @throws \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException
     */
    protected function dispatchEvent($action, &$entity, $isNew = false, Event $event = null)
    {
        if (!$entity instanceof Form) {
            throw new MethodNotAllowedHttpException(array('Form'));
        }

        switch ($action) {
            case "pre_save":
                $name = FormEvents::FORM_PRE_SAVE;
                break;
            case "post_save":
                $name = FormEvents::FORM_POST_SAVE;
                break;
            case "pre_delete":
                $name = FormEvents::FORM_PRE_DELETE;
                break;
            case "post_delete":
                $name = FormEvents::FORM_POST_DELETE;
                break;
            default:
                return null;
        }

        if ($this->dispatcher->hasListeners($name)) {
            if (empty($event)) {
                $event = new FormEvent($entity, $isNew);
                $event->setEntityManager($this->em);
            }

            $this->dispatcher->dispatch($name, $event);
            return $event;
        } else {
            return null;
        }
    }

    /**
     * @param Form $entity
     * @param      $sessionFields
     */
    public function setFields(Form $entity, $sessionFields)
    {
        $order          = 1;
        $existingFields = $entity->getFields();

        foreach ($sessionFields as $key => $properties) {
            $isNew = (!empty($properties['id']) && isset($existingFields[$properties['id']])) ? false : true;
            $field = !$isNew ? $existingFields[$properties['id']] : new Field();

            if (!$isNew) {
                if (empty($properties['alias'])) {
                    $properties['alias'] = $field->getAlias();
                }
                if (empty($properties['label'])) {
                    $properties['label'] = $field->getLabel();
                }
            }

            foreach ($properties as $f => $v) {
                if (in_array($f, array('id', 'order')))
                    continue;

                $func = "set" . ucfirst($f);
                if (method_exists($field, $func)) {
                    $field->$func($v);
                }
            }
            $field->setForm($entity);
            $field->setSessionId($key);
            $field->setOrder($order);
            $order++;
            $entity->addField($properties['id'], $field);
        }
    }

    /**
     * @param Form $entity
     * @param      $sessionFields
     */
    public function deleteFields(Form $entity, $sessionFields)
    {
        if (empty($sessionFields)) {
            return;
        }
        $existingFields = $entity->getFields();
        foreach ($sessionFields as $fieldId) {
            if (isset($existingFields[$fieldId])) {
                $entity->removeField($fieldId, $existingFields[$fieldId]);
            }
        }
    }

    /**
     * @param Form $entity
     * @param      $sessionActions
     */
    public function setActions(Form $entity, $sessionActions)
    {
        $order   = 1;
        $existingActions = $entity->getActions();
        $savedFields     = $entity->getFields();

        //match sessionId with field Id to update mapped fields
        $fieldIds = array();
        foreach ($savedFields as $id => $field) {
            $fieldIds[$field->getSessionId()] = $field->getId();
        }

        foreach ($sessionActions as $properties) {
            $isNew = (!empty($properties['id']) && isset($existingActions[$properties['id']])) ? false : true;
            $action = !$isNew ? $existingActions[$properties['id']] : new Action();

            foreach ($properties as $f => $v) {
                if (in_array($f, array('id', 'order')))
                    continue;

                $func = "set" .  ucfirst($f);

                if ($f == 'properties') {
                    if (isset($v['mappedFields'])) {
                        foreach ($v['mappedFields'] as $pk => $pv) {
                            if (strpos($pv, 'new') !== false) {
                                $v['mappedFields'][$pk] = $fieldIds[$pv];
                            }
                        }
                    }
                }

                if (method_exists($action, $func)) {
                    $action->$func($v);
                }
            }
            $action->setForm($entity);
            $action->setOrder($order);
            $order++;
            $entity->addAction($properties['id'], $action);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function saveEntity($entity, $unlock = true)
    {
        $isNew = ($entity->getId()) ? false : true;

        if ($isNew) {
            $alias = $this->cleanAlias($entity->getName(), '', 10);
            $entity->setAlias($alias);
        }

        //save the form so that the ID is available for the form html
        parent::saveEntity($entity, $unlock);

        //now build the form table
        if ($entity->getId()) {
            $this->createTableSchema($entity, $isNew);
        }

        $this->generateHtml($entity);
    }

    /**
     * Obtains the cached HTML of a form and generates it if missing
     *
     * @param Form      $form
     * @param bool|true $withScript
     * @param bool|true $useCache
     *
     * @return string
     */
    public function getContent(Form $form, $withScript = true, $useCache = true)
    {
        if ($useCache) {
            $cachedHtml = $form->getCachedHtml();
        }

        if (empty($cachedHtml)) {
            $cachedHtml = $this->generateHtml($form, $useCache);
        }

        if ($withScript) {
            $cachedHtml = $this->getFormScript($form) . "\n\n" . $cachedHtml;
        }

        return $cachedHtml;
    }

    /**
     * Generate the form's html
     *
     * @param Form $entity
     * @param bool $persist
     *
     * @return string
     */
    public function generateHtml(Form $entity, $persist = true)
    {
        //generate cached HTML
        $templating = $this->factory->getTemplating();
        $theme      = $entity->getTemplate();

        if (!empty($theme)) {
            $theme .= '|';
        }

        $html = $templating->render(
            $theme.'MauticFormBundle:Builder:form.html.php',
            array(
                'form'  => $entity,
                'theme' => $theme,
            )
        );

        $entity->setCachedHtml($html);

        if ($persist) {
            //bypass model function as events aren't needed for this
            $this->getRepository()->saveEntity($entity);
        }

        return $html;
    }

    /**
     * Creates the table structure for form results
     *
     * @param Form $entity
     * @param bool $isNew
     * @param bool $dropExisting
     */
    public function createTableSchema(Form $entity, $isNew = false, $dropExisting = false)
    {
        //create the field as its own column in the leads table
        $schemaHelper = $this->factory->getSchemaHelper('table');
        $name         = "form_results_" . $entity->getId() . "_" . $entity->getAlias();
        $columns      = $this->generateFieldColumns($entity);
        if ($isNew || (!$isNew && !$schemaHelper->checkTableExists($name))) {
            $schemaHelper->addTable(array(
                'name'    => $name,
                'columns' => $columns,
                'options' => array(
                    'primaryKey'  => array('submission_id'),
                    'uniqueIndex' => array('submission_id', 'form_id')
                )
            ), true, $dropExisting);
            $schemaHelper->executeChanges();
        } else {
            //check to make sure columns exist
            $schemaHelper = $this->factory->getSchemaHelper('column', $name);
            foreach ($columns as $c) {
                if (!$schemaHelper->checkColumnExists($c['name'])) {
                    $schemaHelper->addColumn($c, false);
                }
            }
            $schemaHelper->executeChanges();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteEntity($entity)
    {
        parent::deleteEntity($entity);

        if (!$entity->getId()) {
            //delete the associated results table
            $schemaHelper = $this->factory->getSchemaHelper('table');
            $schemaHelper->deleteTable("form_results_" . $entity->deletedId . "_" . $entity->getAlias());
            $schemaHelper->executeChanges();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteEntities($ids)
    {
        $entities = parent::deleteEntities($ids);
        $schemaHelper = $this->factory->getSchemaHelper('table');
        foreach ($entities as $id => $entity) {
            //delete the associated results table
            $schemaHelper->deleteTable("form_results_" . $id . "_" . $entity->getAlias());
        }
        $schemaHelper->executeChanges();
        return $entities;
    }

    /**
     * Generate an array of columns from fields
     *
     * @param Form $form
     *
     * @return array
     */
    public function generateFieldColumns(Form $form)
    {
        $fields = $form->getFields();

        $columns = array(
            array(
                'name' => 'submission_id',
                'type' => 'integer'
            ),
            array(
                'name' => 'form_id',
                'type' => 'integer'
            )
        );
        $ignoreTypes = array('button', 'freetext');
        foreach ($fields as $f) {
            if (!in_array($f->getType(), $ignoreTypes) && $f->getSaveResult() !== false) {
                $columns[] = array(
                    'name'    => $f->getAlias(),
                    'type'    => 'text',
                    'options' => array(
                        'notnull' => false
                    )
                );
            }
        }

        return $columns;
    }

    /**
     * Gets array of custom fields and submit actions from bundles subscribed FormEvents::FORM_ON_BUILD
     * @return mixed
     */
    public function getCustomComponents()
    {
        static $customComponents;

        if (empty($customComponents)) {
            //build them
            $event = new FormBuilderEvent($this->translator);
            $this->dispatcher->dispatch(FormEvents::FORM_ON_BUILD, $event);
            $customComponents['fields']  = $event->getFormFields();
            $customComponents['actions'] = $event->getSubmitActions();
            $customComponents['choices'] = $event->getSubmitActionGroups();
        }

        return $customComponents;
    }

    /**
     * Get the document write javascript for the form
     *
     * @param Form $form
     * @return string
     */
    public function getAutomaticJavascript(Form $form)
    {
        $html = $this->getContent($form);

        //replace line breaks with literal symbol and escape quotations
        $search  = array("\n", '"');
        $replace = array('\n', '\"');
        $html = str_replace($search, $replace, $html);
        return "document.write(\"".$html."\");";
    }

    /**
     * @param Form $form
     *
     * @return string
     */
    public function getFormScript(Form $form)
    {
        $templating = $this->factory->getTemplating();
        $theme      = $form->getTemplate();

        if (!empty($theme)) {
            $theme .= '|';
        }

        $script = $templating->render(
            $theme.'MauticFormBundle:Builder:script.html.php',
            array(
                'form'  => $form,
                'theme' => $theme,
            )
        );

        return $script;
    }

    /**
     * Writes in form values from get parameters
     *
     * @param $form
     * @param $formHtml
     */
    public function populateValuesWithGetParameters(Form $form, &$formHtml)
    {
        $request  = $this->factory->getRequest();
        $formName = $form->generateFormName();

        $fields = $form->getFields();
        /** @var \Mautic\FormBundle\Entity\Field $f */
        foreach ($fields as $f) {
            $alias = $f->getAlias();
            if ($request->query->has($alias)) {
                $value = $request->query->get($alias);

                switch ($f->getType()) {
                    case 'text':
                        if (preg_match('/<input(.*?)id="mauticform_input_'.$formName.'_'.$alias.'"(.*?)value="(.*?)"(.*?)\/>/i', $formHtml, $match)) {
                            $replace  = '<input'.$match[1].'id="mauticform_input_'.$formName.'_'.$alias.'"'.$match[2].'value="'.urldecode($value).'"'.$match[4].'/>';
                            $formHtml = str_replace($match[0], $replace, $formHtml);
                        }
                        break;
                    case 'textarea':
                        if (preg_match('/<textarea(.*?)id="mauticform_input_'.$formName.'_'.$alias.'"(.*?)>(.*?)<\/textarea>/i', $formHtml, $match)) {
                            $replace  = '<textarea'.$match[1].'id="mauticform_input_'.$formName.'_'.$alias.'"'.$match[2].'>'.urldecode($value).'</textarea>';
                            $formHtml = str_replace($match[0], $replace, $formHtml);
                        }
                        break;
                    case 'checkboxgrp':
                        if (!is_array($value)) {
                            $value = array($value);
                        }
                        foreach ($value as $val) {
                            $val = urldecode($val);
                            if (preg_match(
                                '/<input(.*?)id="mauticform_checkboxgrp_checkbox(.*?)"(.*?)value="'.$val.'"(.*?)\/>/i',
                                $formHtml,
                                $match
                            )) {
                                $replace  = '<input'.$match[1].'id="mauticform_checkboxgrp_checkbox'.$match[2].'"'.$match[3].'value="'.$val.'"'
                                    .$match[4].' checked />';
                                $formHtml = str_replace($match[0], $replace, $formHtml);
                            }
                        }
                        break;
                    case 'radiogrp':
                        $value = urldecode($value);
                        if (preg_match('/<input(.*?)id="mauticform_radiogrp_radio(.*?)"(.*?)value="'.$value.'"(.*?)\/>/i', $formHtml, $match)) {
                            $replace  = '<input'.$match[1].'id="mauticform_radiogrp_radio'.$match[2].'"'.$match[3].'value="'.$value.'"'.$match[4]
                                .' checked />';
                            $formHtml = str_replace($match[0], $replace, $formHtml);
                        }
                        break;
                }
            }
        }
    }

    /**
     * @param null $operator
     *
     * @return array
     */
    public function getFilterExpressionFunctions($operator = null)
    {
        $operatorOptions = array(
            '='          =>
                array(
                    'label'       => 'mautic.lead.list.form.operator.equals',
                    'expr'        => 'eq',
                    'negate_expr' => 'neq'
                ),
            '!='         =>
                array(
                    'label'       => 'mautic.lead.list.form.operator.notequals',
                    'expr'        => 'neq',
                    'negate_expr' => 'eq'
                ),
            'gt'         =>
                array(
                    'label'       => 'mautic.lead.list.form.operator.greaterthan',
                    'expr'        => 'gt',
                    'negate_expr' => 'lt'
                ),
            'gte'        =>
                array(
                    'label'       => 'mautic.lead.list.form.operator.greaterthanequals',
                    'expr'        => 'gte',
                    'negate_expr' => 'lt'
                ),
            'lt'         =>
                array(
                    'label'       => 'mautic.lead.list.form.operator.lessthan',
                    'expr'        => 'lt',
                    'negate_expr' => 'gt'
                ),
            'lte'        =>
                array(
                    'label'       => 'mautic.lead.list.form.operator.lessthanequals',
                    'expr'        => 'lte',
                    'negate_expr' => 'gt'
                ),
            'like'       =>
                array(
                    'label'       => 'mautic.lead.list.form.operator.islike',
                    'expr'        => 'like',
                    'negate_expr' => 'notLike'
                ),
            '!like'      =>
                array(
                    'label'       => 'mautic.lead.list.form.operator.isnotlike',
                    'expr'        => 'notLike',
                    'negate_expr' => 'like'
                ),
        );

        return ($operator === null) ? $operatorOptions : $operatorOptions[$operator];
    }
}
