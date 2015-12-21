<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\PluginBundle\Form\Type;

use Mautic\CoreBundle\Helper\InputHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class SocialMediaServiceType
 *
 * @package Mautic\FormBundle\Form\Type
 */
class FieldsType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm (FormBuilderInterface $builder, array $options)
    {
        foreach ($options['integration_fields'] as $field => $details) {
            $label = (is_array($details)) ? $details['label'] : $details;
            $field = InputHelper::alphanum($field, false, '_');

            $builder->add($field, 'choice', array(
                'choices'    => $options['lead_fields'],
                'label'      => $label,
                'required'   => (is_array($details) && isset($details['required'])) ? $details['required'] : false,
                'label_attr' => array('class' => 'control-label'),
                'attr'       => array('class' => 'form-control', 'data-placeholder' => ' ')
            ));
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array('integration_fields', 'lead_fields'));
        $resolver->setDefaults(array(
            'special_instructions' => '',
            'alert_type'           => ''
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return "integration_fields";
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['specialInstructions'] = $options['special_instructions'];
        $view->vars['alertType']           = $options['alert_type'];
    }
}