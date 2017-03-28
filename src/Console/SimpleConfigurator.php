<?php

/**
 * TechDivision\Import\Cli\SimpleConfigurator
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

namespace TechDivision\Import\Cli;

use TechDivision\Import\ApplicationInterface;
use Symfony\Component\Console\Command\Command;

/**
 * Configurator for the The M2IF - Console Tool implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class SimpleConfigurator
{

    protected $command;

    public function __construct(Command $command)
    {
        $this->command = $command;
    }

    public function configure(ApplicationInterface $simple)
    {



        $emailManager->setEnabledFormatters(
            $this->formatterManager->getEnabledFormatters()
        );
    }
}
