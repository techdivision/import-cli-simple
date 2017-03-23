<?php

/**
 * TechDivision\Import\Cli\Configuration\SwiftMailer
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
use TechDivision\Import\Configuration\SwiftMailerConfigurationInterface;

/**
 * The swift mailer configuration.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class SwiftMailer implements SwiftMailerConfigurationInterface
{

    /**
     * The trait that provides parameter configuration functionality.
     *
     * @var \TechDivision\Import\Cli\Configuration\ParamsTrait
     */
    use ParamsTrait;

    /**
     * The factory used to create the swift mailer instance.
     *
     * @var string
     * @Type("string")
     */
    protected $factory;

    /**
     * The swift mailer factory used to create the instance.
     *
     * @var string
     * @Type("string")
     * @SerializedName("mailer-factory")
     */
    protected $mailerFactory;

    /**
     * The swift mailer transport configuration to use.
     *
     * @var \TechDivision\Import\Cli\Configuration\SwiftMailer\Transport
     * @Type("TechDivision\Import\Cli\Configuration\SwiftMailer\Transport")
     */
    protected $transport;

    /**
     * Return's the factory used to create the swift mailer instance.
     *
     * @return string The factory
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Return's the swift mailer factory that creates the mailer instance.
     *
     * @return string The swift mailer factory
     */
    public function getMailerFactory()
    {
        return $this->mailerFactory;
    }

    /**
     * Return's the swift mailer transport configuration to use.
     *
     * @return TechDivision\Import\Cli\Configuration\SwiftMailer\Transport The transport configuration to use
     */
    public function getTransport()
    {
        return $this->transport;
    }
}
