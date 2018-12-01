<?php

/**
 * TechDivision\Import\Cli\Command\ImportClearPidFileCommand
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

use TechDivision\Import\Utils\CommandNames;
use TechDivision\Import\ConfigurationInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The remove PID command implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class ImportClearPidFileCommand extends AbstractSimpleImportCommand
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
        $this->setName(CommandNames::IMPORT_CLEAR_PID_FILE)
             ->setDescription('Clears the PID file from a previous import process, if it has not been cleaned up');

        // invoke the parent method
        parent::configure();
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
    protected function executeSimpleCommand(
        ConfigurationInterface $configuration,
        InputInterface $input,
        OutputInterface $output
    ) {

        // query whether or not a PID file exists and delete it
        if (file_exists($pidFilename = $configuration->getPidFilename())) {
            if (!unlink($pidFilename)) {
                throw new \Exception(sprintf('Can\'t delete PID file %s', $pidFilename));
            }

            // write a message to the console
            $output->writeln(sprintf('<info>Successfully deleted PID file %s</info>', $pidFilename));
        } else {
            // write a message to the console
            $output->writeln(sprintf('<error>PID file %s not available</error>', $pidFilename));
        }
    }
}
