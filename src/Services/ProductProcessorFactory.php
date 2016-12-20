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
use TechDivision\Import\Repositories\EavAttributeOptionValueRepository;
use TechDivision\Import\Product\Actions\ProductAction;
use TechDivision\Import\Product\Actions\ProductCategoryAction;
use TechDivision\Import\Product\Actions\StockItemAction;
use TechDivision\Import\Product\Actions\StockStatusAction;
use TechDivision\Import\Product\Actions\ProductWebsiteAction;
use TechDivision\Import\Product\Actions\ProductVarcharAction;
use TechDivision\Import\Product\Actions\ProductTextAction;
use TechDivision\Import\Product\Actions\ProductIntAction;
use TechDivision\Import\Product\Actions\ProductDecimalAction;
use TechDivision\Import\Product\Actions\ProductDatetimeAction;
use TechDivision\Import\Product\Actions\Processors\ProductRemoveProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductPersistProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductCategoryRemoveProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductCategoryPersistProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductDatetimePersistProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductDecimalPersistProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductIntPersistProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductTextPersistProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductVarcharPersistProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductWebsiteRemoveProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductWebsitePersistProcessor;
use TechDivision\Import\Product\Actions\Processors\StockItemRemoveProcessor;
use TechDivision\Import\Product\Actions\Processors\StockItemPersistProcessor;
use TechDivision\Import\Product\Actions\Processors\StockStatusRemoveProcessor;
use TechDivision\Import\Product\Actions\Processors\StockStatusPersistProcessor;
use TechDivision\Import\Product\Repositories\UrlRewriteRepository;
use TechDivision\Import\Product\Actions\UrlRewriteAction;
use TechDivision\Import\Product\Actions\Processors\UrlRewritePersistProcessor;
use TechDivision\Import\Product\Actions\Processors\UrlRewriteRemoveProcessor;

