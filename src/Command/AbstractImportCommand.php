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

use Rhumsaa\Uuid\Uuid;
use Psr\Log\LogLevel;
use Monolog\Logger;
use Monolog\Handler\ErrorLogHandler;
use JMS\Serializer\SerializerBuilder;
use TechDivision\Import\Utils\LoggerKeys;
use TechDivision\Import\Cli\Simple;
use TechDivision\Import\Cli\Configuration;
use TechDivision\Import\Cli\Configuration\Database;
use TechDivision\Import\Cli\Configuration\LoggerFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
 * The abstract import command implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
abstract class AbstractImportCommand extends Command
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
            'The operation that has to be used for the import, one of "add-update", "replace" or "delete"'
        )
        ->addOption(
            InputOptionKeys::CONFIGURATION,
            null,
            InputOption::VALUE_REQUIRED,
            'Specify the pathname to the configuration file to use',
            sprintf('%s/techdivision-import.json', getcwd())
        )
        ->addOption(
            InputOptionKeys::INSTALLATION_DIR,
            null,
            InputOption::VALUE_REQUIRED,
            'The Magento installation directory to which the files has to be imported'
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
            'The DSN used to connect to the Magento database where the data has to be imported, e. g. mysql:host=127.0.0.1;dbname=magento'
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

        // initialize the flag, whether the JMS annotations has been loaded or not
        $loaded = false;

        // the possible paths to the JMS annotations
        $annotationDirectories = array(
            dirname(__DIR__) . '/../../../jms/serializer/src',
            dirname(__DIR__) . '/../vendor/jms/serializer/src'
        );

        // register the JMS Serializer annotations
        foreach ($annotationDirectories as $annotationDirectory) {
            if (file_exists($annotationDirectory)) {
                \Doctrine\Common\Annotations\AnnotationRegistry::registerAutoloadNamespace(
                    'JMS\Serializer\Annotation',
                    $annotationDirectory
                );
                $loaded = true;
                break;
            }
        }

        // stop processing, if the JMS annotations can't be loaded
        if (!$loaded) {
            throw new \Exception(
                sprintf(
                    'The JMS annotations can not be found in one of paths %s',
                    implode(', ', $annotationDirectories)
                )
            );
        }

        // load the DI container instance
        $container = $this->getApplication()->getContainer();

        // add the input/outut instances to the DI container
        $container->set('input', $input);
        $container->set('output', $output);

        // load the importer configuration
        $configuration = $container->get('configuration');

        // initialize the PDO connection
        $dsn = $configuration->getDatabase()->getDsn();
        $username = $configuration->getDatabase()->getUsername();
        $password = $configuration->getDatabase()->getPassword();
        $connection = new \PDO($dsn, $username, $password);
        $connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // add the PDO connection to the DI container
        $container->set('connection', $connection);

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
        $container->set('loggers', $loggers);

        // start the import process
        $container->get('simple')->process();
    }
}
