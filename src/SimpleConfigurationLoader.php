<?php

/**
 * TechDivision\Import\Cli\SimpleConfigurationLoader
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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TechDivision\Import\ConfigurationFactoryInterface;
use TechDivision\Import\Cli\Command\InputOptionKeys;
use TechDivision\Import\Cli\Configuration\LibraryLoader;
use TechDivision\Import\Cli\Utils\DependencyInjectionKeys;
use TechDivision\Import\Cli\Utils\MagentoConfigurationKeys;
use TechDivision\Import\Utils\CommandNames;
use TechDivision\Import\Utils\EntityTypeCodes;
use TechDivision\Import\Utils\Mappings\CommandNameToEntityTypeCode;

/**
 * The configuration loader implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class SimpleConfigurationLoader implements ConfigurationLoaderInterface
{

    /**
     * The composer name => Magento Edition mappings.
     *
     * @var array
     */
    protected $editionMappings = array(
        'magento2ce'                 => 'CE',
        'project-community-edition'  => 'CE',
        'magento2ee'                 => 'EE',
        'project-enterprise-edition' => 'EE'
    );

    /**
     * The array with the default entity type => configuration filename mapping.
     *
     * @var array
     */
    protected $configurationFileMappings = array(
        EntityTypeCodes::NONE                          => 'techdivision-import',
        EntityTypeCodes::EAV_ATTRIBUTE                 => 'techdivision-import',
        EntityTypeCodes::EAV_ATTRIBUTE_SET             => 'techdivision-import',
        EntityTypeCodes::CATALOG_PRODUCT               => 'techdivision-import',
        EntityTypeCodes::CATALOG_PRODUCT_PRICE         => 'techdivision-import-price',
        EntityTypeCodes::CATALOG_PRODUCT_TIER_PRICE    => 'techdivision-import',
        EntityTypeCodes::CATALOG_PRODUCT_INVENTORY     => 'techdivision-import-inventory',
        EntityTypeCodes::CATALOG_PRODUCT_INVENTORY_MSI => 'techdivision-import',
        EntityTypeCodes::CATALOG_CATEGORY              => 'techdivision-import',
        EntityTypeCodes::CUSTOMER                      => 'techdivision-import',
        EntityTypeCodes::CUSTOMER_ADDRESS              => 'techdivision-import'
    );

    /**
     * The array with the entity type => repository with default configuration file mapping.
     *
     * @var array
     */
    protected $defaultConfigurations = array(
        'ce' => array(
            EntityTypeCodes::NONE                          => 'techdivision/import-product',
            EntityTypeCodes::EAV_ATTRIBUTE                 => 'techdivision/import-attribute',
            EntityTypeCodes::EAV_ATTRIBUTE_SET             => 'techdivision/import-attribute-set',
            EntityTypeCodes::CATALOG_PRODUCT               => 'techdivision/import-product',
            EntityTypeCodes::CATALOG_PRODUCT_PRICE         => 'techdivision/import-product',
            EntityTypeCodes::CATALOG_PRODUCT_TIER_PRICE    => 'techdivision/import-product-tier-price',
            EntityTypeCodes::CATALOG_PRODUCT_INVENTORY     => 'techdivision/import-product',
            EntityTypeCodes::CATALOG_PRODUCT_INVENTORY_MSI => 'techdivision/import-product-msi',
            EntityTypeCodes::CATALOG_CATEGORY              => 'techdivision/import-category',
            EntityTypeCodes::CUSTOMER                      => 'techdivision/import-customer',
            EntityTypeCodes::CUSTOMER_ADDRESS              => 'techdivision/import-customer-address'
        ),
        'ee' => array(
            EntityTypeCodes::NONE                          => 'techdivision/import-product-ee',
            EntityTypeCodes::EAV_ATTRIBUTE                 => 'techdivision/import-attribute',
            EntityTypeCodes::EAV_ATTRIBUTE_SET             => 'techdivision/import-attribute-set',
            EntityTypeCodes::CATALOG_PRODUCT               => 'techdivision/import-product-ee',
            EntityTypeCodes::CATALOG_PRODUCT_PRICE         => 'techdivision/import-product-ee',
            EntityTypeCodes::CATALOG_PRODUCT_TIER_PRICE    => 'techdivision/import-product-tier-price',
            EntityTypeCodes::CATALOG_PRODUCT_INVENTORY     => 'techdivision/import-product-ee',
            EntityTypeCodes::CATALOG_PRODUCT_INVENTORY_MSI => 'techdivision/import-product-msi',
            EntityTypeCodes::CATALOG_CATEGORY              => 'techdivision/import-category-ee',
            EntityTypeCodes::CUSTOMER                      => 'techdivision/import-customer',
            EntityTypeCodes::CUSTOMER_ADDRESS              => 'techdivision/import-customer-address'
        )
    );

    /**
     * The container instance.
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * The actual input instance.
     *
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    protected $input;

    /**
     * The library loader instance.
     *
     * @param \TechDivision\Import\Cli\LibraryLoader
     */
    protected $libraryLoader;

    /**
     * The configuration factory instance.
     *
     * @var \TechDivision\Import\ConfigurationFactoryInterface
     */
    protected $configurationFactory;

    /**
     * The available command names.
     *
     * @var \TechDivision\Import\Utils\CommandNames
     */
    protected $commandNames;

    /**
     * The mapping of the command names to the entity type codes
     *
     * @var \TechDivision\Import\Utils\Mappings\CommandNameToEntityTypeCode
     */
    protected $commandNameToEntityTypeCode;

    /**
     * Initializes the configuration loader.
     *
     * @param \Symfony\Component\Console\Input\InputInterface                 $input                        The input instance
     * @param \Symfony\Component\DependencyInjection\ContainerInterface       $container                    The container instance
     * @param \TechDivision\Import\Cli\Configuration\LibraryLoader            $libraryLoader                The configuration loader instance
     * @param \TechDivision\Import\ConfigurationFactoryInterface              $configurationFactory         The configuration factory instance
     * @param \TechDivision\Import\Utils\CommandNames                         $commandNames                 The available command names
     * @param \TechDivision\Import\Utils\Mappings\CommandNameToEntityTypeCode $commandNameToEntityTypeCodes The mapping of the command names to the entity type codes
     */
    public function __construct(
        InputInterface $input,
        ContainerInterface $container,
        LibraryLoader $libraryLoader,
        ConfigurationFactoryInterface $configurationFactory,
        CommandNames $commandNames,
        CommandNameToEntityTypeCode $commandNameToEntityTypeCodes
    ) {

        // set the passed instances
        $this->input = $input;
        $this->container = $container;
        $this->libraryLoader = $libraryLoader;
        $this->configurationFactory = $configurationFactory;
        $this->commandNames = $commandNames;
        $this->commandNameToEntityTypeCode = $commandNameToEntityTypeCodes;
    }

    /**
     * Factory implementation to create a new initialized configuration instance.
     *
     * If command line options are specified, they will always override the
     * values found in the configuration file.
     *
     * @return \TechDivision\Import\ConfigurationInterface The configuration instance
     * @throws \Exception Is thrown, if the specified configuration file doesn't exist or the mandatory arguments/options to run the requested operation are not available
     */
    public function load()
    {

        // load the actual vendor directory and entity type code
        $vendorDir = $this->getVendorDir();

        // the path of the JMS serializer directory, relative to the vendor directory
        $jmsDir = DIRECTORY_SEPARATOR . 'jms' . DIRECTORY_SEPARATOR . 'serializer' . DIRECTORY_SEPARATOR . 'src';

        // try to find the path to the JMS Serializer annotations
        if (!file_exists($annotationDir = $vendorDir . DIRECTORY_SEPARATOR . $jmsDir)) {
            // stop processing, if the JMS annotations can't be found
            throw new \Exception(
                sprintf(
                    'The jms/serializer libarary can not be found in one of "%s"',
                    implode(', ', $vendorDir)
                )
            );
        }

        // register the autoloader for the JMS serializer annotations
        \Doctrine\Common\Annotations\AnnotationRegistry::registerAutoloadNamespace(
            'JMS\Serializer\Annotation',
            $annotationDir
        );

        // query whether or not, a configuration file has been specified
        if ($configuration = $this->input->getOption(InputOptionKeys::CONFIGURATION)) {
            // load the configuration from the file with the given filename
            $instance = $this->createConfiguration($configuration);
        } elseif ($magentoEdition = $this->input->getOption(InputOptionKeys::MAGENTO_EDITION)) {
            // use the Magento Edition that has been specified as option
            $instance = $this->createConfiguration($this->getDefaultConfiguration($magentoEdition, $this->getEntityTypeCode()));

            // override the Magento Edition
            $instance->setMagentoEdition($magentoEdition);
        } else {
            // finally, query whether or not the installation directory is a valid Magento root directory
            if (!$this->isMagentoRootDir($installationDir = $this->input->getOption(InputOptionKeys::INSTALLATION_DIR))) {
                throw new \Exception(
                    sprintf(
                        'Directory "%s" specified with option "--installation-dir" is not a valid Magento root directory',
                        $installationDir
                    )
                );
            }

            // load the composer file from the Magento root directory
            $composer = json_decode(file_get_contents($composerFile = sprintf('%s/composer.json', $installationDir)), true);

            // try to load and explode the Magento Edition identifier from the Composer name
            $explodedVersion = explode('/', $composer[MagentoConfigurationKeys::COMPOSER_EDITION_NAME_ATTRIBUTE]);

            // try to locate Magento Edition
            if (!isset($this->editionMappings[$possibleEdition = end($explodedVersion)])) {
                throw new \Exception(
                    sprintf(
                        '"%s" detected in "%s" is not a valid Magento Edition, please set Magento Edition with the "--magento-edition" option',
                        $possibleEdition,
                        $composerFile
                    )
                );
            }

            // if Magento Edition/Version are available, load them
            $magentoEdition = $this->editionMappings[$possibleEdition];

            // use the Magento Edition that has been detected by the installation directory
            $instance = $this->createConfiguration($this->getDefaultConfiguration($magentoEdition, $this->getEntityTypeCode()));

            // override the Magento Edition, if NOT explicitly specified
            $instance->setMagentoEdition($magentoEdition);
        }

        // query whether or not a system name has been specified as command line option, if yes override the value from the configuration file
        if (($this->input->hasOptionSpecified(InputOptionKeys::SYSTEM_NAME) && $this->input->getOption(InputOptionKeys::SYSTEM_NAME)) || $instance->getSystemName() === null) {
            $instance->setSystemName($this->input->getOption(InputOptionKeys::SYSTEM_NAME));
        }

        // query whether or not a PID filename has been specified as command line option, if yes override the value from the configuration file
        if (($this->input->hasOptionSpecified(InputOptionKeys::PID_FILENAME) && $this->input->getOption(InputOptionKeys::PID_FILENAME)) || $instance->getPidFilename() === null) {
            $instance->setPidFilename($this->input->getOption(InputOptionKeys::PID_FILENAME));
        }

        // query whether or not a Magento installation directory has been specified as command line option, if yes override the value from the configuration file
        if (($this->input->hasOptionSpecified(InputOptionKeys::INSTALLATION_DIR) && $this->input->getOption(InputOptionKeys::INSTALLATION_DIR)) || $instance->getInstallationDir() === null) {
            $instance->setInstallationDir($this->input->getOption(InputOptionKeys::INSTALLATION_DIR));
        }

        // query whether or not a Magento edition has been specified as command line option, if yes override the value from the configuration file
        if (($this->input->hasOptionSpecified(InputOptionKeys::MAGENTO_EDITION) && $this->input->getOption(InputOptionKeys::MAGENTO_EDITION)) || $instance->getMagentoEdition() === null) {
            $instance->setMagentoEdition($this->input->getOption(InputOptionKeys::MAGENTO_EDITION));
        }

        // query whether or not a directory for the source files has been specified as command line option, if yes override the value from the configuration file
        if (($this->input->hasOptionSpecified(InputOptionKeys::SOURCE_DIR) && $this->input->getOption(InputOptionKeys::SOURCE_DIR)) || $instance->getSourceDir() === null) {
            $instance->setSourceDir($this->input->getOption(InputOptionKeys::SOURCE_DIR));
        }

        // return the initialized configuration instance
        return $instance;
    }

    /**
     * Create and return a new configuration instance from the passed configuration filename
     * after merging additional specified params from the commandline.
     *
     * @param string $filename The configuration filename to use
     *
     * @return \TechDivision\Import\ConfigurationInterface The configuration instance
     */
    protected function createConfiguration($filename)
    {

        // initialize the params specified with the --params parameter
        $params = null;

        // try to load the params from the commandline
        if ($this->input->hasOptionSpecified(InputOptionKeys::PARAMS) && $this->input->getOption(InputOptionKeys::PARAMS)) {
            $params = $this->input->getOption(InputOptionKeys::PARAMS);
        }

        // initialize the params file specified with the --params-file parameter
        $paramsFile = null;

        // try to load the path of the params file from the commandline
        if ($this->input->hasOptionSpecified(InputOptionKeys::PARAMS_FILE) && $this->input->getOption(InputOptionKeys::PARAMS_FILE)) {
            $paramsFile = $this->input->getOption(InputOptionKeys::PARAMS_FILE);
        }

        // create the configuration and return it
        return $this->configurationFactory->factory($filename, pathinfo($filename, PATHINFO_EXTENSION), $params, $paramsFile);
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
     * Return's the actual command name.
     *
     * @return string The actual command name
     */
    protected function getCommandName()
    {
        return $this->input->getArgument('command');
    }

    /**
     * Return's the command's entity type code.
     *
     * @return string The command's entity type code
     * @throws \Exception Is thrown, if the command name can not be mapped
     */
    protected function getEntityTypeCode()
    {

        // try to map the command name to a entity type code
        if (array_key_exists($commandName = $this->getCommandName(), (array) $this->commandNameToEntityTypeCode)) {
            return $this->commandNameToEntityTypeCode[$commandName];
        }

        // throw an exception if not possible
        throw new \Exception(sprintf('Can\' map command name %s to a entity type', $commandName));
    }

    /**
     * Return's the default configuration for the passed Magento Edition and the actual entity type.
     *
     * @param string $magentoEdition The Magento Edition to return the configuration for
     * @param string $entityTypeCode The entity type code to use
     *
     * @return string The path to the default configuration
     */
    protected function getDefaultConfiguration($magentoEdition, $entityTypeCode)
    {
        return sprintf(
            '%s/%s/etc/%s.json',
            $this->getVendorDir(),
            $this->getDefaultConfigurationLibrary(
                $magentoEdition,
                $entityTypeCode
            ),
            $this->getDefaultConfigurationFile($entityTypeCode)
        );
    }

    /**
     * Return's the name of the default configuration file.
     *
     * @param string $entityTypeCode The entity type code to return the default configuration file for
     *
     * @return string The name of the entity type's default configuration file
     * @throws \Exception
     */
    protected function getDefaultConfigurationFile($entityTypeCode)
    {

        // query whether or not a default configuration file for the passed entity type code exists
        if (isset($this->configurationFileMappings[$entityTypeCode])) {
            return $this->configurationFileMappings[$entityTypeCode];
        }

        // throw an exception, if no default configuration file for the passed entity type is available
        throw new \Exception(
            sprintf(
                'Can\'t find a default configuration file for entity Type Code \'%s\' (MUST be one of catalog_product, catalog_product_price, catalog_product_inventory, catalog_category or eav_attribute)',
                $entityTypeCode
            )
        );
    }

    /**
     * Return's the Magento Edition and entity type's specific default library that contains
     * the configuration file.
     *
     * @param string $magentoEdition The Magento Edition to return the default library for
     * @param string $entityTypeCode The entity type code to return the default library file for
     *
     * @return string The name of the library that contains the default configuration file for the passed Magento Edition and entity type code
     * @throws \Exception Is thrown, if no default configuration for the passed entity type code is available
     */
    protected function getDefaultConfigurationLibrary($magentoEdition, $entityTypeCode)
    {

        // query whether or not, a default configuration file for the passed entity type is available
        if (isset($this->defaultConfigurations[$edition = strtolower($magentoEdition)])) {
            if (isset($this->defaultConfigurations[$edition][$entityTypeCode])) {
                return $this->defaultConfigurations[$edition][$entityTypeCode];
            }

            // throw an exception, if the passed entity type is not supported
            throw new \Exception(
                sprintf(
                    'Entity Type Code \'%s\' not supported by entity type code \'%s\' (MUST be one of catalog_product, catalog_category or eav_attribute)',
                    $edition,
                    $entityTypeCode
                )
            );
        }

        // throw an exception, if the passed edition is not supported
        throw new \Exception(
            sprintf(
                'Default configuration for Magento \'%s\' not supported (MUST be one of CE or EE)',
                $magentoEdition
            )
        );
    }
}
