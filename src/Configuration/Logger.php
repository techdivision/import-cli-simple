<?php

/**
 * TechDivision\Import\Cli\Configuration\Logger
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
use Doctrine\Common\Collections\ArrayCollection;
use TechDivision\Import\Configuration\LoggerConfigurationInterface;

/**
 * The logger configuration.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class Logger implements LoggerConfigurationInterface
{

    /**
     * The trait that provides parameter handling functionality.
     *
     * @var \TechDivision\Import\Cli\Configuration\ParamsTrait
     */
    use ParamsTrait;

    /**
     * The logger's channel name to use.
     *
     * @var string
     * @Type("string")
     * @SerializedName("channel-name")
     */
    protected $channelName;

    /**
     * The logger's unique name to use.
     *
     * @var string
     * @Type("string")
     */
    protected $name;

    /**
     * The logger's type to use.
     *
     * @var string
     * @Type("string")
     */
    protected $type;

    /**
     * The factory used to create the logger instance.
     *
     * @var string
     * @Type("string")
     */
    protected $factory = 'TechDivision\Import\Cli\Configuration\LoggerFactory';

    /**
     * ArrayCollection with the information of the configured processors.
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @Type("ArrayCollection<TechDivision\Import\Cli\Configuration\Logger\Processor>")
     */
    protected $processors = array();

    /**
     * ArrayCollection with the information of the configured handlers.
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @Type("ArrayCollection<TechDivision\Import\Cli\Configuration\Logger\Handler>")
     */
    protected $handlers = array();

    /**
     * Initialize the logger instance.
     */
    public function __construct()
    {
        $this->processors = new ArrayCollection();
        $this->handlers = new ArrayCollection();
    }

    /**
     * Return's the logger's channel name to use.
     *
     * @return string The channel name
     */
    public function getChannelName()
    {
        return $this->channelName;
    }

    /**
     * Return's the logger's unique name to use.
     *
     * @return string The unique name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return's the factory used to create the logger instance.
     *
     * @return string The factory to use
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Return's the logger's type to use.
     *
     * @return string The type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Return's the array with the logger's processors.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection The ArrayCollection with the processors
     */
    public function getProcessors()
    {
        return $this->processors;
    }

    /**
     * Return's the array with the logger's handlers.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection The ArrayCollection with the handlers
     */
    public function getHandlers()
    {
        return $this->handlers;
    }
}
