<?php

/**
 * TechDivision\Import\Cli\Command\InputArgumentKeys
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

namespace TechDivision\Import\Cli\Command;

/**
 * Utility class containing the available visibility keys.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class InputArgumentKeys
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
     * Key for operation name 'add-update'.
     *
     * @var string
     */
    const OPERATION_NAME_ARG_ADD_UPDATE = 'add-update';

    /**
     * Key for operation name 'replace'.
     *
     * @var string
     */
    const OPERATION_NAME_ARG_REPLACE = 'replace';

    /**
     * Key for operation name 'delete'.
     *
     * @var string
     */
    const OPERATION_NAME_ARG_DELETE = 'delete';

    /**
     * The input argument key for the operation name to use.
     *
     * @var string
     */
    const OPERATION_NAME = 'operation-name';
}
