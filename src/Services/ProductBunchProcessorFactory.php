<?php

/**
 * TechDivision\Import\Cli\Services\ProductBunchProcessorFactory
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
use TechDivision\Import\Product\Repositories\ProductRepository;
use TechDivision\Import\Product\Repositories\UrlRewriteRepository;
use TechDivision\Import\Repositories\EavAttributeOptionValueRepository;
use TechDivision\Import\Product\Actions\UrlRewriteAction;
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
use TechDivision\Import\Product\Actions\Processors\ProductDeleteProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductUpdateProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductCreateProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductCategoryDeleteProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductCategoryCreateProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductDatetimeCreateProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductDecimalCreateProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductIntCreateProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductTextCreateProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductVarcharCreateProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductWebsiteDeleteProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductWebsiteCreateProcessor;
use TechDivision\Import\Product\Actions\Processors\StockItemDeleteProcessor;
use TechDivision\Import\Product\Actions\Processors\StockItemCreateProcessor;
use TechDivision\Import\Product\Actions\Processors\StockStatusDeleteProcessor;
use TechDivision\Import\Product\Actions\Processors\StockStatusCreateProcessor;
use TechDivision\Import\Product\Actions\Processors\UrlRewriteCreateProcessor;
use TechDivision\Import\Product\Actions\Processors\UrlRewriteDeleteProcessor;

/**
 * Factory to create a new product bunch processor.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class ProductBunchProcessorFactory extends AbstractProductProcessorFactory
{

    /**
     * Return's the processor class name.
     *
     * @return string The processor class name
     */
    protected static function getProcessorType()
    {
        return 'TechDivision\Import\Product\Services\ProductBunchProcessor';
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

        // initialize the repository that provides product query functionality
        $productRepository = new ProductRepository();
        $productRepository->setUtilityClassName($utilityClassName);
        $productRepository->setConnection($connection);
        $productRepository->init();

        // initialize the action that provides product category CRUD functionality
        $productCategoryCreateProcessor = new ProductCategoryCreateProcessor();
        $productCategoryCreateProcessor->setUtilityClassName($utilityClassName);
        $productCategoryCreateProcessor->setConnection($connection);
        $productCategoryCreateProcessor->init();
        $productCategoryDeleteProcessor = new ProductCategoryDeleteProcessor();
        $productCategoryDeleteProcessor->setUtilityClassName($utilityClassName);
        $productCategoryDeleteProcessor->setConnection($connection);
        $productCategoryDeleteProcessor->init();
        $productCategoryAction = new ProductCategoryAction();
        $productCategoryAction->setCreateProcessor($productCategoryCreateProcessor);
        $productCategoryAction->setDeleteProcessor($productCategoryDeleteProcessor);

        // initialize the action that provides product datetime attribute CRUD functionality
        $productDatetimeCreateProcessor = new ProductDatetimeCreateProcessor();
        $productDatetimeCreateProcessor->setUtilityClassName($utilityClassName);
        $productDatetimeCreateProcessor->setConnection($connection);
        $productDatetimeCreateProcessor->init();
        $productDatetimeAction = new ProductDatetimeAction();
        $productDatetimeAction->setCreateProcessor($productDatetimeCreateProcessor);

        // initialize the action that provides product decimal attribute CRUD functionality
        $productDecimalCreateProcessor = new ProductDecimalCreateProcessor();
        $productDecimalCreateProcessor->setUtilityClassName($utilityClassName);
        $productDecimalCreateProcessor->setConnection($connection);
        $productDecimalCreateProcessor->init();
        $productDecimalAction = new ProductDecimalAction();
        $productDecimalAction->setCreateProcessor($productDecimalCreateProcessor);

        // initialize the action that provides product integer attribute CRUD functionality
        $productIntCreateProcessor = new ProductIntCreateProcessor();
        $productIntCreateProcessor->setUtilityClassName($utilityClassName);
        $productIntCreateProcessor->setConnection($connection);
        $productIntCreateProcessor->init();
        $productIntAction = new ProductIntAction();
        $productIntAction->setCreateProcessor($productIntCreateProcessor);

        // initialize the action that provides product CRUD functionality
        $productCreateProcessor = new ProductCreateProcessor();
        $productCreateProcessor->setUtilityClassName($utilityClassName);
        $productCreateProcessor->setConnection($connection);
        $productCreateProcessor->init();
        $productDeleteProcessor = new ProductDeleteProcessor();
        $productDeleteProcessor->setUtilityClassName($utilityClassName);
        $productDeleteProcessor->setConnection($connection);
        $productDeleteProcessor->init();
        $productUpdateProcessor = new ProductUpdateProcessor();
        $productUpdateProcessor->setUtilityClassName($utilityClassName);
        $productUpdateProcessor->setConnection($connection);
        $productUpdateProcessor->init();
        $productAction = new ProductAction();
        $productAction->setCreateProcessor($productCreateProcessor);
        $productAction->setDeleteProcessor($productDeleteProcessor);
        $productAction->setUpdateProcessor($productUpdateProcessor);

        // initialize the action that provides product text attribute CRUD functionality
        $productTextCreateProcessor = new ProductTextCreateProcessor();
        $productTextCreateProcessor->setUtilityClassName($utilityClassName);
        $productTextCreateProcessor->setConnection($connection);
        $productTextCreateProcessor->init();
        $productTextAction = new ProductTextAction();
        $productTextAction->setCreateProcessor($productTextCreateProcessor);

        // initialize the action that provides product varchar attribute CRUD functionality
        $productVarcharCreateProcessor = new ProductVarcharCreateProcessor();
        $productVarcharCreateProcessor->setUtilityClassName($utilityClassName);
        $productVarcharCreateProcessor->setConnection($connection);
        $productVarcharCreateProcessor->init();
        $productVarcharAction = new ProductVarcharAction();
        $productVarcharAction->setCreateProcessor($productVarcharCreateProcessor);

        // initialize the action that provides provides product website CRUD functionality
        $productWebsiteCreateProcessor = new ProductWebsiteCreateProcessor();
        $productWebsiteCreateProcessor->setUtilityClassName($utilityClassName);
        $productWebsiteCreateProcessor->setConnection($connection);
        $productWebsiteCreateProcessor->init();
        $productWebsiteDeleteProcessor = new ProductWebsiteDeleteProcessor();
        $productWebsiteDeleteProcessor->setUtilityClassName($utilityClassName);
        $productWebsiteDeleteProcessor->setConnection($connection);
        $productWebsiteDeleteProcessor->init();
        $productWebsiteAction = new ProductWebsiteAction();
        $productWebsiteAction->setCreateProcessor($productWebsiteCreateProcessor);
        $productWebsiteAction->setDeleteProcessor($productWebsiteDeleteProcessor);

        // initialize the action that provides stock item CRUD functionality
        $stockItemCreateProcessor = new StockItemCreateProcessor();
        $stockItemCreateProcessor->setUtilityClassName($utilityClassName);
        $stockItemCreateProcessor->setConnection($connection);
        $stockItemCreateProcessor->init();
        $stockItemDeleteProcessor = new StockItemDeleteProcessor();
        $stockItemDeleteProcessor->setUtilityClassName($utilityClassName);
        $stockItemDeleteProcessor->setConnection($connection);
        $stockItemDeleteProcessor->init();
        $stockItemAction = new StockItemAction();
        $stockItemAction->setCreateProcessor($stockItemCreateProcessor);
        $stockItemAction->setDeleteProcessor($stockItemDeleteProcessor);

        // initialize the action that provides stock status CRUD functionality
        $stockStatusCreateProcessor = new StockStatusCreateProcessor();
        $stockStatusCreateProcessor->setUtilityClassName($utilityClassName);
        $stockStatusCreateProcessor->setConnection($connection);
        $stockStatusCreateProcessor->init();
        $stockStatusDeleteProcessor = new StockItemDeleteProcessor();
        $stockStatusDeleteProcessor->setUtilityClassName($utilityClassName);
        $stockStatusDeleteProcessor->setConnection($connection);
        $stockStatusDeleteProcessor->init();
        $stockStatusAction = new StockStatusAction();
        $stockStatusAction->setCreateProcessor($stockStatusCreateProcessor);
        $stockStatusAction->setDeleteProcessor($stockStatusDeleteProcessor);

        // initialize the action that provides URL rewrite CRUD functionality
        $urlRewriteCreateProcessor = new UrlRewriteCreateProcessor();
        $urlRewriteCreateProcessor->setUtilityClassName($utilityClassName);
        $urlRewriteCreateProcessor->setConnection($connection);
        $urlRewriteCreateProcessor->init();
        $urlRewriteDeleteProcessor = new UrlRewriteDeleteProcessor();
        $urlRewriteDeleteProcessor->setUtilityClassName($utilityClassName);
        $urlRewriteDeleteProcessor->setConnection($connection);
        $urlRewriteDeleteProcessor->init();
        $urlRewriteAction = new UrlRewriteAction();
        $urlRewriteAction->setCreateProcessor($urlRewriteCreateProcessor);
        $urlRewriteAction->setDeleteProcessor($urlRewriteDeleteProcessor);

        // initialize the product processor
        $processorType = static::getProcessorType();
        $productBunchProcessor = new $processorType();
        $productBunchProcessor->setConnection($connection);
        $productBunchProcessor->setProductRepository($productRepository);
        $productBunchProcessor->setUrlRewriteRepository($urlRewriteRepository);
        $productBunchProcessor->setEavAttributeOptionValueRepository($eavAttributeOptionValueRepository);
        $productBunchProcessor->setProductCategoryAction($productCategoryAction);
        $productBunchProcessor->setProductDatetimeAction($productDatetimeAction);
        $productBunchProcessor->setProductDecimalAction($productDecimalAction);
        $productBunchProcessor->setProductIntAction($productIntAction);
        $productBunchProcessor->setProductAction($productAction);
        $productBunchProcessor->setProductTextAction($productTextAction);
        $productBunchProcessor->setProductVarcharAction($productVarcharAction);
        $productBunchProcessor->setProductWebsiteAction($productWebsiteAction);
        $productBunchProcessor->setStockItemAction($stockItemAction);
        $productBunchProcessor->setStockStatusAction($stockStatusAction);
        $productBunchProcessor->setUrlRewriteAction($urlRewriteAction);

        // return the instance
        return $productBunchProcessor;
    }
}
