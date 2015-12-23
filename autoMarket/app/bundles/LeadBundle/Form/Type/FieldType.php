<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\LeadBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Mautic\CoreBundle\Factory\MauticFactory;
use Mautic\CoreBundle\Form\EventListener\CleanFormSubscriber;
use Mautic\CoreBundle\Form\EventListener\FormExitSubscriber;
use Mautic\LeadBundle\Form\DataTransformer\FieldToOrderTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Mautic\LeadBundle\Helper\FormFieldHelper;

/**
 * Class FieldType
 *
 * @package Mautic\LeadBundle\Form\Type
 */
class FieldType extends AbstractType
{

    private $translator;
    private $em;

    /**
     * @param MauticFactory $factory
     */
    public function __construct(MauticFactory $factory)
    {
        $this->translator = $factory->getTranslator();
        $this->em         = $factory->getEntityManager();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new CleanFormSubscriber());
        $builder->addEventSubscriber(new FormExitSubscriber('lead.field', $options));

        $builder->add(
            'label',
            'text',
            array(
                'label'      => 'mautic.lead.field.label',
                'label_attr' => array('class' => 'control-label'),
                'attr'       => array('class' => 'form-control', 'length' => 50)
            )
        );

        $disabled = (!empty($options['data'])) ? $options['data']->isFixed() : false;

        $builder->add(
            'group',
            'choice',
            array(
                'choices'     => array(
                    'core'         => 'mautic.lead.field.group.core',
                    'social'       => 'mautic.lead.field.group.social',
                    'personal'     => 'mautic.lead.field.group.personal',
                    'professional' => 'mautic.lead.field.group.professional'
                ),
                'attr'        => array(
                    'class'   => 'form-control',
                    'tooltip' => 'mautic.lead.field.form.group.help'
                ),
                'expanded'    => false,
                'multiple'    => false,
                'label'       => 'mautic.lead.field.group',
                'empty_value' => false,
                'required'    => false,
                'disabled'    => $disabled
            )
        );

        $new         = (!empty($options['data']) && $options['data']->getAlias()) ? false : true;
        $default     = ($new) ? 'text' : $options['data']->getType();
        $fieldHelper = new FormFieldHelper();
        $fieldHelper->setTranslator($this->translator);
        $builder->add(
            'type',
            'choice',
            array(
                'choices'     => $fieldHelper->getChoiceList(),
                'expanded'    => false,
                'multiple'    => false,
                'label'       => 'mautic.lead.field.type',
                'empty_value' => false,
                'disabled'    => ($disabled || !$new),
                'attr'        => array(
                    'class'    => 'form-control',
                    'onchange' => 'Mautic.updateLeadFieldProperties(this.value);'
                ),
                'data'        => $default,
                'required'    => false
            )
        );

        $builder->add(
            'properties_select_template',
            'sortablelist',
            array(
                'mapped'          => false,
                'label'           => 'mautic.lead.field.form.properties.select',
                'option_required' => false
            )
        );

        $builder->add(
            'default_template',
            'text',
            array(
                'label'      => 'mautic.core.defaultvalue',
                'label_attr' => array('class' => 'control-label'),
                'attr'       => array('class' => 'form-control'),
                'required'   => false,
                'mapped'     => false
            )
        );

        $builder->add(
            'default_bool_template',
            'yesno_button_group',
            array(
                'label'      => 'mautic.core.defaultvalue',
                'label_attr' => array('class' => 'control-label'),
                'attr'       => array('class' => 'form-control'),
                'required'   => false,
                'mapped'     => false,
                'data'       => false
            )
        );

        $builder->add(
            'properties',
            'collection',
            array(
                'required'       => false,
                'allow_add'      => true,
                'error_bubbling' => false
            )
        );

        $builder->add(
            'defaultValue',
            'text',
            array(
                'label'      => 'mautic.core.defaultvalue',
                'label_attr' => array('class' => 'control-label'),
                'attr'       => array('class' => 'form-control'),
                'required'   => false
            )
        );

