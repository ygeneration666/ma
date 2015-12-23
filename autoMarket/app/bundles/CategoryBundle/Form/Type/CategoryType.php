<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\CategoryBundle\Form\Type;

use Mautic\CoreBundle\Factory\MauticFactory;
use Mautic\CoreBundle\Form\EventListener\CleanFormSubscriber;
use Mautic\CoreBundle\Form\EventListener\FormExitSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class CategoryType
 *
 * @package Mautic\CategoryBundle\Form\Type
 */
class CategoryType extends AbstractType
{

    private $translator;
    private $session;

    /**
     * @param MauticFactory $factory
     */
    public function __construct(MauticFactory $factory) {
        $this->translator = $factory->getTranslator();
        $this->session = $factory->getSession();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm (FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new CleanFormSubscriber(array('content' => 'html')));
        $builder->addEventSubscriber(new FormExitSubscriber('category.category', $options));

        if ($options['data']->getId()) {
            // Edit existing category from category manager - do not allow to edit bundle
            $builder->add('bundle', 'hidden', array(
                'data' => $options['data']->getBundle()
            ));
        } elseif ($options['show_bundle_select'] == true) {
           // Create new category from category bundle - let user select the bundle
           $selected = $this->session->get('mautic.category.type', 'category');
           $builder->add('bundle', 'category_bundles_form', array(
               'label'      => 'mautic.core.type',
               'label_attr' => array('class' => 'control-label'),
               'attr'       => array('class' => 'form-control'),
               'required'   => true,
               'data'       => $selected
           ));
       } else {
            // Create new category directly from another bundle - preset bundle
            $builder->add('bundle', 'hidden', array(
                'data' => $options['bundle']
            ));
        }

        $builder->add('title', 'text', array(
            'label'      => 'mautic.core.title',
            'label_attr' => array('class' => 'control-label'),
            'attr'       => array('class' => 'form-control')
        ));

        $builder->add('description', 'text', array(
            'label'      => 'mautic.core.description',
            'label_attr' => array('class' => 'control-label'),
            'attr'       => array('class' => 'form-control'),
            'required'   => false
        ));

        $builder->add('alias', 'text', array(
            'label'      => 'mautic.core.alias',
            'label_attr' => array('class' => 'control-label'),
            'attr'       => array(
                'class'   => 'form-control',
                'tooltip' => 'mautic.category.form.alias.help',
            ),
            'required'   => false
        ));

        $builder->add('color', 'text', array(
            'label'      => 'mautic.core.color',
            'label_attr' => array('class' => 'control-label'),
            'attr'       => array(
                'class'       => 'form-control',
                'data-toggle' => 'color'
            ),
            'required'   => false
        ));

        $builder->add('isPublished', 'yesno_button_group');

        $builder->add('inForm', 'hidden', array(
            'mapped' => false
        ));

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
        $resolver->setDefaults(array(
            'data_class' => 'Mautic\CategoryBundle\Entity\Category',
            'show_bundle_select' => false
        ));

        $resolver->setRequired(array('bundle'));
    }

    /**
     * @return string
     */
    public function getName() {
        return "category_form";
    }
}
