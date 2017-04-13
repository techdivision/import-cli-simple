<?php

/**
 * TechDivision\Import\Configuration\DefaultLibraries
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

use JMS\Serializer\Annotation\Type;
use TechDivision\Import\Configuration\DefaultLibrariesConfigurationInterface;

/**
 * The default libraries configuration.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class DefaultLibraries implements DefaultLibrariesConfigurationInterface
{

    /**
     * The array with the paths to the default libraries.
     *
     * @var array
     * @Type("array")
     */
    protected $libraries = array();

    /**
     * Return's an array with the path to the default libraries.
     *
     * @return array The paths to the default libraries
     */
    public function getLibraries()
    {
        return $this->libraries;
    }
}
