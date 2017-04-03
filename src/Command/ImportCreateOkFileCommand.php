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

use TechDivision\Import\Cli\Configuration;
use TechDivision\Import\ConfigurationInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The  command implementation that creates a OK file from a directory with CSV files.
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
        $this->setName('import:create:ok-file')
            ->setDescription('Create\'s the OK file for the CSV files of the configured source directory')
            ->addOption(
                InputOptionKeys::CONFIGURATION,
                null,
                InputOption::VALUE_REQUIRED,
                'Specify the pathname to the configuration file to use',
                sprintf('%s/techdivision-import.json', getcwd())
            )
            ->addOption(
                InputOptionKeys::LOG_LEVEL,
                null,
                InputOption::VALUE_REQUIRED,
                'The log level to use'
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

        // load the source directory
        $sourceDir = $configuration->getSourceDir();

        /** @var TechDivision\Import\Configuration\PluginConfigurationInterface $plugin */
        foreach ($configuration->getPlugins() as $plugin) {
            /** @var TechDivision\Import\Configuration\SubjectConfigurationInterface $subject */
            foreach ($plugin->getSubjects() as $subject) {
                // query whether or not, an OK file is needed
                if ($subject->isOkFileNeeded()) {
                    // load the prefix
                    $prefix = $subject->getPrefix();

                    // prepare the OK file's content
                    $okfileContent = '';
                    foreach (glob(sprintf('%s/%s*.csv', $sourceDir, $prefix)) as $filename) {
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

                    // stop the process
                    return;
                }
            }
        }
    }
}
