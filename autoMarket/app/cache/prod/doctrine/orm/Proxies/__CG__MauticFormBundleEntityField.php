<?php

namespace Proxies\__CG__\Mautic\FormBundle\Entity;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class Field extends \Mautic\FormBundle\Entity\Field implements \Doctrine\ORM\Proxy\Proxy
{
    /**
     * @var \Closure the callback responsible for loading properties in the proxy object. This callback is called with
     *      three parameters, being respectively the proxy object to be initialized, the method that triggered the
     *      initialization process and an array of ordered parameters that were passed to that method.
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setInitializer
     */
    public $__initializer__;

    /**
     * @var \Closure the callback responsible of loading properties that need to be copied in the cloned object
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setCloner
     */
    public $__cloner__;

    /**
     * @var boolean flag indicating if this object was already initialized
     *
     * @see \Doctrine\Common\Persistence\Proxy::__isInitialized
     */
    public $__isInitialized__ = false;

    /**
     * @var array properties to be lazy loaded, with keys being the property
     *            names and values being their default values
     *
     * @see \Doctrine\Common\Persistence\Proxy::__getLazyProperties
     */
    public static $lazyPropertiesDefaults = array();



    /**
     * @param \Closure $initializer
     * @param \Closure $cloner
     */
    public function __construct($initializer = null, $cloner = null)
    {

        $this->__initializer__ = $initializer;
        $this->__cloner__      = $cloner;
    }







