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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TechDivision\Import\Cli\Simple;
use TechDivision\Import\Cli\Configuration;
use TechDivision\Import\Services\ImportProcessor;
use TechDivision\Import\Services\RegistryProcessor;
use TechDivision\Import\Repositories\CategoryRepository;
use TechDivision\Import\Repositories\CategoryVarcharRepository;
use TechDivision\Import\Repositories\EavAttributeRepository;
use TechDivision\Import\Repositories\EavAttributeSetRepository;
use TechDivision\Import\Repositories\StoreRepository;
use TechDivision\Import\Repositories\StoreWebsiteRepository;
use TechDivision\Import\Repositories\TaxClassRepository;

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
                 sprintf('%s/techdivision-import.json', getcwd())
             )
             ->addOption(
                 InputOptionKeys::SOURCE_DIR,
                 null,
                 InputOption::VALUE_REQUIRED,
                 'The directory to query for CSV file(s) that has/have to be imported'
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

        // extract magento Edition/version
        $magentoEdition = $configuration->getMagentoEdition();
        $magentoVersion = $configuration->getMagentoVersion();

        // initialize the PDO connection
        $dsn = $configuration->getDatabase()->getDsn();
        $username = $configuration->getDatabase()->getUsername();
        $password = $configuration->getDatabase()->getPassword();
        $connection = new \PDO($dsn, $username, $password);
        $connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // initialize the repository that provides category query functionality
        $categoryRepository = new CategoryRepository();
        $categoryRepository->setMagentoEdition($magentoEdition);
        $categoryRepository->setMagentoVersion($magentoVersion);
        $categoryRepository->setConnection($connection);
        $categoryRepository->init();

        // initialize the repository that provides category varchar value query functionality
        $categoryVarcharRepository = new CategoryVarcharRepository();
        $categoryVarcharRepository->setMagentoEdition($magentoEdition);
        $categoryVarcharRepository->setMagentoVersion($magentoVersion);
        $categoryVarcharRepository->setConnection($connection);
        $categoryVarcharRepository->init();

        // initialize the repository that provides EAV attribute query functionality
        $eavAttributeRepository = new EavAttributeRepository();
        $eavAttributeRepository->setMagentoEdition($magentoEdition);
        $eavAttributeRepository->setMagentoVersion($magentoVersion);
        $eavAttributeRepository->setConnection($connection);
        $eavAttributeRepository->init();

        // initialize the repository that provides EAV attribute set query functionality
        $eavAttributeSetRepository = new EavAttributeSetRepository();
        $eavAttributeSetRepository->setMagentoEdition($magentoEdition);
        $eavAttributeSetRepository->setMagentoVersion($magentoVersion);
        $eavAttributeSetRepository->setConnection($connection);
        $eavAttributeSetRepository->init();

        // initialize the repository that provides store query functionality
        $storeRepository = new StoreRepository();
        $storeRepository->setMagentoEdition($magentoEdition);
        $storeRepository->setMagentoVersion($magentoVersion);
        $storeRepository->setConnection($connection);
        $storeRepository->init();

        // initialize the repository that provides store website query functionality
        $storeWebsiteRepository = new StoreWebsiteRepository();
        $storeWebsiteRepository->setMagentoEdition($magentoEdition);
        $storeWebsiteRepository->setMagentoVersion($magentoVersion);
        $storeWebsiteRepository->setConnection($connection);
        $storeWebsiteRepository->init();

        // initialize the repository that provides tax class query functionality
        $taxClassRepository = new TaxClassRepository();
        $taxClassRepository->setMagentoEdition($magentoEdition);
        $taxClassRepository->setMagentoVersion($magentoVersion);
        $taxClassRepository->setConnection($connection);
        $taxClassRepository->init();

        // initialize the product processor
        $importProcessor = new ImportProcessor();
        $importProcessor->setConnection($connection);
        $importProcessor->setCategoryRepository($categoryRepository);
        $importProcessor->setCategoryVarcharRepository($categoryVarcharRepository);
        $importProcessor->setEavAttributeRepository($eavAttributeRepository);
        $importProcessor->setEavAttributeSetRepository($eavAttributeSetRepository);
        $importProcessor->setStoreRepository($storeRepository);
        $importProcessor->setStoreWebsiteRepository($storeWebsiteRepository);
        $importProcessor->setTaxClassRepository($taxClassRepository);

        // initialize the registry processor
        $registryProcessor = new RegistryProcessor();

        // initialize the system logger
        $systemLogger = new Logger('techdivision/import');
        $systemLogger->pushHandler(new ErrorLogHandler());

        // initialize and run the importer
        $importer = new Simple();
        $importer->setSystemLogger($systemLogger);
        $importer->setConfiguration($configuration);
        $importer->setImportProcessor($importProcessor);
        $importer->setRegistryProcessor($registryProcessor);
        $importer->import();
    }
}
