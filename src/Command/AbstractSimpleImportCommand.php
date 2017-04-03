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

use JMS\Serializer\SerializerBuilder;
use TechDivision\Import\Cli\Simple;
use TechDivision\Import\Cli\Configuration;
use TechDivision\Import\ConfigurationInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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

        // load the specified configuration
        $configuration = $this->configurationFactory($input);

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
