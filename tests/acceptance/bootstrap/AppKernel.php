<?php

/**
 * TechDivision\Import\Cli\Simple\AppKernel
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Cli\Simple;

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;

/**
 * The symfony app kernel implementation for the M2IF import acceptance testsuite.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class AppKernel extends Kernel
{

    /**
     * Returns an array of bundles to register.
     *
     * @return iterable|\Symfony\Component\HttpKernel\Bundle\BundleInterface[] An iterable of bundle instances
     * @see \Symfony\Component\HttpKernel\KernelInterface::registerBundles()
     */
    public function registerBundles()
    {

        // load the environment variables
        $dotenv = new Dotenv();
        $dotenv->loadEnv(__DIR__.'/.env');

        // return the registered bundles
        return array(
            new FrameworkBundle(),
            new ImportBundle()
        );
    }

    /**
     * Loads the container configuration.
     *
     * @return void
     * @see \Symfony\Component\HttpKernel\KernelInterface::registerContainerConfiguration()
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/Resources/config/config_'.$this->getEnvironment().'.yml');
    }
}