<?php

/**
 * TechDivision\Import\Cli\Command\ImportCreateOkFileCommand
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
 * The command implementation that creates a OK file from a directory with CSV files.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class ImportCreateOkFileCommand extends AbstractSimpleImportCommand
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
        $this->setName(CommandNames::IMPORT_CREATE_OK_FILE)
             ->setDescription('Create\'s the OK file for the CSV files of the configured source directory');

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

        // load the source directory, ALWAYS remove the directory separator, if appended
        $sourceDir = rtrim($configuration->getSourceDir(), DIRECTORY_SEPARATOR);

        /** @var TechDivision\Import\Configuration\PluginConfigurationInterface $plugin */
        foreach ($configuration->getPlugins() as $plugin) {
            /** @var TechDivision\Import\Configuration\SubjectConfigurationInterface $subject */
            foreach ($plugin->getSubjects() as $subject) {
                // query whether or not, an OK file is needed
                if ($subject->isOkFileNeeded()) {
                    // load the prefix
                    $prefix = $subject->getPrefix();

                    // load the CSVfiles from the source directory
                    $csvFiles = glob(sprintf('%s/%s*.csv', $sourceDir, $prefix));

                    // query whether or not any CSV files are available
                    if (sizeof($csvFiles) > 0) {
                        // prepare the OK file's content
                        $okfileContent = '';
                        foreach ($csvFiles as $filename) {
                            $okfileContent .= basename($filename) . PHP_EOL;
                        }

                        // prepare the OK file's name
                        $okFilename = sprintf('%s/%s.ok', $sourceDir, $prefix);

                        // write the OK file
                        if (file_put_contents($okFilename, $okfileContent)) {
                            // write a message to the console
                            $output->writeln(sprintf('<info>Successfully written OK file %s</info>', $okFilename));
                        } else {
                            // write a message to the console
                            $output->writeln(sprintf('<error>Can\'t write OK file %s</error>', $okFilename));
                        }
                    } else {
                        // write a message to the console
                        $output->writeln(sprintf('<error>Can\'t find any CSV files in source directory %s</error>', $sourceDir));
                    }

                    // stop the process
                    return;
                }
            }
        }
    }
}
