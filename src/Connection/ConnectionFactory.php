<?php

/**
 * TechDivision\Import\Cli\Connection\ConnectionFactory
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

namespace TechDivision\Import\Cli\Connection;

use TechDivision\Import\ConfigurationInterface;
use TechDivision\Import\Connection\PDOConnectionWrapper;

/**
 * The connection factory implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class ConnectionFactory
{

    /**
     * Create's and return's the connection to use.
     *
     * @param \TechDivision\Import\ConfigurationInterface $configuration The configuration with the data to create the connection with
     *
     * @return \TechDivision\Import\Connection\PDOConnectionWrapper The initialized connection
     */
    public static function createConnection(ConfigurationInterface $configuration)
    {

        // initialize the PDO connection
        $dsn = $configuration->getDatabase()->getDsn();
        $username = $configuration->getDatabase()->getUsername();
        $password = $configuration->getDatabase()->getPassword();
        $connection = new \PDO($dsn, $username, $password);
        $connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        /* As of MySQL version > 5.7.4 the ONLY_FULL_GROUP_BY is activated by default. So in some */
        /* cases it is necessary to activate to TRADITIONAL mode to allow certain queries to run, */
        /* https://dev.mysql.com/doc/refman/5.7/en/sql-mode.html#sqlmode_only_full_group_by       */
        $connection->exec('SET SESSION sql_mode = traditional');

        // reurn the wrapped PDO connection
        return new PDOConnectionWrapper($connection);
    }
}
