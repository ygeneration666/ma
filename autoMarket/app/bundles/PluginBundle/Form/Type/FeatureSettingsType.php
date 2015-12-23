<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\PluginBundle\Form\Type;

use Mautic\CoreBundle\Factory\MauticFactory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class FeatureSettingsType
 *
 * @package Mautic\PluginBundle\Form\Type
 */
class FeatureSettingsType extends AbstractType
{

    /**
     * @param MauticFactory $factory
     */
    public function __construct (MauticFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm (FormBuilderInterface $builder, array $options)
    {

        $integration_object = $options['integration_object'];

        //add custom feature settings
        $integration_object->appendToForm($builder, $options['data'], 'features');
        $leadFields = $options['lead_fields'];

        $formModifier = function (FormInterface $form, $data, $method = 'get') use ($integration_object, $leadFields) {

            $settings = array(
                'silence_exceptions' => false,
                'feature_settings'   => $data
            );
            try {
                $fields = $integration_object->getFormLeadFields($settings);
                if (!is_array($fields)) {
                    $fields = array();
                }
                $error = '';
            } catch (\Exception $e) {
                $fields = array();
                $error  = $e->getMessage();
            }

            list ($specialInstructions, $alertType) = $integration_object->getFormNotes('leadfield_match');

            $form->add('leadFields', 'integration_fields', array(
                'label'                => 'mautic.integration.leadfield_matches',
                'required'             => true,
                'lead_fields'          => $leadFields,
                'data'                 => isset($data['leadFields']) ? $data['leadFields'] : array(),
                'integration_fields'   => $fields,
                'special_instructions' => $specialInstructions,
                'alert_type'           => $alertType
            ));

            if ($method == 'get' && $error) {
                $form->addError(new FormError($error));
            }
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {

                $data = $event->getData();
                $formModifier($event->getForm(), $data);
            }
        );

        $builder->addEventListener(FormEvents::PRE_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $data = $event->getData();
                $formModifier($event->getForm(), $data, 'post');
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions (OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array('integration', 'integration_object', 'lead_fields'));
    }

    /**
     * {@inheritdoc}
     */
    public function getName ()
    {
        return "integration_featuresettings";
    }
}