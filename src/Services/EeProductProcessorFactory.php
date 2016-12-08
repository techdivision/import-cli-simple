<?php

/**
 * TechDivision\Import\Cli\Services\ProductProcessorFactory
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

namespace TechDivision\Import\Cli\Services;

use TechDivision\Import\Configuration\SubjectInterface;
use TechDivision\Import\Cli\Services\ProductProcessorFactory;
use TechDivision\Import\Product\Ee\Actions\SequenceProductAction;
use TechDivision\Import\Product\Ee\Actions\Processors\SequenceProductPersistProcessor;

/**
 * A SLSB providing methods to load product data using a PDO connection.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class EeProductProcessorFactory extends ProductProcessorFactory
{

    /**
     * Return's the processor class name.
     *
     * @return string The processor class name
     */
    protected static function getProcessorType()
    {
        return 'TechDivision\Import\Product\Ee\Services\EeProductProcessor';
    }

    /**
     * Factory method to create a new product processor instance.
     *
     * @param \PDO                                               $connection    The PDO connection to use
     * @param TechDivision\Import\Configuration\SubjectInterface $configuration The subject configuration
     *
     * @return \TechDivision\Import\Product\Services\ProductProcessor The processor instance
     */
    public static function factory(\PDO $connection, SubjectInterface $configuration)
    {

        // initialize the product processor
        $productProcessor = parent::factory($connection, $configuration);

        // load the utility class name
        $utilityClassName = $configuration->getUtilityClassName();

        // initialize the action that provides sequence product CRUD functionality
        $sequenceProductPersistProcessor = new SequenceProductPersistProcessor();
        $sequenceProductPersistProcessor->setUtilityClassName($utilityClassName);
        $sequenceProductPersistProcessor->setConnection($connection);
        $sequenceProductPersistProcessor->init();
        $sequenceProductAction = new SequenceProductAction();
        $sequenceProductAction->setPersistProcessor($sequenceProductPersistProcessor);

        // initialize the product processor
        $productProcessor->setSequenceProductAction($sequenceProductAction);

        // return the instance
        return $productProcessor;
    }
}
