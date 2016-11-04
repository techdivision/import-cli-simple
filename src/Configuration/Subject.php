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
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */

namespace TechDivision\Import\Cli\Configuration;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;
use TechDivision\Import\Configuration\SubjectInterface;

/**
 * A SLSB that handles the product import process.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
class Subject implements SubjectInterface
{

    /**
     * The subject's class name.
     *
     * @var string
     * @Type("string")
     * @SerializedName("class-name")
     */
    protected $className;

    /**
     * The file prefix for import files.
     *
     * @var string
     * @Type("string")
     */
    protected $prefix = 'magento-import';

    /**
     * The source date format to use in the subject.
     *
     * @var string
     * @Type("string")
     * @SerializedName("source-date-format")
     */
    protected $sourceDateFormat = 'n/d/y, g:i A';

    /**
     * The subject's target directory.
     *
     * @var string
     * @Type("string")
     * @SerializedName("target-dir")
     */
    protected $targetDir;

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
     * Return's the subject's class name.
     *
     * @return string The subject's class name
     */
    public function getClassName()
    {
        return $this->className;
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
     * Return's the subject's target directory to use.
     *
     * @return string The target directory
     */
    public function getTargetDir()
    {
        return $this->targetDir;
    }

    /**
     * Set's the subject's target directory to use.
     *
     * @param string $targetDir The target directory
     *
     * @return void
     */
    public function setTargetDir($targetDir)
    {
        $this->targetDir = $targetDir;
    }
}