    /**
     * 
     * @return array
     */
    public function __sleep()
    {
        if ($this->__isInitialized__) {
            return array('__isInitialized__', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'id', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'label', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'showLabel', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'alias', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'type', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'isCustom', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'customParameters', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'defaultValue', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'isRequired', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'validationMessage', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'helpMessage', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'order', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'properties', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'form', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'labelAttributes', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'inputAttributes', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'containerAttributes', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'leadField', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'saveResult', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'changes', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'sessionId');
        }

        return array('__isInitialized__', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'id', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'label', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'showLabel', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'alias', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'type', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'isCustom', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'customParameters', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'defaultValue', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'isRequired', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'validationMessage', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'helpMessage', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'order', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'properties', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'form', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'labelAttributes', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'inputAttributes', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'containerAttributes', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'leadField', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'saveResult', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'changes', '' . "\0" . 'Mautic\\FormBundle\\Entity\\Field' . "\0" . 'sessionId');
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (Field $proxy) {
                $proxy->__setInitializer(null);
                $proxy->__setCloner(null);

                $existingProperties = get_object_vars($proxy);

                foreach ($proxy->__getLazyProperties() as $property => $defaultValue) {
                    if ( ! array_key_exists($property, $existingProperties)) {
                        $proxy->$property = $defaultValue;
                    }
                }
            };

        }
    }

    /**
     * 
     */
    public function __clone()
    {
        $this->__cloner__ && $this->__cloner__->__invoke($this, '__clone', array());
    }

    /**
     * Forces initialization of the proxy
     */
    public function __load()
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__load', array());
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitialized($initialized)
    {
        $this->__isInitialized__ = $initialized;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitializer(\Closure $initializer = null)
    {
        $this->__initializer__ = $initializer;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __getInitializer()
    {
        return $this->__initializer__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setCloner(\Closure $cloner = null)
    {
        $this->__cloner__ = $cloner;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific cloning logic
     */
    public function __getCloner()
    {
        return $this->__cloner__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     * @static
     */
    public function __getLazyProperties()
    {
        return self::$lazyPropertiesDefaults;
    }

    
    /**
     * {@inheritDoc}
     */
    public function getChanges()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getChanges', array());

        return parent::getChanges();
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        if ($this->__isInitialized__ === false) {
            return (int)  parent::getId();
        }


        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getId', array());

        return parent::getId();
    }

    /**
     * {@inheritDoc}
     */
    public function setLabel($label)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setLabel', array($label));

        return parent::setLabel($label);
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getLabel', array());

        return parent::getLabel();
    }

    /**
     * {@inheritDoc}
     */
    public function setAlias($alias)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setAlias', array($alias));

        return parent::setAlias($alias);
    }

    /**
     * {@inheritDoc}
     */
    public function getAlias()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getAlias', array());

        return parent::getAlias();
    }

    /**
     * {@inheritDoc}
     */
    public function setType($type)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setType', array($type));

        return parent::setType($type);
    }

    /**
     * {@inheritDoc}
     */
    public function getType()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getType', array());

        return parent::getType();
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultValue($defaultValue)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setDefaultValue', array($defaultValue));

        return parent::setDefaultValue($defaultValue);
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultValue()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getDefaultValue', array());

        return parent::getDefaultValue();
    }

    /**
     * {@inheritDoc}
     */
    public function setIsRequired($isRequired)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setIsRequired', array($isRequired));

        return parent::setIsRequired($isRequired);
    }

    /**
     * {@inheritDoc}
     */
    public function getIsRequired()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getIsRequired', array());

        return parent::getIsRequired();
    }

    /**
     * {@inheritDoc}
     */
    public function isRequired()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isRequired', array());

        return parent::isRequired();
    }

    /**
     * {@inheritDoc}
     */
    public function setOrder($order)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setOrder', array($order));

        return parent::setOrder($order);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getOrder', array());

        return parent::getOrder();
    }

    /**
     * {@inheritDoc}
     */
    public function setProperties($properties)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setProperties', array($properties));

        return parent::setProperties($properties);
    }

    /**
     * {@inheritDoc}
     */
    public function getProperties()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getProperties', array());

        return parent::getProperties();
    }

    /**
     * {@inheritDoc}
     */
    public function setValidationMessage($validationMessage)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setValidationMessage', array($validationMessage));

        return parent::setValidationMessage($validationMessage);
    }

    /**
     * {@inheritDoc}
     */
    public function getValidationMessage()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getValidationMessage', array());

        return parent::getValidationMessage();
    }

    /**
     * {@inheritDoc}
     */
    public function setForm(\Mautic\FormBundle\Entity\Form $form)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setForm', array($form));

        return parent::setForm($form);
    }

    /**
     * {@inheritDoc}
     */
    public function getForm()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getForm', array());

        return parent::getForm();
    }

    /**
     * {@inheritDoc}
     */
    public function setLabelAttributes($labelAttributes)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setLabelAttributes', array($labelAttributes));

        return parent::setLabelAttributes($labelAttributes);
    }

    /**
     * {@inheritDoc}
     */
    public function getLabelAttributes()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getLabelAttributes', array());

        return parent::getLabelAttributes();
    }

    /**
     * {@inheritDoc}
     */
    public function setInputAttributes($inputAttributes)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setInputAttributes', array($inputAttributes));

        return parent::setInputAttributes($inputAttributes);
    }

    /**
     * {@inheritDoc}
     */
    public function getInputAttributes()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getInputAttributes', array());

        return parent::getInputAttributes();
    }

    /**
     * {@inheritDoc}
     */
    public function getContainerAttributes()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getContainerAttributes', array());

        return parent::getContainerAttributes();
    }

    /**
     * {@inheritDoc}
     */
    public function setContainerAttributes($containerAttributes)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setContainerAttributes', array($containerAttributes));

        return parent::setContainerAttributes($containerAttributes);
    }

    /**
     * {@inheritDoc}
     */
    public function convertToArray()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'convertToArray', array());

        return parent::convertToArray();
    }

    /**
     * {@inheritDoc}
     */
    public function setShowLabel($showLabel)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setShowLabel', array($showLabel));

        return parent::setShowLabel($showLabel);
    }

    /**
     * {@inheritDoc}
     */
    public function getShowLabel()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getShowLabel', array());

        return parent::getShowLabel();
    }

    /**
     * {@inheritDoc}
     */
    public function showLabel()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'showLabel', array());

        return parent::showLabel();
    }

    /**
     * {@inheritDoc}
     */
    public function setHelpMessage($helpMessage)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setHelpMessage', array($helpMessage));

        return parent::setHelpMessage($helpMessage);
    }

    /**
     * {@inheritDoc}
     */
    public function getHelpMessage()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getHelpMessage', array());

        return parent::getHelpMessage();
    }

    /**
     * {@inheritDoc}
     */
    public function setIsCustom($isCustom)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setIsCustom', array($isCustom));

        return parent::setIsCustom($isCustom);
    }

    /**
     * {@inheritDoc}
     */
    public function getIsCustom()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getIsCustom', array());

        return parent::getIsCustom();
    }

    /**
     * {@inheritDoc}
     */
    public function isCustom()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isCustom', array());

        return parent::isCustom();
    }

    /**
     * {@inheritDoc}
     */
    public function setCustomParameters($customParameters)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCustomParameters', array($customParameters));

        return parent::setCustomParameters($customParameters);
    }

    /**
     * {@inheritDoc}
     */
    public function getCustomParameters()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCustomParameters', array());

        return parent::getCustomParameters();
    }

    /**
     * {@inheritDoc}
     */
    public function getSessionId()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSessionId', array());

        return parent::getSessionId();
    }

    /**
     * {@inheritDoc}
     */
    public function setSessionId($sessionId)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setSessionId', array($sessionId));

        return parent::setSessionId($sessionId);
    }

    /**
     * {@inheritDoc}
     */
    public function getLeadField()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getLeadField', array());

        return parent::getLeadField();
    }

    /**
     * {@inheritDoc}
     */
    public function setLeadField($leadField)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setLeadField', array($leadField));

        return parent::setLeadField($leadField);
    }

    /**
     * {@inheritDoc}
     */
    public function getSaveResult()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSaveResult', array());

        return parent::getSaveResult();
    }

    /**
     * {@inheritDoc}
     */
    public function setSaveResult($saveResult)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setSaveResult', array($saveResult));

        return parent::setSaveResult($saveResult);
    }

}
