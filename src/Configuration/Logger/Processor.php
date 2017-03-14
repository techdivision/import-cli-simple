<?php

/**
 * TechDivision\Import\Cli\Configuration\Logger\Processor
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
use TechDivision\Import\Configuration\Logger\ProcessorConfigurationInterface;

/**
 * The logger's processor configuration.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class Processor implements ProcessorConfigurationInterface
{

    /**
     * The trait that provides parameter handling functionality.
     *
     * @var \TechDivision\Import\Cli\Configuration\ParamsTrait
     */
    use ParamsTrait;

    /**
     * The processor's type to use.
     *
     * @var string
     * @Type("string")
     */
    protected $type;

    /**
     * Return's the processor's type to use.
     *
     * @return string The type
     */
    public function getType()
    {
        return $this->type;
    }
}
