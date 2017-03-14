<?php

/**
 * TechDivision\Import\Cli\Configuration\LoggerFactory
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

namespace TechDivision\Import\Cli\Configuration;

use TechDivision\Import\Configuration\LoggerConfigurationInterface;

/**
 * Logger factory implementation.
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
     * Creates a new logger instance based on the passed logger configuration.
     *
     * @param \TechDivision\Import\Configuration\LoggerConfigurationInterface $loggerConfiguration The logger configuration
     *
     * @return \Psr\Log\LoggerInterface The logger instance
     */
    public static function factory(LoggerConfigurationInterface $loggerConfiguration)
    {

        // initialize the processors
        $processors = array();
        /** @var \TechDivision\Import\Configuration\Logger\ProcessorConfigurationInterface $processorConfiguration */
        foreach ($loggerConfiguration->getProcessors() as $processorConfiguration) {
            $reflectionClass = new \ReflectionClass($processorConfiguration->getType());
            $processors[] = $reflectionClass->newInstanceArgs($processorConfiguration->getParams());
        }

        // initialize the handlers
        $handlers = array();
        /** @var \TechDivision\Import\Configuration\Logger\HandlerConfigurationInterface $handlerConfiguration */
        foreach ($loggerConfiguration->getHandlers() as $handlerConfiguration) {
            // initialize the handler node
            $reflectionClass = new \ReflectionClass($handlerConfiguration->getType());
            $handler = $reflectionClass->newInstanceArgs($handlerConfiguration->getParams());

            // if we've a formatter, initialize the formatter also
            if ($formatterConfiguration = $handlerConfiguration->getFormatter()) {
                $reflectionClass = new \ReflectionClass($formatterConfiguration->getType());
                $handler->setFormatter($reflectionClass->newInstanceArgs($formatterConfiguration->getParams()));
            }

            // add the handler
            $handlers[] = $handler;
        }

        // prepare the logger params
        $loggerParams = array($loggerConfiguration->getChannelName(), $handlers, $processors);
        $loggerParams = array_merge($loggerParams, $loggerConfiguration->getParams());

        // initialize the logger instance itself
        $reflectionClass = new \ReflectionClass($loggerConfiguration->getType());
        return $reflectionClass->newInstanceArgs($loggerParams);
    }
}
