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
use TechDivision\Import\Configuration\DatabaseConfigurationInterface;

/**
 * The database configuration.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class Database implements DatabaseConfigurationInterface
{

    /**
     * The database identifier for this database connection.
     *
     * @var string
     * @Type("string")
     * @SerializedName("id")
     */
    protected $id;

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
     * The flag to signal the default datasource or not.
     *
     * @var boolean
     * @Type("boolean")
     * @SerializedName("default")
     */
    protected $default = false;

    /**
     * Set's the database identifier for this database connection.
     *
     * @param string $id The database identifier
     *
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Return's the database identifier for this database connection.
     *
     * @return string The database identifier
     */
    public function getId()
    {
        return $this->id;
    }

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
     * @param string $username The DB username
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

    /**
     * Set's the flag to signal that this is the default datasource or not.
     *
     * @param boolean $default TRUE if this is the default datasource, else FALSE
     *
     * @return void
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }

    /**
     * Return's the flag to signal that this is the default datasource or not.
     *
     * @return boolean TRUE if this is the default datasource, else FALSE
     */
    public function isDefault()
    {
        return $this->default;
    }
}
