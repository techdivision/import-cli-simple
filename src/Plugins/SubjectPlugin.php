<?php

/**
 * TechDivision\Import\Cli\Plugins\SubjectPlugin
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

namespace TechDivision\Import\Cli\Plugins;

use TechDivision\Import\Configuration\SubjectConfigurationInterface;

/**
 * Plugin that processes the subjects.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class SubjectPlugin extends \TechDivision\Import\Plugins\SubjectPlugin
{

    /**
     * Factory method to create new handler instances.
     *
     * @param \TechDivision\Import\Configuration\SubjectConfigurationInterface $subjectConfiguration The subject configuration
     *
     * @return object The handler instance
     */
    protected function subjectFactory(SubjectConfigurationInterface $subjectConfiguration)
    {
        $this->getApplication()->getContainer()->set(sprintf('configuration.%s', $subjectConfiguration->getClassName()), $subjectConfiguration);
        return $this->getApplication()->getContainer()->get($subjectConfiguration->getClassName());
    }
}
