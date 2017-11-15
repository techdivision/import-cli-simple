<?php

/**
 * TechDivision\Import\Cli\Logger\LoggerFactoryInterface
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

use TechDivision\Import\ConfigurationInterface;

/**
 * The interface for a logger factory implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
interface LoggerFactoryInterface
{

    /**
     * Create's and return's the loggers to use.
     *
     * @param \TechDivision\Import\ConfigurationInterface $configuration The configuration with the data to create the loggers with
     *
     * @return \Doctrine\Common\Collections\ArrayCollection The array with the initialized loggers
     */
    public static function createLoggers(ConfigurationInterface $configuration);
}
