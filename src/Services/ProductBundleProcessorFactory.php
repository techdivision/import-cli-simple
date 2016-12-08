<?php

/**
 * TechDivision\Import\Cli\Services\ProductBundleProcessorFactory
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
use TechDivision\Import\Product\Bundle\Actions\ProductBundleOptionAction;
use TechDivision\Import\Product\Bundle\Actions\ProductBundleOptionValueAction;
use TechDivision\Import\Product\Bundle\Actions\ProductBundleSelectionAction;
use TechDivision\Import\Product\Bundle\Actions\ProductBundleSelectionPriceAction;
use TechDivision\Import\Product\Bundle\Actions\Processors\ProductBundleOptionPersistProcessor;
use TechDivision\Import\Product\Bundle\Actions\Processors\ProductBundleOptionValuePersistProcessor;
use TechDivision\Import\Product\Bundle\Actions\Processors\ProductBundleSelectionPersistProcessor;
use TechDivision\Import\Product\Bundle\Actions\Processors\ProductBundleSelectionPricePersistProcessor;

/**
 * A SLSB providing methods to load product data using a PDO connection.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class ProductBundleProcessorFactory extends AbstractProductProcessorFactory
{

    /**
     * Return's the processor class name.
     *
     * @return string The processor class name
     */
    protected static function getProcessorType()
    {
        return 'TechDivision\Import\Product\Bundle\Services\ProductBundleProcessor';
    }

    /**
     * Factory method to create a new product bundle processor instance.
     *
     * @param \PDO                                               $connection    The PDO connection to use
     * @param TechDivision\Import\Configuration\SubjectInterface $configuration The subject configuration
     *
     * @return \TechDivision\Import\Product\Bundle\Services\ProductBundleProcessor The processor instance
     */
    public static function factory(\PDO $connection, SubjectInterface $configuration)
    {

        // load the utility class name
        $utilityClassName = $configuration->getUtilityClassName();

        // initialize the action that provides product bundle option CRUD functionality
        $productBundleOptionPersistProcessor = new ProductBundleOptionPersistProcessor();
        $productBundleOptionPersistProcessor->setUtilityClassName($utilityClassName);
        $productBundleOptionPersistProcessor->setConnection($connection);
        $productBundleOptionPersistProcessor->init();
        $productBundleOptionAction = new ProductBundleOptionAction();
        $productBundleOptionAction->setPersistProcessor($productBundleOptionPersistProcessor);

        // initialize the action that provides product bundle option CRUD functionality
        $productBundleOptionValuePersistProcessor = new ProductBundleOptionValuePersistProcessor();
        $productBundleOptionValuePersistProcessor->setUtilityClassName($utilityClassName);
        $productBundleOptionValuePersistProcessor->setConnection($connection);
        $productBundleOptionValuePersistProcessor->init();
        $productBundleOptionValueAction = new ProductBundleOptionValueAction();
        $productBundleOptionValueAction->setPersistProcessor($productBundleOptionValuePersistProcessor);

        // initialize the action that provides product bundle option CRUD functionality
        $productBundleSelectionPersistProcessor = new ProductBundleSelectionPersistProcessor();
        $productBundleSelectionPersistProcessor->setUtilityClassName($utilityClassName);
        $productBundleSelectionPersistProcessor->setConnection($connection);
        $productBundleSelectionPersistProcessor->init();
        $productBundleSelectionAction = new ProductBundleSelectionAction();
        $productBundleSelectionAction->setPersistProcessor($productBundleSelectionPersistProcessor);

        // initialize the action that provides product bundle option CRUD functionality
        $productBundleSelectionPricePersistProcessor = new ProductBundleSelectionPricePersistProcessor();
        $productBundleSelectionPricePersistProcessor->setUtilityClassName($utilityClassName);
        $productBundleSelectionPricePersistProcessor->setConnection($connection);
        $productBundleSelectionPricePersistProcessor->init();
        $productBundleSelectionPriceAction = new ProductBundleSelectionPriceAction();
        $productBundleSelectionPriceAction->setPersistProcessor($productBundleSelectionPricePersistProcessor);

        // initialize the product bundle processor
        $processorType = ProductBundleProcessorFactory::getProcessorType();
        $productBundleProcessor = new $processorType();
        $productBundleProcessor->setConnection($connection);
        $productBundleProcessor->setProductBundleOptionAction($productBundleOptionAction);
        $productBundleProcessor->setProductBundleOptionValueAction($productBundleOptionValueAction);
        $productBundleProcessor->setProductBundleSelectionAction($productBundleSelectionAction);
        $productBundleProcessor->setProductBundleSelectionPriceAction($productBundleSelectionPriceAction);

        // return the instance
        return $productBundleProcessor;
    }
}
