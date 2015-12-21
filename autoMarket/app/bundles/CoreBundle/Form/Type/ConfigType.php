<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\CoreBundle\Form\Type;

use Mautic\CoreBundle\Factory\MauticFactory;
use Mautic\CoreBundle\Form\DataTransformer\ArrayLinebreakTransformer;
use Mautic\CoreBundle\Form\DataTransformer\ArrayStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class ConfigType
 *
 * @package Mautic\CoreBundle\Form\Type
 */
class ConfigType extends AbstractType
{

    /**
     * @var MauticFactory
     */
    private $factory;

    public function __construct(MauticFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'site_url',
            'text',
            array(
                'label'       => 'mautic.core.config.form.site.url',
                'label_attr'  => array('class' => 'control-label'),
                'attr'        => array(
                    'class'   => 'form-control',
                    'tooltip' => 'mautic.core.config.form.site.url.tooltip'
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
            'webroot',
            'page_list',
            array(
                'label'       => 'mautic.core.config.form.webroot',
                'label_attr'  => array('class' => 'control-label'),
                'attr'        => array(
                    'class'            => 'form-control',
                    'tooltip'          => 'mautic.core.config.form.webroot.tooltip',
                    'data-placeholder' => $this->factory->getTranslator()->trans('mautic.core.config.form.webroot.dashboard')
                ),
                'multiple'    => false,
                'empty_value' => '',
                'required'    => false
            )
        );

        $builder->add(
            'cache_path',
            'text',
            array(
                'label'       => 'mautic.core.config.form.cache.path',
                'label_attr'  => array('class' => 'control-label'),
                'attr'        => array(
                    'class'   => 'form-control',
                    'tooltip' => 'mautic.core.config.form.cache.path.tooltip'
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
            'log_path',
            'text',
            array(
                'label'       => 'mautic.core.config.form.log.path',
                'label_attr'  => array('class' => 'control-label'),
                'attr'        => array(
                    'class'   => 'form-control',
                    'tooltip' => 'mautic.core.config.form.log.path.tooltip'
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
            'image_path',
            'text',
            array(
                'label'       => 'mautic.core.config.form.image.path',
                'label_attr'  => array('class' => 'control-label'),
                'attr'        => array(
                    'class'   => 'form-control',
                    'tooltip' => 'mautic.core.config.form.image.path.tooltip'
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
            'theme',
            'theme_list',
            array(
                'label' => 'mautic.core.config.form.theme',
                'attr'  => array(
                    'class'   => 'form-control',
                    'tooltip' => 'mautic.page.form.template.help'
                )
            )
        );

        // Get the list of available languages
        /** @var \Mautic\CoreBundle\Helper\LanguageHelper $languageHelper */
        $languageHelper = $this->factory->getHelper('language');
        $languages      = $languageHelper->fetchLanguages(false, false);
        $langChoices    = array();

        foreach ($languages as $code => $langData) {
            $langChoices[$code] = $langData['name'];
        }

        $langChoices = array_merge($langChoices, $this->factory->getParameter('supported_languages'));

        // Alpha sort the languages by name
        asort($langChoices);

        $builder->add(
            'locale',
            'choice',
            array(
                'choices'     => $langChoices,
                'label'       => 'mautic.core.config.form.locale',
                'required'    => false,
                'attr'        => array(
                    'class'   => 'form-control',
                    'tooltip' => 'mautic.core.config.form.locale.tooltip'
                ),
                'empty_value' => false
            )
        );

        $arrayStringTransformer = new ArrayStringTransformer();
        $builder->add(
            $builder->create(
                'trusted_hosts',
                'text',
                array(
                    'label'      => 'mautic.core.config.form.trusted.hosts',
                    'label_attr' => array('class' => 'control-label'),
                    'attr'       => array(
                        'class'   => 'form-control',
                        'tooltip' => 'mautic.core.config.form.trusted.hosts.tooltip'
                    ),
                    'required'   => false
                )
            )->addViewTransformer($arrayStringTransformer)
        );

        $builder->add(
            $builder->create(
                'trusted_proxies',
                'text',
                array(
                    'label'      => 'mautic.core.config.form.trusted.proxies',
                    'label_attr' => array('class' => 'control-label'),
                    'attr'       => array(
                        'class'   => 'form-control',
                        'tooltip' => 'mautic.core.config.form.trusted.proxies.tooltip'
                    ),
                    'required'   => false
                )
            )->addViewTransformer($arrayStringTransformer)
        );

        $arrayLinebreakTransformer = new ArrayLinebreakTransformer();
        $builder->add(
            $builder->create(
                'do_not_track_ips',
                'textarea',
                array(
                    'label'      => 'mautic.core.config.form.do_not_track_ips',
                    'label_attr' => array('class' => 'control-label'),
                    'attr'       => array(
                        'class'   => 'form-control',
                        'tooltip' => 'mautic.core.config.form.do_not_track_ips.tooltip'
                    ),
                    'required'   => false
                )
            )->addViewTransformer($arrayLinebreakTransformer)
        );

        $builder->add(
            'rememberme_key',
            'text',
            array(
                'label'       => 'mautic.core.config.form.rememberme.key',
                'label_attr'  => array('class' => 'control-label'),
                'attr'        => array(
                    'class'   => 'form-control',
                    'tooltip' => 'mautic.core.config.form.rememberme.key.tooltip'
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
            'rememberme_lifetime',
            'text',
            array(
                'label'       => 'mautic.core.config.form.rememberme.lifetime',
                'label_attr'  => array('class' => 'control-label'),
                'attr'        => array(
                    'class'   => 'form-control',
                    'tooltip' => 'mautic.core.config.form.rememberme.lifetime.tooltip'
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
            'rememberme_path',
            'text',
            array(
                'label'       => 'mautic.core.config.form.rememberme.path',
                'label_attr'  => array('class' => 'control-label'),
                'attr'        => array(
                    'class'   => 'form-control',
                    'tooltip' => 'mautic.core.config.form.rememberme.path.tooltip'
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
            'rememberme_domain',
            'text',
            array(
                'label'      => 'mautic.core.config.form.rememberme.domain',
                'label_attr' => array('class' => 'control-label'),
                'attr'       => array(
                    'class'   => 'form-control',
                    'tooltip' => 'mautic.core.config.form.rememberme.domain.tooltip'
                ),
                'required'   => false
            )
        );

        $builder->add(
            'default_pagelimit',
            'choice',
            array(
                'choices'     => array(
                    5   => 'mautic.core.pagination.5',
                    10  => 'mautic.core.pagination.10',
                    15  => 'mautic.core.pagination.15',
                    20  => 'mautic.core.pagination.20',
                    25  => 'mautic.core.pagination.25',
                    30  => 'mautic.core.pagination.30',
                    50  => 'mautic.core.pagination.50',
                    100 => 'mautic.core.pagination.100'
                ),
                'expanded'    => false,
                'multiple'    => false,
                'label'       => 'mautic.core.config.form.default.pagelimit',
                'label_attr'  => array('class' => 'control-label'),
                'attr'        => array(
                    'class'   => 'form-control',
                    'tooltip' => 'mautic.core.config.form.default.pagelimit.tooltip'
                ),
                'required'    => false,
                'empty_value' => false
            )
        );

        $builder->add(
            'default_timezone',
            'timezone',
            array(
                'label'       => 'mautic.core.config.form.default.timezone',
                'label_attr'  => array('class' => 'control-label'),
                'attr'        => array(
                    'class'   => 'form-control',
                    'tooltip' => 'mautic.core.config.form.default.timezone.tooltip'
                ),
                'multiple'    => false,
                'empty_value' => 'mautic.user.user.form.defaulttimezone',
                'required'    => false
            )
        );

        $builder->add(
            'date_format_full',
            'text',
            array(
                'label'       => 'mautic.core.config.form.date.format.full',
                'label_attr'  => array('class' => 'control-label'),
                'attr'        => array(
                    'class'   => 'form-control',
                    'tooltip' => 'mautic.core.config.form.date.format.full.tooltip'
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
            'date_format_short',
            'text',
            array(
                'label'       => 'mautic.core.config.form.date.format.short',
                'label_attr'  => array('class' => 'control-label'),
                'attr'        => array(
                    'class'   => 'form-control',
                    'tooltip' => 'mautic.core.config.form.date.format.short.tooltip'
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
            'date_format_dateonly',
            'text',
            array(
                'label'       => 'mautic.core.config.form.date.format.dateonly',
                'label_attr'  => array('class' => 'control-label'),
                'attr'        => array(
                    'class'   => 'form-control',
                    'tooltip' => 'mautic.core.config.form.date.format.dateonly.tooltip'
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
            'date_format_timeonly',
            'text',
            array(
                'label'       => 'mautic.core.config.form.date.format.timeonly',
                'label_attr'  => array('class' => 'control-label'),
                'attr'        => array(
                    'class'   => 'form-control',
                    'tooltip' => 'mautic.core.config.form.date.format.timeonly.tooltip'
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

        // Search for IP Services
        $bundles = $this->factory->getMauticBundles(true);
        $choices = array();

        foreach ($bundles as $bundle) {
            if (isset($bundle['config']['ip_lookup_services'])) {
                foreach ($bundle['config']['ip_lookup_services'] as $service => $details) {
                    $choices[$service] = $details['display_name'];
                }
            }
        }

        natcasesort($choices);

        $builder->add(
            'ip_lookup_service',
            'choice',
            array(
                'choices'    => $choices,
                'label'      => 'mautic.core.config.form.ip.lookup.service',
                'label_attr' => array(
                    'class' => 'control-label'
                ),
                'required'   => false,
                'attr'       => array(
                    'class'   => 'form-control',
                    'tooltip' => 'mautic.core.config.form.ip.lookup.service.tooltip'
                )
            )
        );

        $builder->add(
            'ip_lookup_auth',
            'text',
            array(
                'label'      => 'mautic.core.config.form.ip.lookup.auth',
                'label_attr' => array('class' => 'control-label'),
                'attr'       => array(
                    'class'   => 'form-control',
                    'tooltip' => 'mautic.core.config.form.ip.lookup.auth.tooltip'
                ),
                'required'   => false
            )
        );

        $builder->add(
            'transifex_username',
            'text',
            array(
                'label'      => 'mautic.core.config.form.transifex.username',
                'label_attr' => array('class' => 'control-label'),
                'attr'       => array(
                    'class'        => 'form-control',
                    'tooltip'      => 'mautic.core.config.form.transifex.username.tooltip',
                    'autocomplete' => 'off'
                ),
                'required'   => false
            )
        );

        $builder->add(
            'transifex_password',
            'password',
            array(
                'label'      => 'mautic.core.config.form.transifex.password',
                'label_attr' => array('class' => 'control-label'),
                'attr'       => array(
                    'class'        => 'form-control',
                    'placeholder'  => 'mautic.user.user.form.passwordplaceholder',
                    'preaddon'     => 'fa fa-lock',
                    'tooltip'      => 'mautic.core.config.form.transifex.password.tooltip',
                    'autocomplete' => 'off'
                ),
                'required'   => false
            )
        );

        $builder->add(
            'update_stability',
            'choice',
            array(
                'choices'     => array(
                    'alpha'  => 'mautic.core.config.update_stability.alpha',
                    'beta'   => 'mautic.core.config.update_stability.beta',
                    'rc'     => 'mautic.core.config.update_stability.rc',
                    'stable' => 'mautic.core.config.update_stability.stable'
                ),
                'label'       => 'mautic.core.config.form.update.stability',
                'required'    => false,
                'attr'        => array(
                    'class'   => 'form-control',
                    'tooltip' => 'mautic.core.config.form.update.stability.tooltip'
                ),
                'empty_value' => false
            )
        );

        $builder->add(
            'cookie_path',
            'text',
            array(
                'label'       => 'mautic.core.config.form.cookie.path',
                'label_attr'  => array('class' => 'control-label'),
                'attr'        => array(
                    'class'   => 'form-control',
                    'tooltip' => 'mautic.core.config.form.cookie.path.tooltip'
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
            'cookie_domain',
            'text',
            array(
                'label'      => 'mautic.core.config.form.cookie.domain',
                'label_attr' => array('class' => 'control-label'),
                'attr'       => array(
                    'class'   => 'form-control',
                    'tooltip' => 'mautic.core.config.form.cookie.domain.tooltip'
                ),
                'required'   => false
            )
        );

        $builder->add(
            'cookie_secure',
            'yesno_button_group',
            array(
                'label'       => 'mautic.core.config.form.cookie.secure',
                'empty_value' => 'mautic.core.form.default',
                'data'        => (array_key_exists('cookie_secure', $options['data']) && !empty($options['data']['cookie_secure'])) ? true : false,
                'attr'        => array(
                    'tooltip' => 'mautic.core.config.form.cookie.secure.tooltip'
                )
            )
        );

        $builder->add(
            'cookie_httponly',
            'yesno_button_group',
            array(
                'label' => 'mautic.core.config.form.cookie.httponly',
                'data'  => (array_key_exists('cookie_httponly', $options['data']) && !empty($options['data']['cookie_httponly'])) ? true : false,
                'attr'  => array(
                    'tooltip' => 'mautic.core.config.form.cookie.httponly.tooltip'
                )
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'coreconfig';
    }
}
