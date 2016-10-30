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
     * The file prefix for import files.
     *
     * @var string
     * @Type("string")
     */
    protected $prefix = 'magento-import';

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
     * The source directory that has to be watched for new files.
     *
     * @var string
     * @Type("string")
     * @SerializedName("source-dir")
     */
    protected $sourceDir;

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
        $configuration = $input->getOption(InputOptionKeys::CONFIGURATION);

        // load the JSON data
        if (!$jsonData = file_get_contents($filename)) {
            throw new \Exception('Can\'t load configuration file $filename');
        }

        // initialize the JMS serializer and load the configuration
        $serializer = SerializerBuilder::create()->build();
        /** @var \TechDivision\Import\Cli\Configuration $instance */
        $instance = $serializer->deserialize($jsonData, 'TechDivision\Import\Cli\Configuration', 'json');

        // query whether or not a prefix has been specified as command line
        // option, if yes override the value from the configuration file.
        if ($prefix = $input->getOption(InputOptionKeys::PREFIX)) {
            $instance->setPrefix($prefix);
        }

        // query whether or not a source directory has been specified as command line
        // option, if yes override the value from the configuration file.
        if ($sourceDir = $input->getOption(InputOptionKeys::SOURCE_DIR)) {
            $instance->setSourceDir($sourceDir);
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
     * Set's the prefix for the import files.
     *
     * @param string $prefix The prefix
     *
     * @return void
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * Return's the prefix for the import files.
     *
     * @return string The prefix
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Set's the source directory that has to be watched for new files.
     *
     * @param string $sourceDir The source directory
     *
     * @return void
     */
    public function setSourceDir($sourceDir)
    {
        $this->sourceDir = $sourceDir;
    }

    /**
     * Return's the source directory that has to be watched for new files.
     *
     * @return string The source directory
     */
    public function getSourceDir()
    {
        return $this->sourceDir;
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
