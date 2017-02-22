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

use TechDivision\Import\Configuration\ProcessorConfigurationInterface;
use TechDivision\Import\Product\Bundle\Actions\ProductBundleOptionAction;
use TechDivision\Import\Product\Bundle\Actions\ProductBundleOptionValueAction;
use TechDivision\Import\Product\Bundle\Actions\ProductBundleSelectionAction;
use TechDivision\Import\Product\Bundle\Actions\ProductBundleSelectionPriceAction;
use TechDivision\Import\Product\Bundle\Repositories\BundleOptionRepository;
use TechDivision\Import\Product\Bundle\Repositories\BundleOptionValueRepository;
use TechDivision\Import\Product\Bundle\Repositories\BundleSelectionRepository;
use TechDivision\Import\Product\Bundle\Repositories\BundleSelectionPriceRepository;
use TechDivision\Import\Product\Bundle\Actions\Processors\ProductBundleOptionCreateProcessor;
use TechDivision\Import\Product\Bundle\Actions\Processors\ProductBundleOptionUpdateProcessor;
use TechDivision\Import\Product\Bundle\Actions\Processors\ProductBundleOptionValueCreateProcessor;
use TechDivision\Import\Product\Bundle\Actions\Processors\ProductBundleSelectionCreateProcessor;
use TechDivision\Import\Product\Bundle\Actions\Processors\ProductBundleSelectionUpdateProcessor;
use TechDivision\Import\Product\Bundle\Actions\Processors\ProductBundleSelectionPriceCreateProcessor;
use TechDivision\Import\Product\Bundle\Actions\Processors\ProductBundleSelectionPriceUpdateProcessor;

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
     * @param \PDO                                                              $connection    The PDO connection to use
     * @param TechDivision\Import\Configuration\ProcessorConfigurationInterface $configuration The subject configuration
     *
     * @return \TechDivision\Import\Product\Bundle\Services\ProductBundleProcessor The processor instance
     */
    public static function factory(\PDO $connection, ProcessorConfigurationInterface $configuration)
    {

        // load the utility class name
        $utilityClassName = $configuration->getUtilityClassName();

        // initialize the repository that provides bundle option query functionality
        $bundleOptionRepository = new BundleOptionRepository();
        $bundleOptionRepository->setUtilityClassName($utilityClassName);
        $bundleOptionRepository->setConnection($connection);
        $bundleOptionRepository->init();

        // initialize the repository that provides bundle option value query functionality
        $bundleOptionValueRepository = new BundleOptionValueRepository();
        $bundleOptionValueRepository->setUtilityClassName($utilityClassName);
        $bundleOptionValueRepository->setConnection($connection);
        $bundleOptionValueRepository->init();

        // initialize the repository that provides bundle selection query functionality
        $bundleSelectionRepository = new BundleSelectionRepository();
        $bundleSelectionRepository->setUtilityClassName($utilityClassName);
        $bundleSelectionRepository->setConnection($connection);
        $bundleSelectionRepository->init();

        // initialize the repository that provides bundle selection price query functionality
        $bundleSelectionPriceRepository = new BundleSelectionPriceRepository();
        $bundleSelectionPriceRepository->setUtilityClassName($utilityClassName);
        $bundleSelectionPriceRepository->setConnection($connection);
        $bundleSelectionPriceRepository->init();

        // initialize the action that provides product bundle option CRUD functionality
        $productBundleOptionCreateProcessor = new ProductBundleOptionCreateProcessor();
        $productBundleOptionCreateProcessor->setUtilityClassName($utilityClassName);
        $productBundleOptionCreateProcessor->setConnection($connection);
        $productBundleOptionCreateProcessor->init();
        $productBundleOptionUpdateProcessor = new ProductBundleOptionUpdateProcessor();
        $productBundleOptionUpdateProcessor->setUtilityClassName($utilityClassName);
        $productBundleOptionUpdateProcessor->setConnection($connection);
        $productBundleOptionUpdateProcessor->init();
        $productBundleOptionAction = new ProductBundleOptionAction();
        $productBundleOptionAction->setCreateProcessor($productBundleOptionCreateProcessor);
        $productBundleOptionAction->setUpdateProcessor($productBundleOptionUpdateProcessor);

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
        $productBundleSelectionUpdateProcessor = new ProductBundleSelectionUpdateProcessor();
        $productBundleSelectionUpdateProcessor->setUtilityClassName($utilityClassName);
        $productBundleSelectionUpdateProcessor->setConnection($connection);
        $productBundleSelectionUpdateProcessor->init();
        $productBundleSelectionAction = new ProductBundleSelectionAction();
        $productBundleSelectionAction->setCreateProcessor($productBundleSelectionCreateProcessor);
        $productBundleSelectionAction->setUpdateProcessor($productBundleSelectionUpdateProcessor);

        // initialize the action that provides product bundle option CRUD functionality
        $productBundleSelectionPriceCreateProcessor = new ProductBundleSelectionPriceCreateProcessor();
        $productBundleSelectionPriceCreateProcessor->setUtilityClassName($utilityClassName);
        $productBundleSelectionPriceCreateProcessor->setConnection($connection);
        $productBundleSelectionPriceCreateProcessor->init();
        $productBundleSelectionPriceUpdateProcessor = new ProductBundleSelectionPriceUpdateProcessor();
        $productBundleSelectionPriceUpdateProcessor->setUtilityClassName($utilityClassName);
        $productBundleSelectionPriceUpdateProcessor->setConnection($connection);
        $productBundleSelectionPriceUpdateProcessor->init();
        $productBundleSelectionPriceAction = new ProductBundleSelectionPriceAction();
        $productBundleSelectionPriceAction->setCreateProcessor($productBundleSelectionPriceCreateProcessor);
        $productBundleSelectionPriceAction->setUpdateProcessor($productBundleSelectionPriceUpdateProcessor);

        // initialize the product bundle processor
        $processorType = static::getProcessorType();
        $productBundleProcessor = new $processorType();
        $productBundleProcessor->setConnection($connection);
        $productBundleProcessor->setBundleOptionRepository($bundleOptionRepository);
        $productBundleProcessor->setBundleOptionValueRepository($bundleOptionValueRepository);
        $productBundleProcessor->setBundleSelectionRepository($bundleSelectionRepository);
        $productBundleProcessor->setBundleSelectionPriceRepository($bundleSelectionPriceRepository);
        $productBundleProcessor->setProductBundleOptionAction($productBundleOptionAction);
        $productBundleProcessor->setProductBundleOptionValueAction($productBundleOptionValueAction);
        $productBundleProcessor->setProductBundleSelectionAction($productBundleSelectionAction);
        $productBundleProcessor->setProductBundleSelectionPriceAction($productBundleSelectionPriceAction);

        // return the instance
        return $productBundleProcessor;
    }
}
