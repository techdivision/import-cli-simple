<?php

/**
 * TechDivision\Import\Cli\Command\ImportProductsCommand
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

use Psr\Log\LogLevel;
use Monolog\Logger;
use Monolog\Handler\ErrorLogHandler;
use TechDivision\Import\Cli\Simple;
use TechDivision\Import\Cli\Configuration;
use TechDivision\Import\Cli\Services\ImportProcessorFactory;
use TechDivision\Import\Cli\Services\RegistryProcessorFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
 * The import command implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class ImportProductsCommand extends Command
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
        $this->setName('import:products')
             ->setDescription('Imports products in the configured Magento 2 instance')
             ->addArgument(
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

        // register the JMS Serializer annotations
        \Doctrine\Common\Annotations\AnnotationRegistry::registerAutoloadNamespace(
            'JMS\Serializer\Annotation',
            dirname(__DIR__).'/../vendor/jms/serializer/src'
        );

        // load the specified configuration
        $configuration = Configuration::factory($input);

        // initialize the PDO connection
        $dsn = $configuration->getDatabase()->getDsn();
        $username = $configuration->getDatabase()->getUsername();
        $password = $configuration->getDatabase()->getPassword();
        $connection = new \PDO($dsn, $username, $password);
        $connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // initialize the system logger
        $systemLogger = new Logger('techdivision/import');
        $systemLogger->pushHandler(new ErrorLogHandler(ErrorLogHandler::OPERATING_SYSTEM, LogLevel::INFO));

        // initialize and run the importer
        $importer = new Simple();
        $importer->setSystemLogger($systemLogger);
        $importer->setConfiguration($configuration);
        $importer->setImportProcessor(ImportProcessorFactory::factory($connection, $configuration));
        $importer->setRegistryProcessor(RegistryProcessorFactory::factory($connection, $configuration));
        $importer->import();

        // write a message to the console
        $output->writeln('Successfully finished import!');
    }
}
