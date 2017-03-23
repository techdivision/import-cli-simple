<?php

/**
 * TechDivision\Import\Cli\Configuration\Logger\Handler
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

namespace TechDivision\Import\Cli\Configuration\Logger;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;
use TechDivision\Import\Cli\Configuration\ParamsTrait;
use TechDivision\Import\Configuration\Logger\HandlerConfigurationInterface;

/**
 * The logger's handler configuration.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class Handler implements HandlerConfigurationInterface
{

    /**
     * The trait that provides parameter handling functionality.
     *
     * @var \TechDivision\Import\Cli\Configuration\ParamsTrait
     */
    use ParamsTrait;

    /**
     * The handler's type to use.
     *
     * @var string
     * @Type("string")
     */
    protected $type;

    /**
     * The handler's formatter to use.
     *
     * @var \TechDivision\Import\Configuration\Logger\FormatterConfigurationInterface
     * @Type("TechDivision\Import\Cli\Configuration\Logger\Formatter")
     */
    protected $formatter;

    /**
     * The swift mailer logger configuration to use.
     *
     * @var \TechDivision\Import\Cli\Configuration\SwiftMailer
     * @Type("TechDivision\Import\Cli\Configuration\SwiftMailer")
     * @SerializedName("swift-mailer")
     */
    protected $swiftMailer;

    /**
     * Return's the handler's type to use.
     *
     * @return string The type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Return's the handler's formtter to use.
     *
     * @return \TechDivision\Import\Configuration\Logger\FormatterConfigurationInterface
     */
    public function getFormatter()
    {
        return $this->formatter;
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
