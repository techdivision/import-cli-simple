<?php

/**
 * TechDivision\Import\Cli\Command
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */

namespace TechDivision\Import\Cli\Command;

use Monolog\Logger;
use Monolog\Handler\ErrorLogHandler;
use TechDivision\Import\Cli\Simple;
use TechDivision\Import\Cli\Configuration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TechDivision\Import\Services\ImportProcessorFactory;
use TechDivision\Import\Services\RegistryProcessorFactory;
use Psr\Log\LogLevel;

/**
 * The import command implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
class ImportProductsCommand extends Command
{

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {

        // initialize the command with the required/optional options
        $this->setName('import:products')
             ->setDescription('Imports products in the configured Magento 2 instance')
             ->addOption(
                 InputOptionKeys::CONFIGURATION,
                 null,
                 InputOption::VALUE_REQUIRED,
                 'Specify the pathname to the configuration file to use',
                 sprintf('%s/example/ce/212/conf/techdivision-import.json', getcwd())
             )
             ->addOption(
                 InputOptionKeys::INSTALLATION_DIR,
                 null,
                 InputOption::VALUE_REQUIRED,
                 'The Magento installation directory to which the files has to be imported'
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
     * {@inheritDoc}
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
    }
}
