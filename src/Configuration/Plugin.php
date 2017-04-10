<?php

/**
 * TechDivision\Import\Cli\Configuration\Plugin
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
use TechDivision\Import\Configuration\PluginConfigurationInterface;

/**
 * A simple plugin configuration implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class Plugin implements PluginConfigurationInterface
{

    /**
     * The main configuration.
     *
     * @var string
     */
    protected $configuration;

    /**
     * The plugin's unique DI identifier.
     *
     * @var string
     * @Type("string")
     * @SerializedName("id")
     */
    protected $id;

    /**
     * ArrayCollection with the information of the configured subjects.
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @Type("ArrayCollection<TechDivision\Import\Cli\Configuration\Subject>")
     */
    protected $subjects = array();

    /**
     * The swift mailer configuration to use.
     *
     * @var \TechDivision\Import\Cli\Configuration\SwiftMailer
     * @Type("TechDivision\Import\Cli\Configuration\SwiftMailer")
     * @SerializedName("swift-mailer")
     */
    protected $swiftMailer;

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
     * Return's the subject's unique DI identifier.
     *
     * @return string The subject's unique DI identifier
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return's the ArrayCollection with the operation's subjects.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection The ArrayCollection with the operation's subjects
     */
    public function getSubjects()
    {
        return $this->subjects;
    }

    /**
     * Return's the swift mailer configuration to use.
     *
     * @return TechDivision\Import\Cli\Configuration\SwiftMailer The swift mailer configuration to use
     */
    public function getSwiftMailer()
    {
        return $this->swiftMailer;
    }
}
