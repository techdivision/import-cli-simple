<?php

/**
 * TechDivision\Import\Cli\Utils\SynteticServiceKeys
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

namespace TechDivision\Import\Cli\Utils;

/**
 * A utility class for the DI service keys.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class SynteticServiceKeys
{

    /**
     * This is a utility class, so protect it against direct
     * instantiation.
     */
    private function __construct()
    {
    }

    /**
     * This is a utility class, so protect it against cloning.
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * The key for the connection service.
     *
     * @var string
     */
    const CONNECTION = 'connection';

    /**
     * The key for the loggers service.
     *
     * @var string
     */
    const LOGGERS = 'loggers';

    /**
     * The key for the simple service.
     *
     * @var string
     */
    const SIMPLE = 'simple';

    /**
     * The key for the input service.
     *
     * @var string
     */
    const INPUT = 'input';

    /**
     * The key for the output service.
     *
     * @var string
     */
    const OUTPUT = 'output';

    /**
     * The key for the configuration service.
     *
     * @var string
     */
    const CONFIGURATION = 'configuration';

    /**
     * The key for the application service.
     *
     * @var string
     */
    const APPLICATION = 'application';
}
