<?php

/**
 * TechDivision\Import\Cli\Configuration\Logger\Formatter
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
use TechDivision\Import\Cli\Configuration\ParamsTrait;
use TechDivision\Import\Configuration\Logger\FormatterConfigurationInterface;

/**
 * The handler's formatter configuration.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class Formatter implements FormatterConfigurationInterface
{

    /**
     * The trait that provides parameter handling functionality.
     *
     * @var \TechDivision\Import\Cli\Configuration\ParamsTrait
     */
    use ParamsTrait;

    /**
     * The formatter's type to use.
     *
     * @var string
     * @Type("string")
     */
    protected $type;

    /**
     * Return's the formatter's type to use.
     *
     * @return string The type
     */
    public function getType()
    {
        return $this->type;
    }
}
