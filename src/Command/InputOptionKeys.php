<?php

/**
 * TechDivision\Import\Cli\Command\InputOptionKeys
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

/**
 * Utility class containing the available visibility keys.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class InputOptionKeys
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
     * The input option key for the system name to use.
     *
     * @var string
     */
    const SYSTEM_NAME = 'system-name';

    /**
     * The input option key for the path to the configuration file to use.
     *
     * @var string
     */
    const CONFIGURATION = 'configuration';

    /**
     * The input option key for the Magento installation directory.
     *
     * @var string
     */
    const INSTALLATION_DIR = 'installation-dir';

    /**
     * The input option key for the directory containing the files to be imported.
     *
     * @var string
     */
    const SOURCE_DIR = 'source-dir';

    /**
     * The input option key for the directory containing the imported files.
     *
     * @var string
     */
    const TARGET_DIR = 'target-dir';

    /**
     * The input option key for the directory containing the archived imported files.
     *
     * @var string
     */
    const ARCHIVE_DIR = 'archive-dir';

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
     * The input option key for the database ID to use.
     *
     * @var string
     */
    const USE_DB_ID = 'use-db-id';

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
     * The input option key for the debug mode.
     *
     * @var string
     */
    const DEBUG_MODE = 'debug-mode';

    /**
     * The input option key for the log level to use.
     *
     * @var string
     */
    const LOG_LEVEL = 'log-level';

    /**
     * The input option key for the PID filename to use.
     *
     * @var string
     */
    const PID_FILENAME = 'pid-filename';

    /**
     * The input option key for the entity type code to use.
     *
     * @var string
     */
    const ENTITY_TYPE_CODE = 'entity-type-code';
}
