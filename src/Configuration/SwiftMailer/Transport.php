<?php

/**
 * TechDivision\Import\Cli\Configuration\SwiftMailer\Transport
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

namespace TechDivision\Import\Cli\Configuration\SwiftMailer;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;
use TechDivision\Import\Cli\Configuration\ParamsTrait;
use TechDivision\Import\Configuration\SwiftMailer\TransportConfigurationInterface;

/**
 * The swift mailer transport configuration.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class Transport implements TransportConfigurationInterface
{

    /**
     * The trait that provides parameter handling functionality.
     *
     * @var \TechDivision\Import\Cli\Configuration\ParamsTrait
     */
    use ParamsTrait;

    /**
     * The swift mailer transport factory used to create the instance.
     *
     * @var string
     * @Type("string")
     * @SerializedName("transport-factory")
     */
    protected $transportFactory;

    /**
     * Return's the swift mailer transport factory used to create he instance.
     *
     * @return string The swift mailer transport factory
     */
    public function getTransportFactory()
    {
        return $this->transportFactory;
    }
}
