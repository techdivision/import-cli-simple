<?php

/**
 * TechDivision\Import\Configuration\VendorDir
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
use JMS\Serializer\Annotation\SerializedName;
use TechDivision\Import\Configuration\VendorDirConfigurationInterface;

/**
 * The vendor dir configuration.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class VendorDir implements VendorDirConfigurationInterface
{

    /**
     * The path to the additional vendor directory.
     *
     * @var string
     * @Type("string")
     * @SerializedName("vendor-dir")
     */
    protected $vendorDir;

    /**
     * The array with the paths to the extension libraries.
     *
     * @var array
     * @Type("array")
     */
    protected $libraries = array();

    /**
     * Return's the path to the additional vendor directory.
     *
     * @return string The path to the additional vendor directory
     */
    public function getVendorDir()
    {
        return $this->vendorDir;
    }

    /**
     * Return's an array with the path to additional extension libraries.
     *
     * @return array The paths to additional extension libraries
     */
    public function getLibraries()
    {
        return $this->libraries;
    }
}
