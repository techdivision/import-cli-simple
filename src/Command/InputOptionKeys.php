<?php

/**
 * TechDivision\Import\Cli\Command
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */

namespace TechDivision\Import\Cli\Command;

/**
 * Utility class containing the available visibility keys.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
class InputOptionKeys
{

    /**
     * The input option key for the path to the configuration file to use.
     *
     * @var string
     */
    const CONFIGURATION = 'configuration';

    /**
     * The input option key for the prefix for the import files.
     *
     * @var string
     */
    const PREFIX = 'prefix';

    /**
     * The input option key for the Magento installation directory.
     *
     * @var string
     */
    const INSTALLATION_DIR = 'installation-dir';

    /**
     * The input option key for the Magento edition, EE or CE.
     *
     * @var string
     */
    const MAGENTO_EDITION = 'magento-edition';

    /**
     * The input option key for the Magento version, e. g. 2.1.0.
     *
     * @var string
     */
    const MAGENTO_VERSION = 'magento-version';

    /**
     * The input option key for the source date format to use.
     *
     * @var string
     */
    const SOURCE_DATE_FORMAT = 'source-date-format';

    /**
     * The input option key for the PDO DSN to use.
     *
     * @var string
     */
    const DB_PDO_DSN = 'db-pdo-dsn';

    /**
     * The input option key for the DB username to use.
     *
     * @var string
     */
    const DB_USERNAME = 'db-username';

    /**
     * The input option key for the DB password to use.
     *
     * @var string
     */
    const DB_PASSWORD = 'db-password';

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
}
