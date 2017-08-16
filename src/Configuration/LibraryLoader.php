<?php

/**
 * TechDivision\Import\Cli\Configuration\LibraryLoader
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
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Cli\Configuration;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use TechDivision\Import\ConfigurationInterface;
use TechDivision\Import\Cli\Utils\DependencyInjectionKeys;

/**
 * The library loader implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class LibraryLoader
{

    /**
     * The container instance.
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * Initializes the configuration loader.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container The container instance
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container= $container;
    }

    /**
     * Return's the DI container instance.
     *
     * @return \Symfony\Component\DependencyInjection\ContainerInterface The DI container instance
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * Return's the absolute path to the actual vendor directory.
     *
     * @return string The absolute path to the actual vendor directory
     * @throws \Exception Is thrown, if none of the possible vendor directories can be found
     */
    protected function getVendorDir()
    {
        return $this->getContainer()->getParameter(DependencyInjectionKeys::CONFIGURATION_VENDOR_DIR);
    }

    /**
     * Load's the external libraries registered in the passed configuration.
     *
     * @param \TechDivision\Import\ConfigurationInterface $configuration The configuration instance
     *
     * @return void
     */
    public function load(ConfigurationInterface $configuration)
    {

        // initialize the default loader and load the DI configuration for the this library
        $defaultLoader = new XmlFileLoader($this->getContainer(), new FileLocator($this->getVendorDir()));

        // load the DI configuration for all the extension libraries
        foreach ($configuration->getExtensionLibraries() as $library) {
            if (file_exists($diConfiguration = sprintf('%s/%s/symfony/Resources/config/services.xml', $this->getVendorDir(), $library))) {
                $defaultLoader->load($diConfiguration);
            } else {
                throw new \Exception(
                    sprintf(
                        'Can\'t load DI configuration for library "%s"',
                        $diConfiguration
                    )
                );
            }
        }

        // register autoloaders for additional vendor directories
        $customLoader = new XmlFileLoader($this->getContainer(), new FileLocator());
        foreach ($configuration->getAdditionalVendorDirs() as $additionalVendorDir) {
            // load the vendor directory's auto loader
            if (file_exists($autoLoader = $additionalVendorDir->getVendorDir() . '/autoload.php')) {
                require $autoLoader;
            } else {
                throw new \Exception(
                    sprintf(
                        'Can\'t find autoloader in configured additional vendor directory "%s"',
                        $additionalVendorDir->getVendorDir()
                    )
                );
            }

            // try to load the DI configuration for the configured extension libraries
            foreach ($additionalVendorDir->getLibraries() as $library) {
                // prepare the DI configuration filename
                $diConfiguration = realpath(sprintf('%s/%s/symfony/Resources/config/services.xml', $additionalVendorDir->getVendorDir(), $library));
                // try to load the filename
                if (file_exists($diConfiguration)) {
                    $customLoader->load($diConfiguration);
                } else {
                    throw new \Exception(
                        sprintf(
                            'Can\'t load DI configuration for library "%s"',
                            $diConfiguration
                        )
                    );
                }
            }
        }
    }
}
