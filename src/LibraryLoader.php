<?php

/**
 * TechDivision\Import\Cli\LibraryLoader
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

namespace TechDivision\Import\Cli;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use TechDivision\Import\ConfigurationInterface;
use TechDivision\Import\Cli\Command\ImportCommandInterface;

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
     * The DI container instance.
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * The configuration instance.
     *
     * @var \TechDivision\Import\ConfigurationInterface
     */
    protected $configuration;

    /**
     * The constructor to initialize the instance.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container     The DI container instance
     * @param \TechDivision\Import\ConfigurationInterface               $configuration The configuration instance
     */
    public function __construct(
        ContainerInterface $container,
        ConfigurationInterface $configuration
    ) {
        $this->container = $container;
        $this->configuration = $configuration;
    }

    /**
     * Factory implementation to create a new initialized configuration instance.
     *
     * @param \TechDivision\Import\Cli\Command\ImportCommandInterface $command The import command instance
     *
     * @return void
     */
    public function load(ImportCommandInterface $command)
    {


        // initialize the default loader and load the DI configuration for the this library
        $defaultLoader = new XmlFileLoader($this->container, new FileLocator($vendorDir));

        // load the DI configuration for all the extension libraries
        foreach ($this->configuration->getExtensionLibraries() as $library) {
            if (file_exists($diConfiguration = sprintf('%s/%s/symfony/Resources/config/services.xml', $command->getVendorDir(), $library))) {
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
        $customLoader = new XmlFileLoader($this->container, new FileLocator());
        foreach ($this->configuration->getAdditionalVendorDirs() as $additionalVendorDir) {
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
