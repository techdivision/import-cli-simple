<?php

/**
 * TechDivision\Import\Configuration\Database
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
use TechDivision\Import\Configuration\DatabaseInterface;

/**
 * A SLSB that handles the product import process.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class Database implements DatabaseInterface
{

    /**
     * The PDO DSN to use.
     *
     * @var string
     * @Type("string")
     * @SerializedName("pdo-dsn")
     */
    protected $dsn;

    /**
     * The DB username to use.
     *
     * @var string
     * @Type("string")
     */
    protected $username;

    /**
     * The DB password to use.
     *
     * @var string
     * @Type("string")
     */
    protected $password;

    /**
     * Set's the PDO DSN to use.
     *
     * @param string $dsn The PDO DSN
     *
     * @return void
     */
    public function setDsn($dsn)
    {
        $this->dsn = $dsn;
    }

    /**
     * Return's the PDO DSN to use.
     *
     * @return string The PDO DSN
     */
    public function getDsn()
    {
        return $this->dsn;
    }

    /**
     * Set's the DB username to use.
     *
     * @return string $username The DB username
     *
     * @return void
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Return's the DB username to use.
     *
     * @return string The DB username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set's the DB password to use.
     *
     * @param string $password The DB password
     *
     * @return void
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Return's the DB password to use.
     *
     * @return string The DB password
     */
    public function getPassword()
    {
        return $this->password;
    }
}