/**
 * A SLSB providing methods to load product data using a PDO connection.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class ProductProcessorFactory extends AbstractProductProcessorFactory
{

    /**
     * Return's the processor class name.
     *
     * @return string The processor class name
     */
    protected static function getProcessorType()
    {
        return 'TechDivision\Import\Product\Services\ProductProcessor';
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

        // load the utility class name
        $utilityClassName = $configuration->getUtilityClassName();

        // initialize the repository that provides EAV attribute option value query functionality
        $eavAttributeOptionValueRepository = new EavAttributeOptionValueRepository();
        $eavAttributeOptionValueRepository->setUtilityClassName($utilityClassName);
        $eavAttributeOptionValueRepository->setConnection($connection);
        $eavAttributeOptionValueRepository->init();

        // initialize the repository that provides URL rewrite query functionality
        $urlRewriteRepository = new UrlRewriteRepository();
        $urlRewriteRepository->setUtilityClassName($utilityClassName);
        $urlRewriteRepository->setConnection($connection);
        $urlRewriteRepository->init();

        // initialize the action that provides product category CRUD functionality
        $productCategoryPersistProcessor = new ProductCategoryPersistProcessor();
        $productCategoryPersistProcessor->setUtilityClassName($utilityClassName);
        $productCategoryPersistProcessor->setConnection($connection);
        $productCategoryPersistProcessor->init();
        $productCategoryRemoveProcessor = new ProductCategoryRemoveProcessor();
        $productCategoryRemoveProcessor->setUtilityClassName($utilityClassName);
        $productCategoryRemoveProcessor->setConnection($connection);
        $productCategoryRemoveProcessor->init();
        $productCategoryAction = new ProductCategoryAction();
        $productCategoryAction->setPersistProcessor($productCategoryPersistProcessor);
        $productCategoryAction->setRemoveProcessor($productCategoryRemoveProcessor);

        // initialize the action that provides product datetime attribute CRUD functionality
        $productDatetimePersistProcessor = new ProductDatetimePersistProcessor();
        $productDatetimePersistProcessor->setUtilityClassName($utilityClassName);
        $productDatetimePersistProcessor->setConnection($connection);
        $productDatetimePersistProcessor->init();
        $productDatetimeAction = new ProductDatetimeAction();
        $productDatetimeAction->setPersistProcessor($productDatetimePersistProcessor);

        // initialize the action that provides product decimal attribute CRUD functionality
        $productDecimalPersistProcessor = new ProductDecimalPersistProcessor();
        $productDecimalPersistProcessor->setUtilityClassName($utilityClassName);
        $productDecimalPersistProcessor->setConnection($connection);
        $productDecimalPersistProcessor->init();
        $productDecimalAction = new ProductDecimalAction();
        $productDecimalAction->setPersistProcessor($productDecimalPersistProcessor);

        // initialize the action that provides product integer attribute CRUD functionality
        $productIntPersistProcessor = new ProductIntPersistProcessor();
        $productIntPersistProcessor->setUtilityClassName($utilityClassName);
        $productIntPersistProcessor->setConnection($connection);
        $productIntPersistProcessor->init();
        $productIntAction = new ProductIntAction();
        $productIntAction->setPersistProcessor($productIntPersistProcessor);

        // initialize the action that provides product CRUD functionality
        $productPersistProcessor = new ProductPersistProcessor();
        $productPersistProcessor->setUtilityClassName($utilityClassName);
        $productPersistProcessor->setConnection($connection);
        $productPersistProcessor->init();
        $productRemoveProcessor = new ProductRemoveProcessor();
        $productRemoveProcessor->setUtilityClassName($utilityClassName);
        $productRemoveProcessor->setConnection($connection);
        $productRemoveProcessor->init();
        $productAction = new ProductAction();
        $productAction->setPersistProcessor($productPersistProcessor);
        $productAction->setRemoveProcessor($productRemoveProcessor);

        // initialize the action that provides product text attribute CRUD functionality
        $productTextPersistProcessor = new ProductTextPersistProcessor();
        $productTextPersistProcessor->setUtilityClassName($utilityClassName);
        $productTextPersistProcessor->setConnection($connection);
        $productTextPersistProcessor->init();
        $productTextAction = new ProductTextAction();
        $productTextAction->setPersistProcessor($productTextPersistProcessor);

        // initialize the action that provides product varchar attribute CRUD functionality
        $productVarcharPersistProcessor = new ProductVarcharPersistProcessor();
        $productVarcharPersistProcessor->setUtilityClassName($utilityClassName);
        $productVarcharPersistProcessor->setConnection($connection);
        $productVarcharPersistProcessor->init();
        $productVarcharAction = new ProductVarcharAction();
        $productVarcharAction->setPersistProcessor($productVarcharPersistProcessor);

        // initialize the action that provides provides product website CRUD functionality
        $productWebsitePersistProcessor = new ProductWebsitePersistProcessor();
        $productWebsitePersistProcessor->setUtilityClassName($utilityClassName);
        $productWebsitePersistProcessor->setConnection($connection);
        $productWebsitePersistProcessor->init();
        $productWebsiteRemoveProcessor = new ProductWebsiteRemoveProcessor();
        $productWebsiteRemoveProcessor->setUtilityClassName($utilityClassName);
        $productWebsiteRemoveProcessor->setConnection($connection);
        $productWebsiteRemoveProcessor->init();
        $productWebsiteAction = new ProductWebsiteAction();
        $productWebsiteAction->setPersistProcessor($productWebsitePersistProcessor);
        $productWebsiteAction->setRemoveProcessor($productWebsiteRemoveProcessor);

        // initialize the action that provides stock item CRUD functionality
        $stockItemPersistProcessor = new StockItemPersistProcessor();
        $stockItemPersistProcessor->setUtilityClassName($utilityClassName);
        $stockItemPersistProcessor->setConnection($connection);
        $stockItemPersistProcessor->init();
        $stockItemRemoveProcessor = new StockItemRemoveProcessor();
        $stockItemRemoveProcessor->setUtilityClassName($utilityClassName);
        $stockItemRemoveProcessor->setConnection($connection);
        $stockItemRemoveProcessor->init();
        $stockItemAction = new StockItemAction();
        $stockItemAction->setPersistProcessor($stockItemPersistProcessor);
        $stockItemAction->setRemoveProcessor($stockItemRemoveProcessor);

        // initialize the action that provides stock status CRUD functionality
        $stockStatusPersistProcessor = new StockStatusPersistProcessor();
        $stockStatusPersistProcessor->setUtilityClassName($utilityClassName);
        $stockStatusPersistProcessor->setConnection($connection);
        $stockStatusPersistProcessor->init();
        $stockStatusRemoveProcessor = new StockItemRemoveProcessor();
        $stockStatusRemoveProcessor->setUtilityClassName($utilityClassName);
        $stockStatusRemoveProcessor->setConnection($connection);
        $stockStatusRemoveProcessor->init();
        $stockStatusAction = new StockStatusAction();
        $stockStatusAction->setPersistProcessor($stockStatusPersistProcessor);
        $stockStatusAction->setRemoveProcessor($stockStatusRemoveProcessor);

        // initialize the action that provides URL rewrite CRUD functionality
        $urlRewritePersistProcessor = new UrlRewritePersistProcessor();
        $urlRewritePersistProcessor->setUtilityClassName($utilityClassName);
        $urlRewritePersistProcessor->setConnection($connection);
        $urlRewritePersistProcessor->init();
        $urlRewriteRemoveProcessor = new UrlRewriteRemoveProcessor();
        $urlRewriteRemoveProcessor->setUtilityClassName($utilityClassName);
        $urlRewriteRemoveProcessor->setConnection($connection);
        $urlRewriteRemoveProcessor->init();
        $urlRewriteAction = new UrlRewriteAction();
        $urlRewriteAction->setPersistProcessor($urlRewritePersistProcessor);
        $urlRewriteAction->setRemoveProcessor($urlRewriteRemoveProcessor);

        // initialize the product processor
        $processorType = static::getProcessorType();
        $productProcessor = new $processorType();
        $productProcessor->setConnection($connection);
        $productProcessor->setEavAttributeOptionValueRepository($eavAttributeOptionValueRepository);
        $productProcessor->setUrlRewriteRepository($urlRewriteRepository);
        $productProcessor->setProductCategoryAction($productCategoryAction);
        $productProcessor->setProductDatetimeAction($productDatetimeAction);
        $productProcessor->setProductDecimalAction($productDecimalAction);
        $productProcessor->setProductIntAction($productIntAction);
        $productProcessor->setProductAction($productAction);
        $productProcessor->setProductTextAction($productTextAction);
        $productProcessor->setProductVarcharAction($productVarcharAction);
        $productProcessor->setProductWebsiteAction($productWebsiteAction);
        $productProcessor->setStockItemAction($stockItemAction);
        $productProcessor->setStockStatusAction($stockStatusAction);
        $productProcessor->setUrlRewriteAction($urlRewriteAction);

        // return the instance
        return $productProcessor;
    }
}
