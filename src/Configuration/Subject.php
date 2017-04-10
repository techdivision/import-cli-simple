<?php

/**
 * TechDivision\Import\Cli\Configuration\Subject
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
use TechDivision\Import\ConfigurationInterface;
use TechDivision\Import\Configuration\SubjectConfigurationInterface;

/**
 * The subject configuration implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class Subject implements SubjectConfigurationInterface
{

    /**
     * The trait that provides parameter handling functionality.
     *
     * @var \TechDivision\Import\Cli\Configuration\ParamsTrait
     */
    use ParamsTrait;

    /**
     * The subject's unique DI identifier.
     *
     * @var string
     * @Type("string")
     * @SerializedName("id")
     */
    protected $id;

    /**
     * The file prefix for import files.
     *
     * @var string
     * @Type("string")
     */
    protected $prefix = 'magento-import';

    /**
     * The array with the subject's observers.
     *
     * @var array
     * @Type("array")
     */
    protected $observers = array();

    /**
     * The array with the subject's callbacks.
     *
     * @var array
     * @Type("array<string, array>")
     */
    protected $callbacks = array();

    /**
     * A reference to the parent configuration instance.
     *
     * @var \TechDivision\Import\ConfigurationInterface
     */
    protected $configuration;

    /**
     * The flag to signal that the subjects needs a OK file to be processed or not.
     *
     * @var boolean
     * @Type("boolean")
     * @SerializedName("ok-file-needed")
     */
    protected $okFileNeeded = false;

    /**
     * Return's the multiple field delimiter character to use, default value is comma (,).
     *
     * @return string The multiple field delimiter character
     */
    public function getMultipleFieldDelimiter()
    {
        return $this->getConfiguration()->getMultipleFieldDelimiter();
    }

    /**
     * Return's the delimiter character to use, default value is comma (,).
     *
     * @return string The delimiter character
     */
    public function getDelimiter()
    {
        return $this->getConfiguration()->getDelimiter();
    }

    /**
     * The enclosure character to use, default value is double quotation (").
     *
     * @return string The enclosure character
     */
    public function getEnclosure()
    {
        return $this->getConfiguration()->getEnclosure();
    }

    /**
     * The escape character to use, default value is backslash (\).
     *
     * @return string The escape character
     */
    public function getEscape()
    {
        return $this->getConfiguration()->getEscape();
    }

    /**
     * The file encoding of the CSV source file, default value is UTF-8.
     *
     * @return string The charset used by the CSV source file
     */
    public function getFromCharset()
    {
        return $this->getConfiguration()->getFromCharset();
    }

    /**
     * The file encoding of the CSV targetfile, default value is UTF-8.
     *
     * @return string The charset used by the CSV target file
     */
    public function getToCharset()
    {
        return $this->getConfiguration()->getToCharset();
    }

    /**
     * The file mode of the CSV target file, either one of write or append, default is write.
     *
     * @return string The file mode of the CSV target file
     */
    public function getFileMode()
    {
        return $this->getConfiguration()->getFileMode();
    }

    /**
     * Queries whether or not strict mode is enabled or not, default is TRUE.
     *
     * @return boolean TRUE if strict mode is enabled, else FALSE
     */
    public function isStrictMode()
    {
        return $this->getConfiguration()->isStrictMode();
    }

    /**
     * Return's the subject's source date format to use.
     *
     * @return string The source date format
     */
    public function getSourceDateFormat()
    {
        return $this->getConfiguration()->getSourceDateFormat();
    }

    /**
     * Return's the source directory that has to be watched for new files.
     *
     * @return string The source directory
     */
    public function getSourceDir()
    {
        return $this->getConfiguration()->getSourceDir();
    }

    /**
     * Return's the target directory with the files that has been imported.
     *
     * @return string The target directory
     */
    public function getTargetDir()
    {
        return $this->getConfiguration()->getTargetDir();
    }

    /**
     * Queries whether or not debug mode is enabled or not, default is TRUE.
     *
     * @return boolean TRUE if debug mode is enabled, else FALSE
     */
    public function isDebugMode()
    {
        return $this->getConfiguration()->isDebugMode();
    }

    /**
     * Return's the subject's unique DI identifier.
     *
     * @return string The subject's unique DI identifier
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set's the reference to the configuration instance.
     *
     * @param \TechDivision\Import\ConfigurationInterface $configuration The configuration instance
     *
     * @return void
     */
    public function setConfiguration(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Return's the reference to the configuration instance.
     *
     * @return \TechDivision\Import\ConfigurationInterface The configuration instance
     */
    public function getConfiguration()
    {
        return $this->configuration;
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
     * Return's the array with the subject's observers.
     *
     * @return array The subject's observers
     */
    public function getObservers()
    {
        return $this->observers;
    }

    /**
     * Return's the array with the subject's callbacks.
     *
     * @return array The subject's callbacks
     */
    public function getCallbacks()
    {
        return $this->callbacks;
    }

    /**
     * Set's the flag to signal that the an OK file is needed for the subject
     * to be processed.
     *
     * @param boolean $okFileNeeded TRUE if the subject needs an OK file, else FALSE
     *
     * @return void
     */
    public function setOkFileNeeded($okFileNeeded)
    {
        $this->okFileNeeded = $okFileNeeded;
    }

    /**
     * Queries whether or not that the subject needs an OK file to be processed.
     *
     * @return boolean TRUE if the subject needs an OK file, else FALSE
     */
    public function isOkFileNeeded()
    {
        return $this->okFileNeeded;
    }
}
