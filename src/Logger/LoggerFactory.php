<?php

/**
 * TechDivision\Import\Cli\Logger\LoggerFactory
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

namespace TechDivision\Import\Cli\Logger;

use Monolog\Logger;
use Monolog\Handler\ErrorLogHandler;
use TechDivision\Import\Utils\LoggerKeys;
use TechDivision\Import\ConfigurationInterface;

/**
 * The logger factory implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class LoggerFactory
{

    /**
     * Create's and return's the loggers to use.
     *
     * @param \TechDivision\Import\ConfigurationInterface $configuration The configuration with the data to create the loggers with
     *
     * @return array The array with the initialized loggers
     */
    public static function createLoggers(ConfigurationInterface $configuration)
    {

        // initialize the array for the loggers
        $loggers = array();

        // initialize the default system logger
        $systemLogger = new Logger('techdivision/import');
        $systemLogger->pushHandler(
            new ErrorLogHandler(
                ErrorLogHandler::OPERATING_SYSTEM,
                $configuration->getLogLevel()
            )
        );

        // add it to the array
        $loggers[LoggerKeys::SYSTEM] = $systemLogger;

        // append the configured loggers or override the default one
        foreach ($configuration->getLoggers() as $loggerConfiguration) {
            // load the factory class that creates the logger instance
            $loggerFactory = $loggerConfiguration->getFactory();
            // create the logger instance and add it to the available loggers
            $loggers[$loggerConfiguration->getName()] = $loggerFactory::factory($configuration, $loggerConfiguration);
        }

        // return the array with the initialized loggers
        return $loggers;
    }
}
