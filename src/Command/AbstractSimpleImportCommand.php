<?php

/**
 * TechDivision\Import\Cli\Command\AbstractSimpleImportCommand
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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TechDivision\Import\ConfigurationInterface;
use TechDivision\Import\Configuration\Jms\Configuration;
use TechDivision\Import\Cli\Utils\DependencyInjectionKeys;

/**
 * Abstract command implementation for simple import commands (not using Importer class).
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
abstract class AbstractSimpleImportCommand extends Command
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
        $this->addOption(InputOptionKeys::PID_FILENAME, null, InputOption::VALUE_REQUIRED, 'The explicit PID filename to use', sprintf('%s/%s', sys_get_temp_dir(), Configuration::PID_FILENAME))
             ->addOption(InputOptionKeys::INSTALLATION_DIR, null, InputOption::VALUE_REQUIRED, 'The Magento installation directory to which the files has to be imported', $installationDir = getcwd())
             ->addOption(InputOptionKeys::SYSTEM_NAME, null, InputOption::VALUE_REQUIRED, 'Specify the system name to use', gethostname())
             ->addOption(InputOptionKeys::SOURCE_DIR, null, InputOption::VALUE_REQUIRED, 'The directory that has to be watched for new files', sprintf('%s/var/importexport', $installationDir))
             ->addOption(InputOptionKeys::ENTITY_TYPE_CODE, null, InputOption::VALUE_REQUIRED, 'Specify the entity type code to use, either one of "catalog_product", "catalog_category" or "eav_attribute"')
             ->addOption(InputOptionKeys::MAGENTO_EDITION, null, InputOption::VALUE_REQUIRED, 'The Magento edition to be used, either one of "CE" or "EE"', 'CE')
             ->addOption(InputOptionKeys::CONFIGURATION, null, InputOption::VALUE_REQUIRED, 'Specify the pathname to the configuration file to use')
             ->addOption(InputOptionKeys::LOG_LEVEL, null, InputOption::VALUE_REQUIRED, 'The log level to use');
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

        // try to load the configuration file
        $configuration = $this->getContainer()->get(DependencyInjectionKeys::CONFIGURATION_SIMPLE);

        // finally execute the simple command
        $this->executeSimpleCommand($configuration, $input, $output);
    }

    /**
     * Finally executes the simple command.
     *
     * @param \TechDivision\Import\ConfigurationInterface       $configuration The configuration instance
     * @param \Symfony\Component\Console\Input\InputInterface   $input         An InputInterface instance
     * @param \Symfony\Component\Console\Output\OutputInterface $output        An OutputInterface instance
     *
     * @return void
     */
    abstract protected function executeSimpleCommand(
        ConfigurationInterface $configuration,
        InputInterface $input,
        OutputInterface $output
    );
}
