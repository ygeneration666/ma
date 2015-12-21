<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\CoreBundle\Helper;

use Mautic\CoreBundle\Factory\MauticFactory;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class CacheHelper
 *
 * @package Mautic\CoreBundle\Helper
 */
class CacheHelper
{
    protected $factory;

    protected $cacheDir;

    protected $env;

    /**
     * @param MauticFactory $factory
     */
    public function __construct(MauticFactory $factory)
    {
        $this->factory  = $factory;
        $this->cacheDir = $factory->getSystemPath('cache', true);
        $this->env      = $factory->getEnvironment();
    }

    /**
     * Clear the application cache and run the warmup routine for the current environment
     *
     * @return void
     */
    public function clearCache()
    {
        $this->clearSessionItems();

        $memoryLimit = ini_get('memory_limit');
        if ((int) substr($memoryLimit, 0, -1) < 128) {
            ini_set('memory_limit', '128M');
        }

        $this->clearOpcaches();

        //attempt to squash command output
        ob_start();

        $args = array('console', 'cache:clear', '--env=' . $this->env);

        if ($this->env == 'prod') {
            $args[] = '--no-debug';
        }

        $input       = new ArgvInput($args);
        $application = new Application($this->factory->getKernel());
        $application->setAutoExit(false);
        $output      = new NullOutput();
        $application->run($input, $output);

        if (ob_get_length() > 0) {
            ob_end_clean();
        }
    }

    /**
     * Deletes the cache folder
     */
    public function nukeCache()
    {
        $this->clearSessionItems();

        $fs = new Filesystem();
        $fs->remove($this->cacheDir);
    }

    /**
     * Delete's the file Symfony caches settings in
     */
    public function clearContainerFile()
    {
        $this->clearOpcaches(true);

        $containerFile = $this->factory->getKernel()->getContainerFile();

        if (file_exists($containerFile)) {
            unlink($containerFile);
        }
    }

    /**
     * Clears the cache for translations
     *
     * @param null $locale
     */
    public function clearTranslationCache($locale = null)
    {
        if ($locale) {
            $localeCache = $this->cacheDir . '/translations/catalogue.' . $locale . '.php';
            if (file_exists($localeCache)) {
                unlink($localeCache);
            }
        } else {
            $fs = new Filesystem();
            $fs->remove($this->cacheDir . '/translations');
        }
    }

    /**
     * Clears the cache for routing
     */
    public function clearRoutingCache()
    {
        $unlink = array(
            $this->factory->getKernel()->getContainer()->getParameter('router.options.generator.cache_class'),
            $this->factory->getKernel()->getContainer()->getParameter('router.options.matcher.cache_class')
        );

        foreach ($unlink as $file) {
            if (file_exists($this->cacheDir.'/'.$file.'.php')) {
                unlink($this->cacheDir.'/'.$file.'.php');
            }
        }
    }

    /**
     * Clear cache related session items
     */
    protected function clearSessionItems()
    {
        // Clear the menu items and icons so they can be rebuilt
        $session = $this->factory->getSession();
        $session->remove('mautic.menu.items');
        $session->remove('mautic.menu.icons');
    }

    /**
     * Clear opcaches
     *
     * @param bool|false $configSave
     */
    protected function clearOpcaches($configSave = false)
    {
        // Clear opcaches before rebuilding the cache to ensure latest filechanges are used
        if (function_exists('opcache_reset')) {
            if ($configSave) {
                // Clear the cached config file
                $configFile = $this->factory->getLocalConfigFile(false);
                opcache_invalidate($configFile);
            } else {
                // Clear the entire cache as anything could have been affected
                opcache_reset();
            }
        }

        if (function_exists('apc_clear_cache')) {
            apc_clear_cache('user');
        }
    }
}