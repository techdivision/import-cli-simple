<?php

/**
 * TechDivision\Import\Cli\Command\ImportCommandInterface
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

use TechDivision\Import\ConfigurationInterface;

/**
 * The interface for a import command implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
interface ImportCommandInterface
{

    /**
     * Return's the array with the magento specific extension libraries.
     *
     * @param \TechDivision\Import\ConfigurationInterface $configuration The configuration instance
     *
     * @return array The magento edition specific extension libraries
     */
    public function getExtensionLibraries(ConfigurationInterface $configuration);

    /**
     * Return's the absolute path to the actual vendor directory.
     *
     * @return string The absolute path to the actual vendor directory
     * @throws \Exception Is thrown, if none of the possible vendor directories can be found
     */
    public function getVendorDir();

    /**
     * Return's the Magento installation directory, assuming that this is the
     * actual directory.
     *
     * @return string The Magento installation directory
     */
    public function getMagentoInstallationDir();

    /**
     * Return's the given entity type's specific default configuration file.
     *
     * @return string The name of the library to query for the default configuration file
     * @throws \Exception Is thrown, if no default configuration for the passed entity type is available
     */
    public function getDefaultImportDir();

    /**
     * Return's the given entity type's specific default configuration file.
     *
     * @return string The name of the library to query for the default configuration file
     * @throws \Exception Is thrown, if no default configuration for the passed entity type is available
     */
    public function getDefaultConfiguration();

    /**
     * Return's the default operation.
     *
     * @return string The default operation
     */
    public function getDefaultOperation();

    /**
     * Return's the command's entity type code.
     *
     * @return string The command's entity type code
     */
    public function getEntityTypeCode();
}
