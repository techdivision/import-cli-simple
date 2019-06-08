<?php

/**
 * TechDivision\Import\Cli\ConfigurationLoader
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

use Psr\Log\LogLevel;
use Ramsey\Uuid\Uuid;
use TechDivision\Import\Cli\Command\InputArgumentKeys;
use TechDivision\Import\Cli\Command\InputOptionKeys;
use TechDivision\Import\Cli\Utils\MagentoConfigurationKeys;
use TechDivision\Import\Configuration\Jms\Configuration\Database;
use TechDivision\Import\Utils\EntityTypeCodes;
use TechDivision\Import\ConfigurationInterface;

/**
 * The configuration loader implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class ConfigurationLoader extends SimpleConfigurationLoader
{

    /**
     * The array with the default entity type code => import directory mappings.
     *
     * @var array
     */
    protected $defaultDirectories = array(
        EntityTypeCodes::CATALOG_PRODUCT               => 'products',
        EntityTypeCodes::CATALOG_PRODUCT_PRICE         => 'products',
        EntityTypeCodes::CATALOG_PRODUCT_TIER_PRICE    => 'products',
        EntityTypeCodes::CATALOG_PRODUCT_INVENTORY     => 'products',
        EntityTypeCodes::CATALOG_PRODUCT_INVENTORY_MSI => 'products',
        EntityTypeCodes::CATALOG_CATEGORY              => 'categories',
        EntityTypeCodes::EAV_ATTRIBUTE                 => 'attributes',
        EntityTypeCodes::EAV_ATTRIBUTE_SET             => 'attributes',
        EntityTypeCodes::CUSTOMER                      => 'customers',
        EntityTypeCodes::CUSTOMER_ADDRESS              => 'customers'
    );

    /**
     * The Magento Edition specific default libraries.
     *
     * @var array
     */
    protected $defaultLibraries = array(
        'ce' => array(
            'techdivision/import-app-simple',
            'techdivision/import',
            'techdivision/import-attribute',
            'techdivision/import-attribute-set',
            'techdivision/import-category',
            'techdivision/import-customer',
            'techdivision/import-customer-address',
            'techdivision/import-product',
            'techdivision/import-product-msi',
            'techdivision/import-product-tier-price',
            'techdivision/import-product-url-rewrite',
            'techdivision/import-product-bundle',
            'techdivision/import-product-link',
            'techdivision/import-product-media',
            'techdivision/import-product-variant',
            'techdivision/import-product-grouped'
        ),
        'ee' => array(
            'techdivision/import-app-simple',
            'techdivision/import',
            'techdivision/import-ee',
            'techdivision/import-attribute',
            'techdivision/import-attribute-set',
            'techdivision/import-category',
            'techdivision/import-category-ee',
            'techdivision/import-customer',
            'techdivision/import-customer-address',
            'techdivision/import-product',
            'techdivision/import-product-msi',
            'techdivision/import-product-tier-price',
            'techdivision/import-product-url-rewrite',
            'techdivision/import-product-ee',
            'techdivision/import-product-bundle',
            'techdivision/import-product-bundle-ee',
            'techdivision/import-product-link',
            'techdivision/import-product-link-ee',
            'techdivision/import-product-media',
            'techdivision/import-product-media-ee',
            'techdivision/import-product-variant',
            'techdivision/import-product-variant-ee',
            'techdivision/import-product-grouped',
            'techdivision/import-product-grouped-ee'
        )
    );

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

        // load the configuration instance
        $instance = parent::load();

        // set the serial that has been specified as command line option (or the default value)
        $instance->setSerial($this->input->getOption(InputOptionKeys::SERIAL));

        // query whether or not an operation name has been specified as command line
        // option, if yes override the value from the configuration file
        if ($operationName = $this->input->getArgument(InputArgumentKeys::OPERATION_NAME)) {
            $instance->setOperationName($operationName);
        }

        // query whether or not a Magento version has been specified as command line
        // option, if yes override the value from the configuration file
        if ($magentoVersion = $this->input->getOption(InputOptionKeys::MAGENTO_VERSION)) {
            $instance->setMagentoVersion($magentoVersion);
        }

        // query whether or not a directory containing the imported files has been specified as command line
        // option, if yes override the value from the configuration file
        if ($targetDir = $this->input->getOption(InputOptionKeys::TARGET_DIR)) {
            $instance->setTargetDir($targetDir);
        }

        // query whether or not a directory containing the archived imported files has been specified as command line
        // option, if yes override the value from the configuration file
        if ($archiveDir = $this->input->getOption(InputOptionKeys::ARCHIVE_DIR)) {
            $instance->setArchiveDir($archiveDir);
        }

        // query whether or not the debug mode has been specified as command line
        // option, if yes override the value from the configuration file
        if ($archiveArtefacts = $this->input->getOption(InputOptionKeys::ARCHIVE_ARTEFACTS)) {
            $instance->setArchiveArtefacts($instance->mapBoolean($archiveArtefacts));
        }

        // query whether or not a source date format has been specified as command
        // line  option, if yes override the value from the configuration file
        if ($sourceDateFormat = $this->input->getOption(InputOptionKeys::SOURCE_DATE_FORMAT)) {
            $instance->setSourceDateFormat($sourceDateFormat);
        }

        // query whether or not the debug mode has been specified as command line
        // option, if yes override the value from the configuration file
        if ($debugMode = $this->input->getOption(InputOptionKeys::DEBUG_MODE)) {
            $instance->setDebugMode($instance->mapBoolean($debugMode));
        }

        // query whether or not the log level has been specified as command line
        // option, if yes override the value from the configuration file
        if ($logLevel = $this->input->getOption(InputOptionKeys::LOG_LEVEL)) {
            $instance->setLogLevel($logLevel);
        }

        // query whether or not the single transaction flag has been specified as command line
        // option, if yes override the value from the configuration file
        if ($singleTransaction = $this->input->getOption(InputOptionKeys::SINGLE_TRANSACTION)) {
            $instance->setSingleTransaction($instance->mapBoolean($singleTransaction));
        }

        // query whether or not the cache flag has been specified as command line
        // option, if yes override the value from the configuration file
        if ($cacheEnabled = $this->input->getOption(InputOptionKeys::CACHE_ENABLED)) {
            $instance->setCacheEnabled($instance->mapBoolean($cacheEnabled));
        }

        // query whether or not we've an valid Magento root directory specified
        if ($this->isMagentoRootDir($installationDir = $instance->getInstallationDir())) {
            // if yes, add the database configuration
            $instance->addDatabase($this->getMagentoDbConnection($installationDir));

            // add the source directory if NOT specified in the configuration file
            if (($sourceDir = $instance->getSourceDir()) === null) {
                $instance->setSourceDir($sourceDir = sprintf('%s/var/importexport', $installationDir));
            }

            // add the target directory if NOT specified in the configuration file
            if ($instance->getTargetDir() === null) {
                $instance->setTargetDir($sourceDir);
            }
        }

        // query whether or not a DB ID has been specified as command line
        // option, if yes override the value from the configuration file
        if ($useDbId = $this->input->getOption(InputOptionKeys::USE_DB_ID)) {
            $instance->setUseDbId($useDbId);
        } else {
            // query whether or not a PDO DSN has been specified as command line
            // option, if yes override the value from the configuration file
            if ($dsn = $this->input->getOption(InputOptionKeys::DB_PDO_DSN)) {
                // first REMOVE all other database configurations
                $instance->clearDatabases();

                // add the database configuration
                $instance->addDatabase(
                    $this->newDatabaseConfiguration(
                        $dsn,
                        $this->input->getOption(InputOptionKeys::DB_USERNAME),
                        $this->input->getOption(InputOptionKeys::DB_PASSWORD)
                    )
                );
            }
        }

        // extend the plugins with the main configuration instance
        /** @var \TechDivision\Import\Cli\Configuration\Subject $subject */
        foreach ($instance->getPlugins() as $plugin) {
            // set the configuration instance on the plugin
            $plugin->setConfiguration($instance);

            // query whether or not the plugin has subjects configured
            if ($subjects = $plugin->getSubjects()) {
                // extend the plugin's subjects with the main configuration instance
                /** @var \TechDivision\Import\Cli\Configuration\Subject $subject */
                foreach ($subjects as $subject) {
                    // set the configuration instance on the subject
                    $subject->setConfiguration($instance);
                }
            }
        }

        // query whether or not the debug mode is enabled and log level
        // has NOT been overwritten with a commandline option
        if ($instance->isDebugMode() && !$this->input->getOption(InputOptionKeys::LOG_LEVEL)) {
            // set debug log level, if log level has NOT been overwritten on command line
            $instance->setLogLevel(LogLevel::DEBUG);
        }

        // prepend the array with the Magento Edition specific core libraries
        $instance->setExtensionLibraries(
            array_merge(
                $this->getDefaultLibraries($instance->getMagentoEdition()),
                $instance->getExtensionLibraries()
            )
        );

        // load the extension libraries, if configured
        $this->libraryLoader->load($instance);

        // register the configured aliases in the DI container, this MUST
        // happen after the libraries have been loaded, else it would not
        // be possible to override existing aliases
        $this->initializeAliases($instance);

        // return the initialized configuration instance
        return $instance;
    }

    /**
     * Query whether or not, the passed directory is a Magento root directory.
     *
     * @param string $dir The directory to query
     *
     * @return boolean TRUE if the directory is a Magento root directory, else FALSE
     */
    protected function isMagentoRootDir($dir)
    {
        return is_file($this->getMagentoEnv($dir));
    }

    /**
     * Return's the path to the Magento file with the environment configuration.
     *
     * @param string $dir The path to the Magento root directory
     *
     * @return string The path to the Magento file with the environment configuration
     */
    protected function getMagentoEnv($dir)
    {
        return sprintf('%s/app/etc/env.php', $dir);
    }

    /**
     * Return's the requested Magento DB connction data.
     *
     * @param string $dir            The path to the Magento root directory
     * @param string $connectionName The connection name to return the data for
     *
     * @return array The connection data
     * @throws \Exception Is thrown, if the requested DB connection is not available
     */
    protected function getMagentoDbConnection($dir, $connectionName = 'default')
    {

        // load the magento environment
        $env = require $this->getMagentoEnv($dir);

        // query whether or not, the requested connection is available
        if (isset($env[MagentoConfigurationKeys::DB][MagentoConfigurationKeys::CONNECTION][$connectionName])) {
            // load the connection data
            $connection = $env[MagentoConfigurationKeys::DB][MagentoConfigurationKeys::CONNECTION][$connectionName];

            // create and return a new database configuration
            return $this->newDatabaseConfiguration(
                $this->newDsn($connection[MagentoConfigurationKeys::HOST], $connection[MagentoConfigurationKeys::DBNAME]),
                $connection[MagentoConfigurationKeys::USERNAME],
                $connection[MagentoConfigurationKeys::PASSWORD],
                false
            );
        }

        // throw an execption if not
        throw new \Exception(sprintf('Requested Magento DB connection "%s" not found in Magento "%s"', $connectionName, $dir));
    }

    /**
     * Create's and return's a new database configuration instance, initialized with
     * the passed values.
     *
     * @param string      $dsn      The DSN to use
     * @param string      $username The username to  use
     * @param string|null $password The passed to use
     * @param boolean     $default  TRUE if this should be the default connection
     * @param string      $id       The ID to use
     *
     * @return \TechDivision\Import\Configuration\Jms\Configuration\Database The database configuration instance
     */
    protected function newDatabaseConfiguration($dsn, $username = 'root', $password = null, $default = true, $id = null)
    {

        // initialize a new database configuration
        $database = new Database();
        $database->setDsn($dsn);
        $database->setDefault($default);
        $database->setUsername($username);

        // query whether or not an ID has been passed
        if ($id === null) {
            $id = Uuid::uuid4()->__toString();
        }

        // set the ID
        $database->setId($id);

        // query whether or not a password has been passed
        if ($password) {
            $database->setPassword($password);
        }

        // return the database configuration
        return $database;
    }

    /**
     * Create's and return's a new DSN from the passed values.
     *
     * @param string $host    The host to use
     * @param string $dbName  The database name to use
     * @param string $charset The charset to use
     *
     * @return string The DSN
     */
    protected function newDsn($host, $dbName, $charset = 'utf8')
    {
        return sprintf('mysql:host=%s;dbname=%s;charset=%s', $host, $dbName, $charset);
    }

    /**
     * Return's the Magento Edition specific default libraries. Supported Magento Editions are CE or EE.
     *
     * @param string $magentoEdition The Magento Edition to return the libraries for
     *
     * @return array The Magento Edition specific default libraries
     * @throws \Exception Is thrown, if the passed Magento Edition is NOT supported
     */
    protected function getDefaultLibraries($magentoEdition)
    {

        // query whether or not, default libraries for the passed edition are available
        if (isset($this->defaultLibraries[$edition = strtolower($magentoEdition)])) {
            return $this->defaultLibraries[$edition];
        }

        // throw an exception, if the passed edition is not supported
        throw new \Exception(
            sprintf(
                'Default libraries for Magento \'%s\' not supported (MUST be one of CE or EE)',
                $magentoEdition
            )
        );
    }

    /**
     * Return's the entity types specific default import directory.
     *
     * @param string $entityTypeCode The entity type code to return the default import directory for
     *
     * @return string The default default import directory
     * @throws \Exception Is thrown, if no default import directory for the passed entity type code is available
     */
    protected function getDefaultDirectory($entityTypeCode)
    {

        // query whether or not, a default configuration file for the passed entity type is available
        if (isset($this->defaultDirectories[$entityTypeCode])) {
            return $this->defaultDirectories[$entityTypeCode];
        }

        // throw an exception, if the passed entity type is not supported
        throw new \Exception(
            sprintf(
                'Entity Type Code \'%s\' not supported (MUST be one of catalog_product or catalog_category)',
                $entityTypeCode
            )
        );
    }

    /**
     * Registers the configured aliases in the DI container.
     *
     * @param \TechDivision\Import\ConfigurationInterface $configuration The configuration with the aliases to register
     *
     * @return void
     */
    protected function initializeAliases(ConfigurationInterface $configuration)
    {

        // load the DI aliases
        $aliases = $configuration->getAliases();

        // register the DI aliases
        foreach ($aliases as $alias) {
            $this->getContainer()->setAlias($alias->getId(), $alias->getTarget());
        }
    }
}
