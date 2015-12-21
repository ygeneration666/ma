<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\ApiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class ConfigType
 *
 * @package Mautic\ApiBundle\Form\Type
 */
class ConfigType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm (FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'api_enabled',
            'yesno_button_group',
            array(
                'label' => 'mautic.api.config.form.api.enabled',
                'data'  => (bool) $options['data']['api_enabled'],
                'attr'  => array(
                    'tooltip' => 'mautic.api.config.form.api.enabled.tooltip'
                )
            )
        );

        $builder->add(
            'api_oauth2_access_token_lifetime',
            'number',
            array(
                'label'       => 'mautic.api.config.form.api.oauth2_access_token_lifetime',
                'attr'        => array(
                    'tooltip'      => 'mautic.api.config.form.api.oauth2_access_token_lifetime.tooltip',
                    'class'        => 'form-control',
                    'data-show-on' => '{"config_apiconfig_api_enabled_1":"checked"}',
                ),
                'constraints' => array(
                    new NotBlank(
                        array(
                            'message' => 'mautic.core.value.required'
                        )
                    )
                )
            )
        );

        $builder->add(
            'api_oauth2_refresh_token_lifetime',
            'number',
            array(
                'label'       => 'mautic.api.config.form.api.oauth2_refresh_token_lifetime',
                'attr'        => array(
                    'tooltip'      => 'mautic.api.config.form.api.oauth2_refresh_token_lifetime.tooltip',
                    'class'        => 'form-control',
                    'data-show-on' => '{"config_apiconfig_api_enabled_1":"checked"}',
                ),
                'constraints' => array(
                    new NotBlank(
                        array(
                            'message' => 'mautic.core.value.required'
                        )
                    )
                )
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'apiconfig';
    }
}