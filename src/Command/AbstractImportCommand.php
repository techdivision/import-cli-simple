<?php

/**
 * TechDivision\Import\Cli\Command\ImportCommandTrait
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

namespace TechDivision\Import\Cli\Command;

use Monolog\Logger;
use Monolog\Handler\ErrorLogHandler;
use TechDivision\Import\Utils\LoggerKeys;
use TechDivision\Import\Utils\OperationKeys;
use TechDivision\Import\ConfigurationInterface;
use TechDivision\Import\Cli\Simple;
use TechDivision\Import\Cli\ConfigurationFactory;
use TechDivision\Import\Cli\Configuration;
use TechDivision\Import\Cli\Configuration\Database;
use TechDivision\Import\Cli\Configuration\LoggerFactory;
use TechDivision\Import\Cli\Utils\SynteticServiceKeys;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * The abstract import command implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
abstract class AbstractImportCommand extends Command implements ImportCommandInterface
{

    /**
     * Configures the current command.
     *
     * @return void
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {

        // initialize the command with the required/optional options
        $this->addArgument(
            InputArgumentKeys::OPERATION_NAME,
            InputArgument::OPTIONAL,
            'The operation that has to be used for the import, one of "add-update", "replace" or "delete"',
            $this->getDefaultOperation()
        )
        ->addOption(
            InputOptionKeys::CONFIGURATION,
            null,
            InputOption::VALUE_REQUIRED,
            'Specify the pathname to the configuration file to use',
            $this->getDefaultConfiguration()
        )
        ->addOption(
            InputOptionKeys::ENTITY_TYPE_CODE,
            null,
            InputOption::VALUE_REQUIRED,
            'Specify the entity type code to use',
            $this->getEntityTypeCode()
        )
        ->addOption(
            InputOptionKeys::INSTALLATION_DIR,
            null,
            InputOption::VALUE_REQUIRED,
            'The Magento installation directory to which the files has to be imported',
            $this->getMagentoInstallationDir()
        )
        ->addOption(
            InputOptionKeys::SOURCE_DIR,
            null,
            InputOption::VALUE_REQUIRED,
            'The directory that has to be watched for new files',
            $this->getDefaultImportDir()
        )
        ->addOption(
            InputOptionKeys::TARGET_DIR,
            null,
            InputOption::VALUE_REQUIRED,
            'The target directory with the files that has been imported',
            $this->getDefaultImportDir()
        )
        ->addOption(
            InputOptionKeys::UTILITY_CLASS_NAME,
            null,
            InputOption::VALUE_REQUIRED,
            'The utility class name with the SQL statements'
        )
        ->addOption(
            InputOptionKeys::PREFIX,
            null,
            InputOption::VALUE_REQUIRED,
            'The prefix of the CSV source file(s) that has/have to be imported'
        )
        ->addOption(
            InputOptionKeys::MAGENTO_EDITION,
            null,
            InputOption::VALUE_REQUIRED,
            'The Magento edition to be used, either one of CE or EE'
        )
        ->addOption(
            InputOptionKeys::MAGENTO_VERSION,
            null,
            InputOption::VALUE_REQUIRED,
            'The Magento version to be used, e. g. 2.1.2'
        )
        ->addOption(
            InputOptionKeys::SOURCE_DATE_FORMAT,
            null,
            InputOption::VALUE_REQUIRED,
            'The date format used in the CSV file(s)'
        )
        ->addOption(
            InputOptionKeys::USE_DB_ID,
            null,
            InputOption::VALUE_REQUIRED,
            'The explicit database ID used for the actual import process'
        )
        ->addOption(
            InputOptionKeys::DB_PDO_DSN,
            null,
            InputOption::VALUE_REQUIRED,
            'The DSN used to connect to the Magento database where the data has to be imported, e. g. mysql:host=127.0.0.1;dbname=magento;charset=utf8'
        )
        ->addOption(
            InputOptionKeys::DB_USERNAME,
            null,
            InputOption::VALUE_REQUIRED,
            'The username used to connect to the Magento database'
        )
        ->addOption(
            InputOptionKeys::DB_PASSWORD,
            null,
            InputOption::VALUE_REQUIRED,
            'The password used to connect to the Magento database'
        )
        ->addOption(
            InputOptionKeys::LOG_LEVEL,
            null,
            InputOption::VALUE_REQUIRED,
            'The log level to use'
        )
        ->addOption(
            InputOptionKeys::DEBUG_MODE,
            null,
            InputOption::VALUE_REQUIRED,
            'Whether use the debug mode or not'
        )
        ->addOption(
            InputOptionKeys::PID_FILENAME,
            null,
            InputOption::VALUE_REQUIRED,
            'The explicit PID filename to use',
            sprintf('%s/%s', sys_get_temp_dir(), Configuration::PID_FILENAME)
        );
    }

    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input  An InputInterface instance
     * @param \Symfony\Component\Console\Output\OutputInterface $output An OutputInterface instance
     *
     * @return null|int null or 0 if everything went fine, or an error code
     * @throws \LogicException When this abstract method is not implemented
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        // load the actual vendor directory
        $vendorDirectory = $this->getVendorDir();

        // the path of the JMS serializer directory, relative to the vendor directory
        $jmsDirectory = DIRECTORY_SEPARATOR . 'jms' . DIRECTORY_SEPARATOR . 'serializer' . DIRECTORY_SEPARATOR . 'src';

        // try to find the path to the JMS Serializer annotations
        if (!file_exists($annotationDirectory = $vendorDirectory . DIRECTORY_SEPARATOR . $jmsDirectory)) {
            // stop processing, if the JMS annotations can't be found
            throw new \Exception(
                sprintf(
                    'The jms/serializer libarary can not be found in one of %s',
                    implode(', ', $this->getVendorDir())
                )
            );
        }

        // register the autoloader for the JMS serializer annotations
        \Doctrine\Common\Annotations\AnnotationRegistry::registerAutoloadNamespace(
            'JMS\Serializer\Annotation',
            $annotationDirectory
        );

        // load the importer configuration and set the entity type code
        $configuration = ConfigurationFactory::factory($input);
        $configuration->setEntityTypeCode($this->getEntityTypeCode());

        // initialize the DI container
        $container = new ContainerBuilder();

        // initialize the default loader and load the DI configuration for the this library
        $defaultLoader = new XmlFileLoader($container, new FileLocator($vendorDirectory));
        $defaultLoader->load(dirname(dirname(__DIR__)) . '/etc/services.xml');

        // load the DI configuration for all the extension libraries
        foreach ($this->getExtensionLibraries($configuration) as $library) {
            if (file_exists($diConfiguration = sprintf('%s/%s/etc/services.xml', $vendorDirectory, $library))) {
                $defaultLoader->load($diConfiguration);
            }
        }

        // register autoloaders for additional vendor directories
        $customLoader = new XmlFileLoader($container, new FileLocator());
        foreach ($configuration->getAdditionalVendorDirs() as $additionalVendorDir) {
            // load the vendor directory's auto loader
            if (file_exists($autoLoader = $additionalVendorDir->getVendorDir() . '/autoload.php')) {
                require $autoLoader;
            }

            // load the DI configuration for the extension libraries
            foreach ($vendorDir->getLibraries() as $library) {
                $customLoader->load(realpath(sprintf('%s/%s/etc/services.xml', $additionalVendorDir->getVendorDir(), $library)));
            }
        }

        // add the configuration as well as input/outut instances to the DI container
        $container->set(SynteticServiceKeys::INPUT, $input);
        $container->set(SynteticServiceKeys::OUTPUT, $output);
        $container->set(SynteticServiceKeys::CONFIGURATION, $configuration);
        $container->set(SynteticServiceKeys::APPLICATION, $this->getApplication());

        // initialize the PDO connection
        $dsn = $configuration->getDatabase()->getDsn();
        $username = $configuration->getDatabase()->getUsername();
        $password = $configuration->getDatabase()->getPassword();
        $connection = new \PDO($dsn, $username, $password);
        $connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // add the PDO connection to the DI container
        $container->set(SynteticServiceKeys::CONNECTION, $connection);

        // initialize the system logger
        $loggers = array();

        // initialize the default system logger
        $systemLogger = new Logger('techdivision/import');
        $systemLogger->pushHandler(
            new ErrorLogHandler(
                ErrorLogHandler::OPERATING_SYSTEM,
                $configuration->getLogLevel()
            )
        );

        // add it to the array
        $loggers[LoggerKeys::SYSTEM] = $systemLogger;

        // append the configured loggers or override the default one
        foreach ($configuration->getLoggers() as $loggerConfiguration) {
            // load the factory class that creates the logger instance
            $loggerFactory = $loggerConfiguration->getFactory();
            // create the logger instance and add it to the available loggers
            $loggers[$loggerConfiguration->getName()] = $loggerFactory::factory($loggerConfiguration);
        }

        // add the system loggers to the DI container
        $container->set(SynteticServiceKeys::LOGGERS, $loggers);

        // start the import process
        $container->get(SynteticServiceKeys::SIMPLE)->process();
    }

    /**
     * Return's the array with the magento specific extension libraries.
     *
     * @param \TechDivision\Import\ConfigurationInterface $configuration The configuration instance
     *
     * @return array The magento edition specific extension libraries
     */
    public function getExtensionLibraries(ConfigurationInterface $configuration)
    {

        // return the array with the Magento Edition specific libraries
        return array_merge(
            Simple::getDefaultLibraries($configuration->getMagentoEdition()),
            $configuration->getExtensionLibraries()
        );
    }

    /**
     * Return's the absolute path to the actual vendor directory.
     *
     * @return string The absolute path to the actual vendor directory
     * @throws \Exception Is thrown, if none of the possible vendor directories can be found
     */
    public function getVendorDir()
    {

        // the possible paths to the vendor directory
        $possibleVendorDirectories = array(
            dirname(dirname(dirname(dirname(dirname(__DIR__))))) . DIRECTORY_SEPARATOR . 'vendor',
            dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'vendor'
        );

        // try to find the path to the JMS Serializer annotations
        foreach ($possibleVendorDirectories as $possibleVendorDirectory) {
            // return the directory as vendor directory if available
            if (is_dir($possibleVendorDirectory)) {
                return $possibleVendorDirectory;
            }
        }

        // stop processing, if NO vendor directory is available
        throw new \Exception(
            sprintf(
                'None of the possible vendor directories %s is available',
                implode(', ', $possibleVendorDirectories)
            )
        );
    }

    /**
     * Return's the Magento installation directory, assuming that this is the
     * actual directory.
     *
     * @return string The Magento installation directory
     */
    public function getMagentoInstallationDir()
    {
        return getcwd();
    }

    /**
     * Return's the given entity type's specific default configuration file.
     *
     * @return string The name of the library to query for the default configuration file
     * @throws \Exception Is thrown, if no default configuration for the passed entity type is available
     */
    public function getDefaultImportDir()
    {
        return sprintf('%s/var/importexport', $this->getMagentoInstallationDir());
    }

    /**
     * Return's the given entity type's specific default configuration file.
     *
     * @return string The name of the library to query for the default configuration file
     * @throws \Exception Is thrown, if no default configuration for the passed entity type is available
     */
    public function getDefaultConfiguration()
    {
        return sprintf(
            '%s/%s/etc/techdivision-import.json',
            $this->getVendorDir(),
            Simple::getDefaultConfiguration($this->getEntityTypeCode())
        );
    }

    /**
     * Return's the default operation.
     *
     * @return string The default operation
     */
    public function getDefaultOperation()
    {
        return OperationKeys::ADD_UPDATE;
    }
}
