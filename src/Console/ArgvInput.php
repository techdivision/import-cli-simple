<?php

/**
 * TechDivision\Import\Cli\Console\ArgvInput
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

namespace TechDivision\Import\Cli\Console;

/**
 * Utility class containing the available visibility keys.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class ArgvInput extends \Symfony\Component\Console\Input\ArgvInput
{

    /**
     * Queries whether or not, a option value HAS been specified on command line.
     *
     * @param string $name The option name to be queried
     *
     * @return boolean TRUE if the option has been specified, else FALSE
     */
    public function hasOptionSpecified($name)
    {
        return isset($this->options[$name]);
    }
}