        $formModifier = function (FormEvent $event, $eventName) {
            $form = $event->getForm();
            $data = $event->getData();

            $type = (is_array($data)) ? (isset($data['type']) ? $data['type'] : null) : $data->getType();

            if ($type == 'select' || $type == 'lookup') {
                if (is_array($data) && isset($data['properties'])) {
                    $properties = $data['properties'];
                } else {
                    $properties = $data->getProperties();
                }

                if (isset($properties['list']) && is_string($properties['list'])) {
                    $properties['list'] = array_map('trim', explode('|', $properties['list']));
                }

                $form->add(
                    'properties',
                    'sortablelist',
                    array(
                        'required' => false,
                        'label'    => 'mautic.lead.field.form.properties.select',
                        'data'     => $properties
                    )
                );
            } elseif ($type == 'boolean') {
                if (is_array($data)) {
                    $value    = isset($data['defaultValue']) ? $data['defaultValue'] : false;
                    $yesLabel = !empty($data['properties']['yes']) ? $data['properties']['yes'] : 'matuic.core.form.yes';
                    $noLabel  = !empty($data['properties']['no']) ? $data['properties']['no'] : 'matuic.core.form.no';
                } else {
                    $value    = $data->getDefaultValue();
                    $props    = $data->getProperties();
                    $yesLabel = !empty($props['yes']) ? $props['yes'] : 'matuic.core.form.yes';
                    $noLabel  = !empty($props['no']) ? $props['no'] : 'matuic.core.form.no';
                }

                $form->add(
                    'defaultValue',
                    'yesno_button_group',
                    array(
                        'label'       => 'mautic.core.defaultvalue',
                        'label_attr'  => array('class' => 'control-label'),
                        'attr'        => array('class' => 'form-control'),
                        'required'    => false,
                        'data'        => !empty($value),
                        'choice_list' => new ChoiceList(
                            array(false, true),
                            array($noLabel, $yesLabel)
                        )
                    )
                );
            }
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $formModifier($event, FormEvents::PRE_SET_DATA);
            }
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $formModifier($event, FormEvents::PRE_SUBMIT);
            }
        );

        //get order list
        $transformer = new FieldToOrderTransformer($this->em);
        $builder->add(
            $builder->create(
                'order',
                'entity',
                array(
                    'label'         => 'mautic.core.order',
                    'class'         => 'MauticLeadBundle:LeadField',
                    'property'      => 'label',
                    'label_attr'    => array('class' => 'control-label'),
                    'attr'          => array('class' => 'form-control'),
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('f')
                            ->orderBy('f.order', 'ASC');
                    },
                    'required'      => false
                )
            )->addModelTransformer($transformer)
        );

        $builder->add(
            'alias',
            'text',
            array(
                'label'      => 'mautic.core.alias',
                'label_attr' => array('class' => 'control-label'),
                'attr'       => array(
                    'class'   => 'form-control',
                    'length'  => 25,
                    'tooltip' => 'mautic.lead.field.help.alias',
                ),
                'required'   => false,
                'disabled'   => ($disabled || !$new)
            )
        );

        $data = ($disabled) ? true : $options['data']->getIsPublished();
        $builder->add(
            'isPublished',
            'yesno_button_group',
            array(
                'disabled' => $disabled,
                'data'     => $data
            )
        );

        $builder->add(
            'isRequired',
            'yesno_button_group',
            array(
                'label' => 'mautic.core.required'
            )
        );

        $builder->add(
            'isVisible',
            'yesno_button_group',
            array(
                'label' => 'mautic.lead.field.form.isvisible'
            )
        );

        $builder->add(
            'isShortVisible',
            'yesno_button_group',
            array(
                'label' => 'mautic.lead.field.form.isshortvisible'
            )
        );

        $builder->add(
            'isListable',
            'yesno_button_group',
            array(
                'label' => 'mautic.lead.field.form.islistable'
            )
        );

        $builder->add(
            'isUniqueIdentifer',
            'yesno_button_group',
            array(
                'label'    => 'mautic.lead.field.form.isuniqueidentifer',
                'attr'     => array(
                    'tooltip' => 'mautic.lead.field.form.isuniqueidentifer.tooltip'
                ),
                'disabled' => ($options['data']->getId()) ? true : false
            )
        );

        $builder->add(
            'isPubliclyUpdatable',
            'yesno_button_group',
            array(
                'label' => 'mautic.lead.field.form.ispubliclyupdatable',
                'attr'  => array(
                    'tooltip' => 'mautic.lead.field.form.ispubliclyupdatable.tooltip'
                )
            )
        );

        $builder->add('buttons', 'form_buttons');

        if (!empty($options["action"])) {
            $builder->setAction($options["action"]);
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Mautic\LeadBundle\Entity\LeadField'
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return "leadfield";
    }
}
