<?php

/**
 * TechDivision\Import\Cli\ConfigurationFactory
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
use Rhumsaa\Uuid\Uuid;
use TechDivision\Import\App\Simple;
use Symfony\Component\Console\Input\InputInterface;
use TechDivision\Import\Utils\EntityTypeCodes;
use TechDivision\Import\Cli\Command\InputOptionKeys;
use TechDivision\Import\Cli\Command\InputArgumentKeys;
use TechDivision\Import\Cli\Command\ImportCommandInterface;
use TechDivision\Import\Configuration\Jms\Configuration\Database;
use TechDivision\Import\Cli\Utils\MagentoConfigurationKeys;

/**
 * The configuration factory implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class ConfigurationFactory extends \TechDivision\Import\Configuration\Jms\ConfigurationFactory
{

    /**
     * The composer name => Magento Edition mappings.
     *
     * @var array
     */
    protected static $editionMappings = array(
        'magento/magento2ce'                 => 'CE',
        'magento/project-communit-edition'   => 'CE',
        'magento/magento2ee'                 => 'EE',
        'magento/project-enterprise-edition' => 'EE'
    );

    /**
     * The array with the default entity type code => import directory mappings.
     *
     * @var array
     */
    protected static $defaultDirectories = array(
        EntityTypeCodes::CATALOG_PRODUCT  => 'products',
        EntityTypeCodes::CATALOG_CATEGORY => 'categories',
        EntityTypeCodes::EAV_ATTRIBUTE => 'attributes'
    );

    /**
     * The array with the default entity type => configuration mapping.
     *
     * @var array
     */
    protected static $defaultConfigurations = array(
        'ce' => array(
            EntityTypeCodes::EAV_ATTRIBUTE => 'techdivision/import-attribute',
            EntityTypeCodes::CATALOG_PRODUCT  => 'techdivision/import-product',
            EntityTypeCodes::CATALOG_CATEGORY => 'techdivision/import-category'
        ),
        'ee' => array(
            EntityTypeCodes::EAV_ATTRIBUTE => 'techdivision/import-attribute',
            EntityTypeCodes::CATALOG_PRODUCT  => 'techdivision/import-product-ee',
            EntityTypeCodes::CATALOG_CATEGORY => 'techdivision/import-category-ee'
        )
    );

    /**
     * The Magento Edition specific default libraries.
     *
     * @var array
     */
    protected static $defaultLibraries = array(
        'ce' => array(
            'techdivision/import-app-simple',
            'techdivision/import',
            'techdivision/import-attribute',
            'techdivision/import-category',
            'techdivision/import-product',
            'techdivision/import-product-bundle',
            'techdivision/import-product-link',
            'techdivision/import-product-media',
            'techdivision/import-product-variant'
        ),
        'ee' => array(
            'techdivision/import-app-simple',
            'techdivision/import',
            'techdivision/import-ee',
            'techdivision/import-attribute',
            'techdivision/import-category',
            'techdivision/import-category-ee',
            'techdivision/import-product',
            'techdivision/import-product-ee',
            'techdivision/import-product-bundle',
            'techdivision/import-product-bundle-ee',
            'techdivision/import-product-link',
            'techdivision/import-product-link-ee',
            'techdivision/import-product-media',
            'techdivision/import-product-media-ee',
            'techdivision/import-product-variant',
            'techdivision/import-product-variant-ee'
        )
    );

    /**
     * Factory implementation to create a new initialized configuration instance.
     *
     * If command line options are specified, they will always override the
     * values found in the configuration file.
     *
     * @param \TechDivision\Import\Cli\Command\ImportCommandInterface $command The command that tries to create the instance
     * @param \Symfony\Component\Console\Input\InputInterface         $input   The Symfony console input instance
     *
     * @return \TechDivision\Import\Cli\Configuration The configuration instance
     * @throws \Exception Is thrown, if the specified configuration file doesn't exist or the mandatory arguments/options to run the requested operation are not available
     */
    public static function load(ImportCommandInterface $command, InputInterface $input)
    {

        // query whether or not, a configuration file has been specified
        if ($configuration = $input->getOption(InputOptionKeys::CONFIGURATION)) {
            // load the configuration from the file with the given filename
            $instance = static::factory($configuration);

        } elseif ($input->hasOptionSpecified(InputOptionKeys::INSTALLATION_DIR) &&
                  $input->getOption(InputOptionKeys::INSTALLATION_DIR)
        ) {
            // query whether or not, the specified installation directory is a valid Magento root directory
            if (!self::isMagentoRootDir($installationDir = $input->getOption(InputOptionKeys::INSTALLATION_DIR))) {
                throw new \Exception(sprintf('Directory %s specified by --installation-dir is not a Magento root directory', $installationDir));
            }

            // load the composer file from the Magento root directory
            $composer = json_decode(file_get_contents($composerFile = sprintf('%s/composer.json', $installationDir)), true);

            // try to locate Magento Edition/Version
            if (!isset(self::$editionMappings[$possibleEdition = $composer[MagentoConfigurationKeys::COMPOSER_EDITION_NAME_ATTRIBUTE]])) {
                throw new \Exception(sprintf('"%s" detected in "%s" is not a valid Magento Edition/Version', $possibleEdition, $composerFile));
            }

            // if Magento Edition/Version are available, load them
            $magentoEdition = self::$editionMappings[$possibleEdition];

            // use the Magento Edition that has been detected by the installation directory
            $instance = static::factory(self::getDefaultConfiguration($command, $magentoEdition));

        } elseif ($magentoEdition = $input->getOption(InputOptionKeys::MAGENTO_EDITION)) {
            // use the Magento Edition that has been specified as option
            $instance = static::factory(self::getDefaultConfiguration($command, $magentoEdition));

        } else {
            // throw an exception, if either no configuration file has been specified nor the Magento Edition can be detected
            throw new \Exception('At least one of "--configuration", "--installation-dir" or "--magento-edition" options has to be specified');
        }

        // query whether or not an operation name has been specified as command line
        // option, if yes override the value from the configuration file
        if ($operationName = $input->getArgument(InputArgumentKeys::OPERATION_NAME)) {
            $instance->setOperationName($operationName);
        }

        // query whether or not a system name has been specified as command line
        // option, if yes override the value from the configuration file
        if (($input->hasOptionSpecified(InputOptionKeys::SYSTEM_NAME) && $input->getOption(InputOptionKeys::SYSTEM_NAME)) ||
            $instance->getSystemName() === null
        ) {
            $instance->setSystemName($input->getOption(InputOptionKeys::SYSTEM_NAME));
        }

        // query whether or not a PID filename has been specified as command line
        // option, if yes override the value from the configuration file
        if (($input->hasOptionSpecified(InputOptionKeys::PID_FILENAME) && $input->getOption(InputOptionKeys::PID_FILENAME)) ||
            $instance->getPidFilename() === null
        ) {
            $instance->setPidFilename($input->getOption(InputOptionKeys::PID_FILENAME));
        }

        // query whether or not a Magento installation directory has been specified as command line
        // option, if yes override the value from the configuration file
        if (($input->hasOptionSpecified(InputOptionKeys::INSTALLATION_DIR) && $input->getOption(InputOptionKeys::INSTALLATION_DIR)) ||
            $instance->getInstallationDir() === null
        ) {
            $instance->setInstallationDir($input->getOption(InputOptionKeys::INSTALLATION_DIR));
        }

        // query whether or not a Magento edition has been specified as command line
        // option, if yes override the value from the configuration file
        if ($magentoEdition = $input->getOption(InputOptionKeys::MAGENTO_EDITION)) {
            $instance->setMagentoEdition($magentoEdition);
        }

        // query whether or not a Magento version has been specified as command line
        // option, if yes override the value from the configuration file
        if ($magentoVersion = $input->getOption(InputOptionKeys::MAGENTO_VERSION)) {
            $instance->setMagentoVersion($magentoVersion);
        }

        // query whether or not a directory for the source files has been specified as command line
        // option, if yes override the value from the configuration file
        if ($sourceDir = $input->getOption(InputOptionKeys::SOURCE_DIR)) {
            $instance->setSourceDir($sourceDir);
        }

        // query whether or not a directory containing the imported files has been specified as command line
        // option, if yes override the value from the configuration file
        if ($targetDir = $input->getOption(InputOptionKeys::TARGET_DIR)) {
            $instance->setTargetDir($targetDir);
        }

        // query whether or not a source date format has been specified as command
        // line  option, if yes override the value from the configuration file
        if ($sourceDateFormat = $input->getOption(InputOptionKeys::SOURCE_DATE_FORMAT)) {
            $instance->setSourceDateFormat($sourceDateFormat);
        }

        // query whether or not the debug mode has been specified as command line
        // option, if yes override the value from the configuration file
        if ($debugMode = $input->getOption(InputOptionKeys::DEBUG_MODE)) {
            $instance->setDebugMode($instance->mapBoolean($debugMode));
        }

        // query whether or not the log level has been specified as command line
        // option, if yes override the value from the configuration file
        if ($logLevel = $input->getOption(InputOptionKeys::LOG_LEVEL)) {
            $instance->setLogLevel($logLevel);
        }

        // query whether or not we've an valid Magento root directory specified
        if (self::isMagentoRootDir($installationDir = $instance->getInstallationDir())) {
            // if yes, add the database configuration
            $instance->addDatabase(self::getMagentoDbConnection($installationDir));

            // add the source directory if NOT specified in the configuration file
            if ($instance->getSourceDir() === null) {
                $instance->setSourceDir($sourceDir = sprintf('%s/var/importexport', $installationDir));
            }

            // add the target directory if NOT specified in the configuration file
            if ($instance->getTargetDir() === null) {
                $instance->setTargetDir($sourceDir);
            }
        }

        // query whether or not a DB ID has been specified as command line
        // option, if yes override the value from the configuration file
        if ($useDbId = $input->getOption(InputOptionKeys::USE_DB_ID)) {
            $instance->setUseDbId($useDbId);
        } else {
            // query whether or not a PDO DSN has been specified as command line
            // option, if yes override the value from the configuration file
            if ($dsn = $input->getOption(InputOptionKeys::DB_PDO_DSN)) {
                // first REMOVE all other database configurations
                $instance->clearDatabases();

                // add the database configuration
                $instance->addDatabase(
                    self::newDatabaseConfiguration(
                        $dsn,
                        $input->getOption(InputOptionKeys::DB_USERNAME),
                        $input->getOption(InputOptionKeys::DB_PASSWORD)
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
        if ($instance->isDebugMode() && !$input->getOption(InputOptionKeys::LOG_LEVEL)) {
            // set debug log level, if log level has NOT been overwritten on command line
            $instance->setLogLevel(LogLevel::DEBUG);
        }

        // prepend the array with the Magento Edition specific core libraries
        $instance->setExtensionLibraries(
            array_merge(
                ConfigurationFactory::getDefaultLibraries($instance->getMagentoEdition()),
                $instance->getExtensionLibraries()
            )
        );

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
    public static function isMagentoRootDir($dir)
    {
        return is_file(self::getMagentoEnv($dir));
    }

    /**
     * Return's the path to the Magento file with the environment configuration.
     *
     * @param string $dir The path to the Magento root directory
     *
     * @return string The path to the Magento file with the environment configuration
     */
    public static function getMagentoEnv($dir)
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
    public static function getMagentoDbConnection($dir, $connectionName = 'default')
    {

        // load the magento environment
        $env = require self::getMagentoEnv($dir);

        // query whether or not, the requested connection is available
        if (isset($env[MagentoConfigurationKeys::DB][MagentoConfigurationKeys::CONNECTION][$connectionName])) {
            // load the connection data
            $connection = $env[MagentoConfigurationKeys::DB][MagentoConfigurationKeys::CONNECTION][$connectionName];

            // create and return a new database configuration
            return self::newDatabaseConfiguration(
                self::newDsn($connection[MagentoConfigurationKeys::HOST], $connection[MagentoConfigurationKeys::DBNAME]),
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
    public static function newDatabaseConfiguration($dsn, $username = 'root', $password = null, $default = true, $id = null)
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
    public static function newDsn($host, $dbName, $charset = 'utf8')
    {
        return sprintf('mysql:host=%s;dbname=%s;charset=%s', $host, $dbName, $charset);
    }

    /**
     * Return's the default configuration for the passed Magento Edition and the actual entity type.
     *
     * @param \TechDivision\Import\Cli\Command\ImportCommandInterface $command        The command instance
     * @param string                                                  $magentoEdition The Magento Edition to return the configuration for
     *
     * @return string The path to the default configuration
     */
    public static function getDefaultConfiguration(ImportCommandInterface $command, $magentoEdition)
    {
        return sprintf(
            '%s/%s/etc/techdivision-import.json',
            $command->getVendorDir(),
            ConfigurationFactory::getDefaultConfigurationLibrary(
                $magentoEdition,
                $command->getEntityTypeCode()
            )
        );
    }

    /**
     * Return's the Magento Edition specific default libraries. Supported Magento Editions are CE or EE.
     *
     * @param string $magentoEdition The Magento Edition to return the libraries for
     *
     * @return array The Magento Edition specific default libraries
     * @throws \Exception Is thrown, if the passed Magento Edition is NOT supported
     */
    public static function getDefaultLibraries($magentoEdition)
    {

        // query whether or not, default libraries for the passed edition are available
        if (isset(self::$defaultLibraries[$edition = strtolower($magentoEdition)])) {
            return self::$defaultLibraries[$edition];
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
     * Return's the Magento Edition and entity type's specific default library that contains
     * the configuration file.
     *
     * @param string $magentoEdition The Magento Edition to return the default library for
     * @param string $entityTypeCode The entity type code to return the default library file for
     *
     * @return string The name of the library that contains the default configuration file for the passed Magento Edition and entity type code
     * @throws \Exception Is thrown, if no default configuration for the passed entity type code is available
     */
    public static function getDefaultConfigurationLibrary($magentoEdition, $entityTypeCode)
    {

        // query whether or not, a default configuration file for the passed entity type is available
        if (isset(self::$defaultConfigurations[$edition = strtolower($magentoEdition)])) {
            if (isset(self::$defaultConfigurations[$edition][$entityTypeCode])) {
                return self::$defaultConfigurations[$edition][$entityTypeCode];
            }

            // throw an exception, if the passed entity type is not supported
            throw new \Exception(
                sprintf(
                    'Entity Type Code \'%s\' not supported for Magento Edition \'%s\' (MUST be one of catalog_product, catalog_category or eav_attribute)',
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

    /**
     * Return's the entity types specific default import directory.
     *
     * @param string $entityTypeCode The entity type code to return the default import directory for
     *
     * @return string The default default import directory
     * @throws \Exception Is thrown, if no default import directory for the passed entity type code is available
     */
    public static function getDefaultDirectory($entityTypeCode)
    {

        // query whether or not, a default configuration file for the passed entity type is available
        if (isset(self::$defaultDirectories[$entityTypeCode])) {
            return self::$defaultDirectories[$entityTypeCode];
        }

        // throw an exception, if the passed entity type is not supported
        throw new \Exception(
            sprintf(
                'Entity Type Code \'%s\' not supported (MUST be one of catalog_product or catalog_category)',
                $entityTypeCode
            )
        );
    }
}
