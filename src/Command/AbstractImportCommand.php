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

use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use TechDivision\Import\Utils\OperationKeys;
use TechDivision\Import\Configuration\Jms\Configuration;
use TechDivision\Import\Cli\Utils\DependencyInjectionKeys;

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

        // configure the command
        $this->addArgument(InputArgumentKeys::OPERATION_NAME, InputArgument::OPTIONAL, 'The operation that has to be used for the import, one of "add-update", "replace" or "delete"', OperationKeys::ADD_UPDATE)
             ->addOption(InputOptionKeys::SERIAL, null, InputOption::VALUE_REQUIRED, 'The unique identifier of this import process', Uuid::uuid4()->__toString())
             ->addOption(InputOptionKeys::INSTALLATION_DIR, null, InputOption::VALUE_REQUIRED, 'The Magento installation directory to which the files has to be imported', getcwd())
             ->addOption(InputOptionKeys::SYSTEM_NAME, null, InputOption::VALUE_REQUIRED, 'Specify the system name to use', gethostname())
             ->addOption(InputOptionKeys::PID_FILENAME, null, InputOption::VALUE_REQUIRED, 'The explicit PID filename to use', sprintf('%s/%s', sys_get_temp_dir(), Configuration::PID_FILENAME))
             ->addOption(InputOptionKeys::CACHE_ENABLED, null, InputOption::VALUE_REQUIRED, 'Whether or not the cache functionality for the import should be enabled', true)
             ->addOption(InputOptionKeys::MAGENTO_EDITION, null, InputOption::VALUE_REQUIRED, 'The Magento edition to be used, either one of "CE" or "EE" (will be autodetected if possible and not specified)')
             ->addOption(InputOptionKeys::MAGENTO_VERSION, null, InputOption::VALUE_REQUIRED, 'The Magento version to be used, e. g. "2.1.2"')
             ->addOption(InputOptionKeys::CONFIGURATION, null, InputOption::VALUE_REQUIRED, 'Specify the pathname to the configuration file to use')
             ->addOption(InputOptionKeys::SOURCE_DIR, null, InputOption::VALUE_REQUIRED, 'The directory that has to be watched for new files')
             ->addOption(InputOptionKeys::TARGET_DIR, null, InputOption::VALUE_REQUIRED, 'The target directory with the files that has been imported')
             ->addOption(InputOptionKeys::ARCHIVE_DIR, null, InputOption::VALUE_REQUIRED, 'The directory the imported files will be archived in')
             ->addOption(InputOptionKeys::ARCHIVE_ARTEFACTS, null, InputOption::VALUE_REQUIRED, 'Whether or not files should be archived')
             ->addOption(InputOptionKeys::SOURCE_DATE_FORMAT, null, InputOption::VALUE_REQUIRED, 'The date format used in the CSV file(s)')
             ->addOption(InputOptionKeys::USE_DB_ID, null, InputOption::VALUE_REQUIRED, 'The explicit database ID used for the actual import process')
             ->addOption(InputOptionKeys::DB_PDO_DSN, null, InputOption::VALUE_REQUIRED, 'The DSN used to connect to the Magento database where the data has to be imported, e. g. mysql:host=127.0.0.1;dbname=magento;charset=utf8')
             ->addOption(InputOptionKeys::DB_USERNAME, null, InputOption::VALUE_REQUIRED, 'The username used to connect to the Magento database')
             ->addOption(InputOptionKeys::DB_PASSWORD, null, InputOption::VALUE_REQUIRED, 'The password used to connect to the Magento database')
             ->addOption(InputOptionKeys::LOG_LEVEL, null, InputOption::VALUE_REQUIRED, 'The log level to use')
             ->addOption(InputOptionKeys::DEBUG_MODE, null, InputOption::VALUE_REQUIRED, 'Whether or not debug mode should be used')
             ->addOption(InputOptionKeys::SINGLE_TRANSACTION, null, InputOption::VALUE_REQUIRED, 'Whether or not the import should be wrapped within a single transaction')
             ->addOption(InputOptionKeys::PARAMS, null, InputOption::VALUE_REQUIRED, 'Additional options passed as a string (MUST have the same format as the used configuration file has)')
             ->addOption(InputOptionKeys::PARAMS_FILE, null, InputOption::VALUE_REQUIRED, 'Additional options passed as pathname');
    }

    /**
     * Return's the container instance.
     *
     * @return \Symfony\Component\DependencyInjection\ContainerInterface The container instance
     */
    protected function getContainer()
    {
        return $this->getApplication()->getContainer();
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
        $this->getContainer()->get(DependencyInjectionKeys::CONFIGURATION);
        $this->getContainer()->get(DependencyInjectionKeys::SIMPLE)->process($input->getOption(InputOptionKeys::SERIAL));
    }
}
