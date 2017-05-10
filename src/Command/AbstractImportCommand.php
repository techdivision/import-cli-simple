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
use TechDivision\Import\App\Simple;
use TechDivision\Import\App\Utils\SynteticServiceKeys;
use TechDivision\Import\Cli\ConfigurationFactory;
use TechDivision\Import\Configuration\Jms\Configuration;
use TechDivision\Import\Configuration\Jms\Configuration\Database;
use TechDivision\Import\Configuration\Jms\Configuration\LoggerFactory;
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
            OperationKeys::ADD_UPDATE
        )
        ->addOption(
            InputOptionKeys::INSTALLATION_DIR,
            null,
            InputOption::VALUE_REQUIRED,
            'The Magento installation directory to which the files has to be imported',
            getcwd()
        )
        ->addOption(
            InputOptionKeys::SYSTEM_NAME,
            null,
            InputOption::VALUE_REQUIRED,
            'Specify the system name to use',
            gethostname()
        )
        ->addOption(
            InputOptionKeys::PID_FILENAME,
            null,
            InputOption::VALUE_REQUIRED,
            'The explicit PID filename to use',
            sprintf('%s/%s', sys_get_temp_dir(), Configuration::PID_FILENAME)
        )
        ->addOption(
            InputOptionKeys::MAGENTO_EDITION,
            null,
            InputOption::VALUE_REQUIRED,
            'The Magento edition to be used, either one of "CE" or "EE"'
        )
        ->addOption(
            InputOptionKeys::MAGENTO_VERSION,
            null,
            InputOption::VALUE_REQUIRED,
            'The Magento version to be used, e. g. "2.1.2"'
        )
        ->addOption(
            InputOptionKeys::CONFIGURATION,
            null,
            InputOption::VALUE_REQUIRED,
            'Specify the pathname to the configuration file to use'
        )
        ->addOption(
            InputOptionKeys::ENTITY_TYPE_CODE,
            null,
            InputOption::VALUE_REQUIRED,
            'Specify the entity type code to use, either one of "catalog_product", "catalog_category" or "eav_attribute"'
        )
        ->addOption(
            InputOptionKeys::SOURCE_DIR,
            null,
            InputOption::VALUE_REQUIRED,
            'The directory that has to be watched for new files'
        )
        ->addOption(
            InputOptionKeys::TARGET_DIR,
            null,
            InputOption::VALUE_REQUIRED,
            'The target directory with the files that has been imported'
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
        $configuration = ConfigurationFactory::load($this, $input);

        // initialize the DI container
        $container = new ContainerBuilder();

        // initialize the default loader and load the DI configuration for the this library
        $defaultLoader = new XmlFileLoader($container, new FileLocator($vendorDirectory));

        // load the DI configuration for all the extension libraries
        foreach ($configuration->getExtensionLibraries() as $library) {
            if (file_exists($diConfiguration = sprintf('%s/%s/symfony/Resources/config/services.xml', $vendorDirectory, $library))) {
                $defaultLoader->load($diConfiguration);
            } else {
                throw new \Exception(
                    sprintf(
                        'Can\'t load DI configuration for library %s',
                        $diConfiguration
                    )
                );
            }
        }

        // register autoloaders for additional vendor directories
        $customLoader = new XmlFileLoader($container, new FileLocator());
        foreach ($configuration->getAdditionalVendorDirs() as $additionalVendorDir) {
            // load the vendor directory's auto loader
            if (file_exists($autoLoader = $additionalVendorDir->getVendorDir() . '/autoload.php')) {
                require $autoLoader;
            } else {
                throw new \Exception(
                    sprintf(
                        'Can\'t find autoloader in configured additional vendor directory %s',
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
                            'Can\'t load DI configuration for library %s',
                            $diConfiguration
                        )
                    );
                }
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
            $loggers[$loggerConfiguration->getName()] = $loggerFactory::factory($configuration, $loggerConfiguration);
        }

        // add the system loggers to the DI container
        $container->set(SynteticServiceKeys::LOGGERS, $loggers);

        // start the import process
        $container->get(SynteticServiceKeys::SIMPLE)->process();
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
}
