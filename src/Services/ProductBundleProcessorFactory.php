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
use TechDivision\Import\Product\Bundle\Actions\Processors\ProductBundleOptionCreateProcessor;
use TechDivision\Import\Product\Bundle\Actions\Processors\ProductBundleOptionValueCreateProcessor;
use TechDivision\Import\Product\Bundle\Actions\Processors\ProductBundleSelectionCreateProcessor;
use TechDivision\Import\Product\Bundle\Actions\Processors\ProductBundleSelectionPriceCreateProcessor;

/**
 * Factory to create a new product bundle processor.
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
        $productBundleOptionCreateProcessor = new ProductBundleOptionCreateProcessor();
        $productBundleOptionCreateProcessor->setUtilityClassName($utilityClassName);
        $productBundleOptionCreateProcessor->setConnection($connection);
        $productBundleOptionCreateProcessor->init();
        $productBundleOptionAction = new ProductBundleOptionAction();
        $productBundleOptionAction->setCreateProcessor($productBundleOptionCreateProcessor);

        // initialize the action that provides product bundle option CRUD functionality
        $productBundleOptionValueCreateProcessor = new ProductBundleOptionValueCreateProcessor();
        $productBundleOptionValueCreateProcessor->setUtilityClassName($utilityClassName);
        $productBundleOptionValueCreateProcessor->setConnection($connection);
        $productBundleOptionValueCreateProcessor->init();
        $productBundleOptionValueAction = new ProductBundleOptionValueAction();
        $productBundleOptionValueAction->setCreateProcessor($productBundleOptionValueCreateProcessor);

        // initialize the action that provides product bundle option CRUD functionality
        $productBundleSelectionCreateProcessor = new ProductBundleSelectionCreateProcessor();
        $productBundleSelectionCreateProcessor->setUtilityClassName($utilityClassName);
        $productBundleSelectionCreateProcessor->setConnection($connection);
        $productBundleSelectionCreateProcessor->init();
        $productBundleSelectionAction = new ProductBundleSelectionAction();
        $productBundleSelectionAction->setCreateProcessor($productBundleSelectionCreateProcessor);

        // initialize the action that provides product bundle option CRUD functionality
        $productBundleSelectionPriceCreateProcessor = new ProductBundleSelectionPriceCreateProcessor();
        $productBundleSelectionPriceCreateProcessor->setUtilityClassName($utilityClassName);
        $productBundleSelectionPriceCreateProcessor->setConnection($connection);
        $productBundleSelectionPriceCreateProcessor->init();
        $productBundleSelectionPriceAction = new ProductBundleSelectionPriceAction();
        $productBundleSelectionPriceAction->setCreateProcessor($productBundleSelectionPriceCreateProcessor);

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
