<?php

/**
 * TechDivision\Import\Cli\Configuration
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

namespace TechDivision\Import\Cli;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Annotation\SerializedName;
use TechDivision\Import\ConfigurationInterface;
use Symfony\Component\Console\Input\InputInterface;
use TechDivision\Import\Cli\Command\InputOptionKeys;

/**
 * A simple configuration implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
class Configuration implements ConfigurationInterface
{

    /**
     * The Magento edition, EE or CE.
     *
     * @var string
     * @Type("string")
     * @SerializedName("magento-edition")
     */
    protected $magentoEdition = 'CE';

    /**
     * The Magento version, e. g. 2.1.0.
     *
     * @var string
     * @Type("string")
     * @SerializedName("magento-version")
     */
    protected $magentoVersion = '2.1.2';

    /**
     * The Magento installation directory.
     *
     * @var string
     * @Type("string")
     * @SerializedName("installation-dir")
     */
    protected $installationDir;

    /**
     * The database configuration.
     *
     * @var TechDivision\Import\Configuration\Database
     * @Type("TechDivision\Import\Cli\Configuration\Database")
     */
    protected $database;

    /**
     * ArrayCollection with the information of the configured subjects.
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @Type("ArrayCollection<TechDivision\Import\Cli\Configuration\Subject>")
     */
    protected $subjects;

    /**
     * The subject's utility class with the SQL statements to use.
     *
     * @var string
     * @Type("string")
     * @SerializedName("utility-class-name")
     */
    protected $utilityClassName;

    /**
     * Factory implementation to create a new initialized configuration instance.
     *
     * If command line options are specified, they will always override the
     * values found in the configuration file.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input The Symfony console input instance
     *
     * @return \TechDivision\Import\Cli\Configuration The configuration instance
     * @throws \Exception Is thrown, if the specified configuration file doesn't exist
     */
    public static function factory(InputInterface $input)
    {

        // load the configuration file to use
        $filename = $input->getOption(InputOptionKeys::CONFIGURATION);

        // load the JSON data
        if (!$jsonData = file_get_contents($filename)) {
            throw new \Exception('Can\'t load configuration file $filename');
        }

        // initialize the JMS serializer and load the configuration
        $serializer = SerializerBuilder::create()->build();
        /** @var \TechDivision\Import\Cli\Configuration $instance */
        $instance = $serializer->deserialize($jsonData, 'TechDivision\Import\Cli\Configuration', 'json');

        // query whether or not a Magento installation directory has been specified as command line
        // option, if yes override the value from the configuration file.
        if ($installationDir = $input->getOption(InputOptionKeys::INSTALLATION_DIR)) {
            $instance->setInstallationDir($installationDir);
        }

        // query whether or not a Magento edition has been specified as command line
        // option, if yes override the value from the configuration file.
        if ($magentoEdition = $input->getOption(InputOptionKeys::MAGENTO_EDITION)) {
            $instance->setMagentoEdition($magentoEdition);
        }

        // query whether or not a Magento version has been specified as command line
        // option, if yes override the value from the configuration file.
        if ($magentoVersion = $input->getOption(InputOptionKeys::MAGENTO_VERSION)) {
            $instance->setMagentoVersion($magentoVersion);
        }

        // query whether or not a PDO DSN has been specified as command line
        // option, if yes override the value from the configuration file.
        if ($dsn = $input->getOption(InputOptionKeys::DB_PDO_DSN)) {
            $instance->getDatabase()->setDsn($dsn);
        }

        // query whether or not a DB username has been specified as command line
        // option, if yes override the value from the configuration file.
        if ($username = $input->getOption(InputOptionKeys::DB_USERNAME)) {
            $instance->getDatabase()->setUsername($username);
        }

        // query whether or not a DB password has been specified as command line
        // option, if yes override the value from the configuration file.
        if ($password = $input->getOption(InputOptionKeys::DB_PASSWORD)) {
            $instance->getDatabase()->setPassword($password);
        }

        // query whether or not a source date format has been specified as command line
        // option, if yes override the value from the configuration file.
        if ($sourceDateFormat = $input->getOption(InputOptionKeys::SOURCE_DATE_FORMAT)) {
            /** @var \TechDivision\Import\Cli\Configuration\Subject $subject */
            foreach ($instance->getSubjects() as $subject) {
                $subject->setSourceDateFormat($sourceDateFormat);
            }
        }

        // extend the subjects with the parent configuration instance
        /** @var \TechDivision\Import\Cli\Configuration\Subject $subject */
        foreach ($instance->getSubjects() as $subject) {
            $subject->setConfiguration($instance);
        }

        // return the initialized configuration instance
        return $instance;
    }

    /**
     * Return's the database configuration.
     *
     * @return \TechDivision\Import\Cli\Configuration\Database The database configuration
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * Return's the ArrayCollection with the subjects.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection The ArrayCollection with the subjects
     */
    public function getSubjects()
    {
        return $this->subjects;
    }

    /**
     * Set's the Magento installation directory.
     *
     * @param string $installationDir The Magento installation directory
     *
     * @return void
     */
    public function setInstallationDir($installationDir)
    {
        $this->installationDir = $installationDir;
    }

    /**
     * Return's the Magento installation directory.
     *
     * @return string The Magento installation directory
     */
    public function getInstallationDir()
    {
        return $this->installationDir;
    }

    /**
     * Return's the utility class with the SQL statements to use.
     *
     * @param string $utilityClassName The utility class name
     *
     * @return void
     */
    public function setUtilityClassName($utilityClassName)
    {
        return $this->utilityClassName = $utilityClassName;
    }

    /**
     * Return's the utility class with the SQL statements to use.
     *
     * @return string The utility class name
     */
    public function getUtilityClassName()
    {
        return $this->utilityClassName;
    }

    /**
     * Set's the Magento edition, EE or CE.
     *
     * @param string $magentoEdition The Magento edition
     *
     * @return void
     */
    public function setMagentoEdition($magentoEdition)
    {
        $this->magentoEdition = $magentoEdition;
    }

    /**
     * Return's the Magento edition, EE or CE.
     *
     * @return string The Magento edition
     */
    public function getMagentoEdition()
    {
        return $this->magentoEdition;
    }

    /**
     * Return's the Magento version, e. g. 2.1.0.
     *
     * @param string $magentoVersion The Magento version
     *
     * @return void
     */
    public function setMagentoVersion($magentoVersion)
    {
        $this->magentoVersion = $magentoVersion;
    }

    /**
     * Return's the Magento version, e. g. 2.1.0.
     *
     * @return string The Magento version
     */
    public function getMagentoVersion()
    {
        return $this->magentoVersion;
    }
}
