<?php

/**
 * TechDivision\Import\Cli\Utils\MagentoConfigurationKeys
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

namespace TechDivision\Import\Cli\Utils;

/**
 * Utility class containing the necessary Magento configuration keys.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class MagentoConfigurationKeys
{

    /**
     * This is a utility class, so protect it against direct
     * instantiation.
     */
    private function __construct()
    {
    }

    /**
     * This is a utility class, so protect it against cloning.
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * The key for the DB environment in the app/etc/env.php file.
     *
     * @var string
     */
    const DB = 'db';

    /**
     * The key for the DB connection in the app/etc/env.php file.
     *
     * @var string
     */
    const CONNECTION = 'connection';

    /**
     * The key for the DB host in the app/etc/env.php file.
     *
     * @var string
     */
    const HOST = 'host';

    /**
     * The key for the DB name in the app/etc/env.php file.
     *
     * @var string
     */
    const DBNAME = 'dbname';

    /**
     * The key for the DB username in the app/etc/env.php file.
     *
     * @var string
     */
    const USERNAME = 'username';

    /**
     * The key for the DB password in the app/etc/env.php file.
     *
     * @var string
     */
    const PASSWORD = 'password';

    /**
     * The attribute with the Magento Edition name in the composer.json file.
     *
     * @var string
     */
    const COMPOSER_EDITION_NAME_ATTRIBUTE = 'name';
}
