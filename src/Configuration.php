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
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Cli;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Annotation\SerializedName;
use TechDivision\Import\ConfigurationInterface;
use Symfony\Component\Console\Input\InputInterface;
use TechDivision\Import\Cli\Command\InputOptionKeys;
use TechDivision\Import\Cli\Command\InputArgumentKeys;
use TechDivision\Import\Cli\Configuration\Operation;

/**
 * A simple configuration implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class Configuration implements ConfigurationInterface
{

    /**
     * The operation name to use.
     *
     * @var string
     * @Type("string")
     * @SerializedName("operation-name")
     */
    protected $operationName;

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
     * The source directory that has to be watched for new files.
     *
     * @var string
     * @Type("string")
     * @SerializedName("source-dir")
     */
    protected $sourceDir;

    /**
     * The target directory with the files that has been imported.
     *
     * @var string
     * @Type("string")
     * @SerializedName("target-dir")
     */
    protected $targetDir;

    /**
     * The database configuration.
     *
     * @var TechDivision\Import\Configuration\Database
     * @Type("TechDivision\Import\Cli\Configuration\Database")
     */
    protected $database;

    /**
     * ArrayCollection with the information of the configured operations.
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @Type("ArrayCollection<TechDivision\Import\Cli\Configuration\Operation>")
     */
    protected $operations;

    /**
     * The subject's utility class with the SQL statements to use.
     *
     * @var string
     * @Type("string")
     * @SerializedName("utility-class-name")
     */
    protected $utilityClassName;

    /**
     * The source date format to use in the subject.
     *
     * @var string
     * @Type("string")
     * @SerializedName("source-date-format")
     */
    protected $sourceDateFormat = 'n/d/y, g:i A';

    /**
     * The subject's multiple field delimiter character for fields with multiple values, defaults to (,).
     *
     * @var string
     * @Type("string")
     * @SerializedName("multiple-field-delimiter")
     */
    protected $multipleFieldDelimiter = ',';

    /**
     * The subject's delimiter character for CSV files.
     *
     * @var string
     * @Type("string")
     */
    protected $delimiter;

    /**
     * The subject's enclosure character for CSV files.
     *
     * @var string
     * @Type("string")
     */
    protected $enclosure;

    /**
     * The subject's escape character for CSV files.
     *
     * @var string
     * @Type("string")
     */
    protected $escape;

    /**
     * The subject's source charset for the CSV file.
     *
     * @var string
     * @Type("string")
     * @SerializedName("from-charset")
     */
    protected $fromCharset;

    /**
     * The subject's target charset for a CSV file.
     *
     * @var string
     * @Type("string")
     * @SerializedName("to-charset")
     */
    protected $toCharset;

    /**
     * The subject's file mode for a CSV target file.
     *
     * @var string
     * @Type("string")
     * @SerializedName("file-mode")
     */
    protected $fileMode;

    /**
     * The flag to signal that the subject has to use the strict mode or not.
     *
     * @var boolean
     * @Type("boolean")
     * @SerializedName("strict-mode")
     */
    protected $strictMode;

    /**
     * The flag whether or not the import artefacts have to be archived.
     *
     * @var boolean
     * @Type("boolean")
     * @SerializedName("archive-artefacts")
     */
    protected $archiveArtefacts;

    /**
     * The directory where the archives will be stored.
     *
     * @var string
     * @Type("string")
     * @SerializedName("archive-dir")
     */
    protected $archiveDir;

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

        // load the configuration filename we want to use
        $filename = $input->getOption(InputOptionKeys::CONFIGURATION);

        // load the JSON data
        if (!$jsonData = file_get_contents($filename)) {
            throw new \Exception('Can\'t load configuration file $filename');
        }

        // initialize the JMS serializer and load the configuration
        $serializer = SerializerBuilder::create()->build();
        /** @var \TechDivision\Import\Cli\Configuration $instance */
        $instance = $serializer->deserialize($jsonData, 'TechDivision\Import\Cli\Configuration', 'json');

        // query whether or not an operation name has been specified as command line
        // option, if yes override the value from the configuration file
        if ($operationName = $input->getArgument(InputArgumentKeys::OPERATION_NAME)) {
            $instance->setOperationName($operationName);
        }

        // query whether or not a Magento installation directory has been specified as command line
        // option, if yes override the value from the configuration file
        if ($installationDir = $input->getOption(InputOptionKeys::INSTALLATION_DIR)) {
            $instance->setInstallationDir($installationDir);
        }

        // query whether or not a directory for the source files has been specified as command line
        // option, if yes override the value from the configuration file
        if ($sourceDir = $input->getOption(InputOptionKeys::SOURCE_DIR)) {
            $instance->setSourceDir($sourceDir);
        }

        // query whether or not a directory containing the imported files has been specified as command line
        // option, if yes override the value from the configuration file
        if ($targetDir = $input->getOption(InputOptionKeys::TARGET_DIR)) {
            $instance->setTargetDir($targetDir);
        }

        // query whether or not a source date format has been specified as command
        // line  option, if yes override the value from the configuration file
        if ($sourceDateFormat = $input->getOption(InputOptionKeys::SOURCE_DATE_FORMAT)) {
            $instance->setSourceDateFormat($sourceDateFormat);
        }

        // query whether or not a Magento edition has been specified as command line
        // option, if yes override the value from the configuration file
        if ($magentoEdition = $input->getOption(InputOptionKeys::MAGENTO_EDITION)) {
            $instance->setMagentoEdition($magentoEdition);
        }

        // query whether or not a Magento version has been specified as command line
        // option, if yes override the value from the configuration file
        if ($magentoVersion = $input->getOption(InputOptionKeys::MAGENTO_VERSION)) {
            $instance->setMagentoVersion($magentoVersion);
        }

        // query whether or not a PDO DSN has been specified as command line
        // option, if yes override the value from the configuration file
        if ($dsn = $input->getOption(InputOptionKeys::DB_PDO_DSN)) {
            $instance->getDatabase()->setDsn($dsn);
        }

        // query whether or not a DB username has been specified as command line
        // option, if yes override the value from the configuration file
        if ($username = $input->getOption(InputOptionKeys::DB_USERNAME)) {
            $instance->getDatabase()->setUsername($username);
        }

        // query whether or not a DB password has been specified as command line
        // option, if yes override the value from the configuration file
        if ($password = $input->getOption(InputOptionKeys::DB_PASSWORD)) {
            $instance->getDatabase()->setPassword($password);
        }

        // extend the subjects with the parent configuration instance
        /** @var \TechDivision\Import\Cli\Configuration\Subject $subject */
        foreach ($instance->getSubjects() as $subject) {
            // set the configuration instance on the subject
            $subject->setConfiguration($instance);
        }

        // return the initialized configuration instance
        return $instance;
    }

    /**
     * Return's the array with the subjects of the operation to use.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection The ArrayCollection with the subjects
     * @throws \Exception Is thrown, if no subjects are available for the actual operation
     */
    public function getSubjects()
    {

        // iterate over the operations and return the subjects of the actual one
        /** @var TechDivision\Import\Configuration\OperationInterface $operation */
        foreach ($this->getOperations() as $operation) {
            if ($this->getOperation()->equals($operation)) {
                return $operation->getSubjects();
            }
        }

        // throw an exception if no subjects are available
        throw new \Exception(sprintf('Can\'t find any subjects for operation %s', $this->getOperation()));
    }

    /**
     * Return's the operation, initialize from the actual operation name.
     *
     * @return \TechDivision\Import\Configuration\OperationInterface The operation instance
     */
    protected function getOperation()
    {
        return new Operation($this->getOperationName());
    }

    /**
     * Return's the operation name that has to be used.
     *
     * @param string $operationName The operation name that has to be used
     *
     * @return void
     */
    public function setOperationName($operationName)
    {
        return $this->operationName = $operationName;
    }

    /**
     * Return's the operation name that has to be used.
     *
     * @return string The operation name that has to be used
     */
    public function getOperationName()
    {
        return $this->operationName;
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
     * Return's the target directory with the files that has been imported.
     *
     * @return string The target directory
     */
    public function getTargetDir()
    {
        return $this->targetDir;
    }

    /**
     * Set's the target directory with the files that has been imported.
     *
     * @param string $targetDir The target directory
     *
     * @return void
     */
    public function setTargetDir($targetDir)
    {
        $this->targetDir = $targetDir;
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

    /**
     * Return's the subject's source date format to use.
     *
     * @return string The source date format
     */
    public function getSourceDateFormat()
    {
        return $this->sourceDateFormat;
    }

    /**
     * Set's the subject's source date format to use.
     *
     * @param string $sourceDateFormat The source date format
     *
     * @return void
     */
    public function setSourceDateFormat($sourceDateFormat)
    {
        $this->sourceDateFormat = $sourceDateFormat;
    }

    /**
     * Return's the multiple field delimiter character to use, default value is comma (,).
     *
     * @return string The multiple field delimiter character
     */
    public function getMultipleFieldDelimiter()
    {
        return $this->multipleFieldDelimiter;
    }

    /**
     * Return's the delimiter character to use, default value is comma (,).
     *
     * @return string The delimiter character
     */
    public function getDelimiter()
    {
        return $this->delimiter;
    }

    /**
     * The enclosure character to use, default value is double quotation (").
     *
     * @return string The enclosure character
     */
    public function getEnclosure()
    {
        return $this->enclosure;
    }

    /**
     * The escape character to use, default value is backslash (\).
     *
     * @return string The escape character
     */
    public function getEscape()
    {
        return $this->escape;
    }

    /**
     * The file encoding of the CSV source file, default value is UTF-8.
     *
     * @return string The charset used by the CSV source file
     */
    public function getFromCharset()
    {
        return $this->fromCharset;
    }

    /**
     * The file encoding of the CSV targetfile, default value is UTF-8.
     *
     * @return string The charset used by the CSV target file
     */
    public function getToCharset()
    {
        return $this->toCharset;
    }

    /**
     * The file mode of the CSV target file, either one of write or append, default is write.
     *
     * @return string The file mode of the CSV target file
     */
    public function getFileMode()
    {
        return $this->fileMode;
    }

    /**
     * Queries whether or not strict mode is enabled or not, default is TRUE.
     *
     * @return boolean TRUE if strict mode is enabled, else FALSE
     */
    public function isStrictMode()
    {
        return $this->strictMode;
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
     * Return's the ArrayCollection with the configured operations.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection The ArrayCollection with the operations
     */
    public function getOperations()
    {
        return $this->operations;
    }

    /**
     * Return's the TRUE if the import artefacts have to be archived.
     *
     * @return boolean TRUE if the import artefacts have to be archived
     */
    public function haveArchiveArtefacts()
    {
        return $this->archiveArtefacts;
    }

    /**
     * The directory where the archives will be stored.
     *
     * @return string The archive directory
     */
    public function getArchiveDir()
    {
        return $this->archiveDir;
    }
}
