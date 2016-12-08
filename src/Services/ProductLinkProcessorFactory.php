<?php

/**
 * TechDivision\Import\Cli\Services\ProductLinkProcessorFactory
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
use TechDivision\Import\Product\Link\Actions\ProductLinkAction;
use TechDivision\Import\Product\Link\Actions\ProductLinkAttributeAction;
use TechDivision\Import\Product\Link\Actions\ProductLinkAttributeIntAction;
use TechDivision\Import\Product\Link\Actions\ProductLinkAttributeDecimalAction;
use TechDivision\Import\Product\Link\Actions\ProductLinkAttributeVarcharAction;
use TechDivision\Import\Product\Link\Actions\Processors\ProductLinkPersistProcessor;
use TechDivision\Import\Product\Link\Actions\Processors\ProductLinkAttributePersistProcessor;
use TechDivision\Import\Product\Link\Actions\Processors\ProductLinkAttributeIntPersistProcessor;
use TechDivision\Import\Product\Link\Actions\Processors\ProductLinkAttributeDecimalPersistProcessor;
use TechDivision\Import\Product\Link\Actions\Processors\ProductLinkAttributeVarcharPersistProcessor;

/**
 * A SLSB providing methods to load product link data using a PDO connection.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class ProductLinkProcessorFactory extends AbstractProductProcessorFactory
{

    /**
     * Return's the processor class name.
     *
     * @return string The processor class name
     */
    protected static function getProcessorType()
    {
        return 'TechDivision\Import\Product\Link\Services\ProductLinkProcessor';
    }

    /**
     * Factory method to create a new product link processor instance.
     *
     * @param \PDO                                       $connection    The PDO connection to use
     * @param TechDivision\Import\ConfigurationInterface $configuration The subject configuration
     *
     * @return \TechDivision\Import\Product\Link\Services\ProducLinkProcessor The processor instance
     */
    public static function factory(\PDO $connection, SubjectInterface $configuration)
    {

        // load the utility class name
        $utilityClassName = $configuration->getUtilityClassName();

        // initialize the action that provides product link CRUD functionality
        $productLinkPersistProcessor = new ProductLinkPersistProcessor();
        $productLinkPersistProcessor->setUtilityClassName($utilityClassName);
        $productLinkPersistProcessor->setConnection($connection);
        $productLinkPersistProcessor->init();
        $productLinkAction = new ProductLinkAction();
        $productLinkAction->setPersistProcessor($productLinkPersistProcessor);

        // initialize the action that provides product link attribute CRUD functionality
        $productLinkAttributePersistProcessor = new ProductLinkAttributePersistProcessor();
        $productLinkAttributePersistProcessor->setUtilityClassName($utilityClassName);
        $productLinkAttributePersistProcessor->setConnection($connection);
        $productLinkAttributePersistProcessor->init();
        $productLinkAttributeAction = new ProductLinkAttributeAction();
        $productLinkAttributeAction->setPersistProcessor($productLinkAttributePersistProcessor);

        // initialize the action that provides product link attribute decimal CRUD functionality
        $productLinkAttributeDecimalPersistProcessor = new ProductLinkAttributeDecimalPersistProcessor();
        $productLinkAttributeDecimalPersistProcessor->setUtilityClassName($utilityClassName);
        $productLinkAttributeDecimalPersistProcessor->setConnection($connection);
        $productLinkAttributeDecimalPersistProcessor->init();
        $productLinkAttributeDecimalAction = new ProductLinkAttributeDecimalAction();
        $productLinkAttributeDecimalAction->setPersistProcessor($productLinkAttributeDecimalPersistProcessor);

        // initialize the action that provides product link attribute integer CRUD functionality
        $productLinkAttributeIntPersistProcessor = new ProductLinkAttributeIntPersistProcessor();
        $productLinkAttributeIntPersistProcessor->setUtilityClassName($utilityClassName);
        $productLinkAttributeIntPersistProcessor->setConnection($connection);
        $productLinkAttributeIntPersistProcessor->init();
        $productLinkAttributeIntAction = new ProductLinkAttributeIntAction();
        $productLinkAttributeIntAction->setPersistProcessor($productLinkAttributeIntPersistProcessor);

        // initialize the action that provides product link attribute varchar CRUD functionality
        $productLinkAttributeVarcharPersistProcessor = new ProductLinkAttributeVarcharPersistProcessor();
        $productLinkAttributeVarcharPersistProcessor->setUtilityClassName($utilityClassName);
        $productLinkAttributeVarcharPersistProcessor->setConnection($connection);
        $productLinkAttributeVarcharPersistProcessor->init();
        $productLinkAttributeVarcharAction = new ProductLinkAttributeVarcharAction();
        $productLinkAttributeVarcharAction->setPersistProcessor($productLinkAttributeVarcharPersistProcessor);

        // initialize the product link processor
        $processorType = ProductProcessorFactory::getProcessorType();
        $productLinkProcessor = new $processorType();
        $productLinkProcessor->setConnection($connection);
        $productLinkProcessor->setProductLinkAction($productLinkAction);
        $productLinkProcessor->setProductLinkAttributeAction($productLinkAttributeAction);
        $productLinkProcessor->setProductLinkAttributeIntAction($productLinkAttributeIntAction);
        $productLinkProcessor->setProductLinkAttributeDecimalAction($productLinkAttributeDecimalAction);
        $productLinkProcessor->setProductLinkAttributeVarcharAction($productLinkAttributeVarcharAction);

        // return the instance
        return $productLinkProcessor;
    }
}
