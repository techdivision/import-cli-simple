<?php

/**
 * TechDivision\Import\Cli\Command\ImportClearPidCommand
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
use JMS\Serializer\SerializerBuilder;
use TechDivision\Import\Cli\Simple;
use TechDivision\Import\Cli\Configuration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
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
class ImportClearPidCommand extends Command
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
        $this->setName('import:clear:pid')
             ->setDescription('Clears the PID file from a previous import process, if it has not been cleaned up')
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
     * Factory implementation to create a new initialized configuration instance.
     *
     * If command line options are specified, they will always override the
     * values found in the configuration file.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input The Symfony console input instance
     *
     * @return \TechDivision\Import\Cli\Configuration The configuration instance
     * @throws \Exception Is thrown, if the specified configuration file doesn't exist
     */
    protected function configurationFactory(InputInterface $input)
    {

        // load the configuration filename we want to use
        $filename = $input->getOption(InputOptionKeys::CONFIGURATION);

        // load the JSON data
        if (!$jsonData = file_get_contents($filename)) {
            throw new \Exception(sprintf('Can\'t load configuration file %s', $filename));
        }

        // initialize the JMS serializer and load the configuration
        $serializer = SerializerBuilder::create()->build();
        /** @var \TechDivision\Import\Cli\Configuration $instance */
        $instance = $serializer->deserialize($jsonData, 'TechDivision\Import\Cli\Configuration', 'json');

        // query whether or not a PID filename has been specified as command line
        // option, if yes override the value from the configuration file
        if ($pidFilename = $input->getOption(InputOptionKeys::PID_FILENAME)) {
            $instance->setPidFilename($pidFilename);
        }

        // return the initialized configuration instance
        return $instance;
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
        $configuration = $this->configurationFactory($input);

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
