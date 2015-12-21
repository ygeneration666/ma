<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\PluginBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class DetailsType
 *
 * @package Mautic\PluginBundle\Form\Type
 */
class DetailsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm (FormBuilderInterface $builder, array $options)
    {
        $builder->add('isPublished', 'yesno_button_group');

        $keys          = $options['integration_object']->getRequiredKeyFields();
        $decryptedKeys = $options['integration_object']->decryptApiKeys($options['data']->getApiKeys());

        $builder->add('apiKeys', 'integration_keys', array(
            'label'               => false,
            'integration_keys'    => $keys,
            'data'                => $decryptedKeys,
            'integration_object'  => $options['integration_object']
        ));

        $formSettings = $options['integration_object']->getFormSettings();
        if (!empty($formSettings['requires_authorization'])) {
            $disabled     = false;
            $label        = ($options['integration_object']->isAuthorized()) ? 'reauthorize' : 'authorize';

            $builder->add('authButton', 'standalone_button', array(
                'attr'     => array(
                    'class'   => 'btn btn-primary',
                    'onclick' => 'Mautic.initiateIntegrationAuthorization()',
                    'icon'    => 'fa fa-key'

                ),
                'label'    => 'mautic.integration.form.' . $label,
                'disabled' => $disabled
            ));
        }

        $features = $options['integration_object']->getSupportedFeatures();
        if (!empty($features)) {

            // Check to see if the integration is a new entry and thus not configured
            $configured      = $options['data']->getId() !== null;
            $enabledFeatures = $options['data']->getSupportedFeatures();
            $data            = ($configured) ? $enabledFeatures : $features;

            $labels = array();
            foreach ($features as $f) {
                $labels[] = 'mautic.integration.form.feature.' . $f;
            }

            $builder->add('supportedFeatures', 'choice', array(
                'choice_list' => new ChoiceList($features, $labels),
                'expanded'    => true,
                'label_attr'  => array('class' => 'control-label'),
                'multiple'    => true,
                'label'       => 'mautic.integration.form.features',
                'required'    => false,
                'data'        => $data
            ));

            $builder->add('featureSettings', 'integration_featuresettings', array(
                'label'              => 'mautic.integration.form.feature.settings',
                'required'           => true,
                'data'               => $options['data']->getFeatureSettings(),
                'label_attr'         => array('class' => 'control-label'),
                'integration'        => $options['integration'],
                'integration_object' => $options['integration_object'],
                'lead_fields'        => $options['lead_fields']
            ));
        };

        $builder->add('name', 'hidden', array('data' => $options['integration']));

        $builder->add('in_auth', 'hidden', array('mapped' => false));

        $builder->add('buttons', 'form_buttons', array(
            'apply_text' => false,
            'save_text'  => 'mautic.core.form.save'
        ));

        if (!empty($options['action'])) {
            $builder->setAction($options['action']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions (OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mautic\PluginBundle\Entity\Integration'
        ));

        $resolver->setRequired(array('integration', 'integration_object', 'lead_fields'));
    }

    /**
     * {@inheritdoc}
     */
    public function getName ()
    {
        return 'integration_details';
    }
}
