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

use TechDivision\Import\Configuration\SubjectConfigurationInterface;
use TechDivision\Import\Repositories\EavAttributeRepository;
use TechDivision\Import\Repositories\EavAttributeOptionValueRepository;
use TechDivision\Import\Product\Repositories\ProductRepository;
use TechDivision\Import\Product\Repositories\ProductWebsiteRepository;
use TechDivision\Import\Product\Repositories\ProductDatetimeRepository;
use TechDivision\Import\Product\Repositories\ProductDecimalRepository;
use TechDivision\Import\Product\Repositories\ProductIntRepository;
use TechDivision\Import\Product\Repositories\ProductTextRepository;
use TechDivision\Import\Product\Repositories\ProductVarcharRepository;
use TechDivision\Import\Product\Repositories\CategoryProductRepository;
use TechDivision\Import\Product\Repositories\StockStatusRepository;
use TechDivision\Import\Product\Repositories\StockItemRepository;
use TechDivision\Import\Product\Repositories\UrlRewriteRepository;
use TechDivision\Import\Product\Repositories\UrlRewriteProductCategoryRepository;
use TechDivision\Import\Product\Actions\UrlRewriteAction;
use TechDivision\Import\Product\Actions\UrlRewriteProductCategoryAction;
use TechDivision\Import\Product\Actions\ProductAction;
use TechDivision\Import\Product\Actions\CategoryProductAction;
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
use TechDivision\Import\Product\Actions\Processors\CategoryProductDeleteProcessor;
use TechDivision\Import\Product\Actions\Processors\CategoryProductCreateProcessor;
use TechDivision\Import\Product\Actions\Processors\CategoryProductUpdateProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductDatetimeCreateProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductDatetimeUpdateProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductDecimalCreateProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductDecimalUpdateProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductIntCreateProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductIntUpdateProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductTextCreateProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductTextUpdateProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductVarcharCreateProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductVarcharUpdateProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductWebsiteDeleteProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductWebsiteCreateProcessor;
use TechDivision\Import\Product\Actions\Processors\StockItemDeleteProcessor;
use TechDivision\Import\Product\Actions\Processors\StockItemCreateProcessor;
use TechDivision\Import\Product\Actions\Processors\StockItemUpdateProcessor;
use TechDivision\Import\Product\Actions\Processors\StockStatusDeleteProcessor;
use TechDivision\Import\Product\Actions\Processors\StockStatusCreateProcessor;
use TechDivision\Import\Product\Actions\Processors\StockStatusUpdateProcessor;
use TechDivision\Import\Product\Actions\Processors\UrlRewriteCreateProcessor;
use TechDivision\Import\Product\Actions\Processors\UrlRewriteDeleteProcessor;
use TechDivision\Import\Product\Actions\Processors\UrlRewriteUpdateProcessor;
use TechDivision\Import\Product\Actions\Processors\UrlRewriteProductCategoryCreateProcessor;
use TechDivision\Import\Product\Actions\Processors\UrlRewriteProductCategoryDeleteProcessor;
use TechDivision\Import\Product\Actions\Processors\UrlRewriteProductCategoryUpdateProcessor;

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
     * @param \PDO                                                            $connection    The PDO connection to use
     * @param TechDivision\Import\Configuration\SubjectConfigurationInterface $configuration The subject configuration
     *
     * @return \TechDivision\Import\Product\Services\ProductProcessor The processor instance
     */
    public static function factory(\PDO $connection, SubjectConfigurationInterface $configuration)
    {

        // load the utility class name
        $utilityClassName = $configuration->getUtilityClassName();

        // initialize the repository that provides EAV attribute option value query functionality
        $eavAttributeOptionValueRepository = new EavAttributeOptionValueRepository();
        $eavAttributeOptionValueRepository->setUtilityClassName($utilityClassName);
        $eavAttributeOptionValueRepository->setConnection($connection);
        $eavAttributeOptionValueRepository->init();

        // initialize the repository that provides EAV attribute query functionality
        $eavAttributeRepository = new EavAttributeRepository();
        $eavAttributeRepository->setUtilityClassName($utilityClassName);
        $eavAttributeRepository->setConnection($connection);
        $eavAttributeRepository->init();

        // initialize the repository that provides product query functionality
        $productRepository = new ProductRepository();
        $productRepository->setUtilityClassName($utilityClassName);
        $productRepository->setConnection($connection);
        $productRepository->init();

        // initialize the repository that provides product website relation query functionality
        $productWebsiteRepository = new ProductWebsiteRepository();
        $productWebsiteRepository->setUtilityClassName($utilityClassName);
        $productWebsiteRepository->setConnection($connection);
        $productWebsiteRepository->init();

        // initialize the repository that provides product datetime attribute query functionality
        $productDatetimeRepository = new ProductDatetimeRepository();
        $productDatetimeRepository->setUtilityClassName($utilityClassName);
        $productDatetimeRepository->setConnection($connection);
        $productDatetimeRepository->init();

        // initialize the repository that provides product decimal attribute query functionality
        $productDecimalRepository = new ProductDecimalRepository();
        $productDecimalRepository->setUtilityClassName($utilityClassName);
        $productDecimalRepository->setConnection($connection);
        $productDecimalRepository->init();

        // initialize the repository that provides product integer attribute query functionality
        $productIntRepository = new ProductIntRepository();
        $productIntRepository->setUtilityClassName($utilityClassName);
        $productIntRepository->setConnection($connection);
        $productIntRepository->init();

        // initialize the repository that provides product text attribute query functionality
        $productTextRepository = new ProductTextRepository();
        $productTextRepository->setUtilityClassName($utilityClassName);
        $productTextRepository->setConnection($connection);
        $productTextRepository->init();

        // initialize the repository that provides product varchar attribute query functionality
        $productVarcharRepository = new ProductVarcharRepository();
        $productVarcharRepository->setUtilityClassName($utilityClassName);
        $productVarcharRepository->setConnection($connection);
        $productVarcharRepository->init();

        // initialize the repository that provides category product relation query functionality
        $categoryProductRepository = new CategoryProductRepository();
        $categoryProductRepository->setUtilityClassName($utilityClassName);
        $categoryProductRepository->setConnection($connection);
        $categoryProductRepository->init();

        // initialize the repository that provides stock status query functionality
        $stockStatusRepository = new StockStatusRepository();
        $stockStatusRepository->setUtilityClassName($utilityClassName);
        $stockStatusRepository->setConnection($connection);
        $stockStatusRepository->init();

        // initialize the repository that provides stock item query functionality
        $stockItemRepository = new StockItemRepository();
        $stockItemRepository->setUtilityClassName($utilityClassName);
        $stockItemRepository->setConnection($connection);
        $stockItemRepository->init();

        // initialize the repository that provides URL rewrite query functionality
        $urlRewriteRepository = new UrlRewriteRepository();
        $urlRewriteRepository->setUtilityClassName($utilityClassName);
        $urlRewriteRepository->setConnection($connection);
        $urlRewriteRepository->init();

        // initialize the repository that provides URL rewrite product category query functionality
        $urlRewriteProductCategoryRepository = new UrlRewriteProductCategoryRepository();
        $urlRewriteProductCategoryRepository->setUtilityClassName($utilityClassName);
        $urlRewriteProductCategoryRepository->setConnection($connection);
        $urlRewriteProductCategoryRepository->init();

        // initialize the action that provides category product relation CRUD functionality
        $categoryProductCreateProcessor = new CategoryProductCreateProcessor();
        $categoryProductCreateProcessor->setUtilityClassName($utilityClassName);
        $categoryProductCreateProcessor->setConnection($connection);
        $categoryProductCreateProcessor->init();
        $categoryProductDeleteProcessor = new CategoryProductDeleteProcessor();
        $categoryProductDeleteProcessor->setUtilityClassName($utilityClassName);
        $categoryProductDeleteProcessor->setConnection($connection);
        $categoryProductDeleteProcessor->init();
        $categoryProductUpdateProcessor = new CategoryProductUpdateProcessor();
        $categoryProductUpdateProcessor->setUtilityClassName($utilityClassName);
        $categoryProductUpdateProcessor->setConnection($connection);
        $categoryProductUpdateProcessor->init();
        $categoryProductAction = new CategoryProductAction();
        $categoryProductAction->setCreateProcessor($categoryProductCreateProcessor);
        $categoryProductAction->setDeleteProcessor($categoryProductDeleteProcessor);
        $categoryProductAction->setUpdateProcessor($categoryProductUpdateProcessor);

        // initialize the action that provides product datetime attribute CRUD functionality
        $productDatetimeCreateProcessor = new ProductDatetimeCreateProcessor();
        $productDatetimeCreateProcessor->setUtilityClassName($utilityClassName);
        $productDatetimeCreateProcessor->setConnection($connection);
        $productDatetimeCreateProcessor->init();
        $productDatetimeUpdateProcessor = new ProductDatetimeUpdateProcessor();
        $productDatetimeUpdateProcessor->setUtilityClassName($utilityClassName);
        $productDatetimeUpdateProcessor->setConnection($connection);
        $productDatetimeUpdateProcessor->init();
        $productDatetimeAction = new ProductDatetimeAction();
        $productDatetimeAction->setCreateProcessor($productDatetimeCreateProcessor);
        $productDatetimeAction->setUpdateProcessor($productDatetimeUpdateProcessor);

        // initialize the action that provides product decimal attribute CRUD functionality
        $productDecimalCreateProcessor = new ProductDecimalCreateProcessor();
        $productDecimalCreateProcessor->setUtilityClassName($utilityClassName);
        $productDecimalCreateProcessor->setConnection($connection);
        $productDecimalCreateProcessor->init();
        $productDecimalUpdateProcessor = new ProductDecimalUpdateProcessor();
        $productDecimalUpdateProcessor->setUtilityClassName($utilityClassName);
        $productDecimalUpdateProcessor->setConnection($connection);
        $productDecimalUpdateProcessor->init();
        $productDecimalAction = new ProductDecimalAction();
        $productDecimalAction->setCreateProcessor($productDecimalCreateProcessor);
        $productDecimalAction->setUpdateProcessor($productDecimalUpdateProcessor);

        // initialize the action that provides product integer attribute CRUD functionality
        $productIntCreateProcessor = new ProductIntCreateProcessor();
        $productIntCreateProcessor->setUtilityClassName($utilityClassName);
        $productIntCreateProcessor->setConnection($connection);
        $productIntCreateProcessor->init();
        $productIntUpdateProcessor = new ProductIntUpdateProcessor();
        $productIntUpdateProcessor->setUtilityClassName($utilityClassName);
        $productIntUpdateProcessor->setConnection($connection);
        $productIntUpdateProcessor->init();
        $productIntAction = new ProductIntAction();
        $productIntAction->setCreateProcessor($productIntCreateProcessor);
        $productIntAction->setUpdateProcessor($productIntUpdateProcessor);

        // initialize the action that provides product text attribute CRUD functionality
        $productTextCreateProcessor = new ProductTextCreateProcessor();
        $productTextCreateProcessor->setUtilityClassName($utilityClassName);
        $productTextCreateProcessor->setConnection($connection);
        $productTextCreateProcessor->init();
        $productTextUpdateProcessor = new ProductTextUpdateProcessor();
        $productTextUpdateProcessor->setUtilityClassName($utilityClassName);
        $productTextUpdateProcessor->setConnection($connection);
        $productTextUpdateProcessor->init();
        $productTextAction = new ProductTextAction();
        $productTextAction->setCreateProcessor($productTextCreateProcessor);
        $productTextAction->setUpdateProcessor($productTextUpdateProcessor);

        // initialize the action that provides product varchar attribute CRUD functionality
        $productVarcharCreateProcessor = new ProductVarcharCreateProcessor();
        $productVarcharCreateProcessor->setUtilityClassName($utilityClassName);
        $productVarcharCreateProcessor->setConnection($connection);
        $productVarcharCreateProcessor->init();
        $productVarcharUpdateProcessor = new ProductVarcharUpdateProcessor();
        $productVarcharUpdateProcessor->setUtilityClassName($utilityClassName);
        $productVarcharUpdateProcessor->setConnection($connection);
        $productVarcharUpdateProcessor->init();
        $productVarcharAction = new ProductVarcharAction();
        $productVarcharAction->setCreateProcessor($productVarcharCreateProcessor);
        $productVarcharAction->setUpdateProcessor($productVarcharUpdateProcessor);

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
        $stockItemUpdateProcessor = new StockItemUpdateProcessor();
        $stockItemUpdateProcessor->setUtilityClassName($utilityClassName);
        $stockItemUpdateProcessor->setConnection($connection);
        $stockItemUpdateProcessor->init();
        $stockItemAction = new StockItemAction();
        $stockItemAction->setCreateProcessor($stockItemCreateProcessor);
        $stockItemAction->setDeleteProcessor($stockItemDeleteProcessor);
        $stockItemAction->setUpdateProcessor($stockItemUpdateProcessor);

        // initialize the action that provides stock status CRUD functionality
        $stockStatusCreateProcessor = new StockStatusCreateProcessor();
        $stockStatusCreateProcessor->setUtilityClassName($utilityClassName);
        $stockStatusCreateProcessor->setConnection($connection);
        $stockStatusCreateProcessor->init();
        $stockStatusDeleteProcessor = new StockStatusDeleteProcessor();
        $stockStatusDeleteProcessor->setUtilityClassName($utilityClassName);
        $stockStatusDeleteProcessor->setConnection($connection);
        $stockStatusDeleteProcessor->init();
        $stockStatusUpdateProcessor = new StockStatusUpdateProcessor();
        $stockStatusUpdateProcessor->setUtilityClassName($utilityClassName);
        $stockStatusUpdateProcessor->setConnection($connection);
        $stockStatusUpdateProcessor->init();
        $stockStatusAction = new StockStatusAction();
        $stockStatusAction->setCreateProcessor($stockStatusCreateProcessor);
        $stockStatusAction->setDeleteProcessor($stockStatusDeleteProcessor);
        $stockStatusAction->setUpdateProcessor($stockStatusUpdateProcessor);

        // initialize the action that provides URL rewrite CRUD functionality
        $urlRewriteCreateProcessor = new UrlRewriteCreateProcessor();
        $urlRewriteCreateProcessor->setUtilityClassName($utilityClassName);
        $urlRewriteCreateProcessor->setConnection($connection);
        $urlRewriteCreateProcessor->init();
        $urlRewriteDeleteProcessor = new UrlRewriteDeleteProcessor();
        $urlRewriteDeleteProcessor->setUtilityClassName($utilityClassName);
        $urlRewriteDeleteProcessor->setConnection($connection);
        $urlRewriteDeleteProcessor->init();
        $urlRewriteUpdateProcessor = new UrlRewriteUpdateProcessor();
        $urlRewriteUpdateProcessor->setUtilityClassName($utilityClassName);
        $urlRewriteUpdateProcessor->setConnection($connection);
        $urlRewriteUpdateProcessor->init();
        $urlRewriteAction = new UrlRewriteAction();
        $urlRewriteAction->setCreateProcessor($urlRewriteCreateProcessor);
        $urlRewriteAction->setDeleteProcessor($urlRewriteDeleteProcessor);
        $urlRewriteAction->setUpdateProcessor($urlRewriteUpdateProcessor);

        // initialize the action that provides URL rewrite CRUD functionality
        $urlRewriteProductCategoryCreateProcessor = new UrlRewriteProductCategoryCreateProcessor();
        $urlRewriteProductCategoryCreateProcessor->setUtilityClassName($utilityClassName);
        $urlRewriteProductCategoryCreateProcessor->setConnection($connection);
        $urlRewriteProductCategoryCreateProcessor->init();
        $urlRewriteProductCategoryDeleteProcessor = new UrlRewriteProductCategoryDeleteProcessor();
        $urlRewriteProductCategoryDeleteProcessor->setUtilityClassName($utilityClassName);
        $urlRewriteProductCategoryDeleteProcessor->setConnection($connection);
        $urlRewriteProductCategoryDeleteProcessor->init();
        $urlRewriteProductCategoryUpdateProcessor = new UrlRewriteProductCategoryUpdateProcessor();
        $urlRewriteProductCategoryUpdateProcessor->setUtilityClassName($utilityClassName);
        $urlRewriteProductCategoryUpdateProcessor->setConnection($connection);
        $urlRewriteProductCategoryUpdateProcessor->init();
        $urlRewriteProductCategoryAction = new UrlRewriteProductCategoryAction();
        $urlRewriteProductCategoryAction->setCreateProcessor($urlRewriteProductCategoryCreateProcessor);
        $urlRewriteProductCategoryAction->setDeleteProcessor($urlRewriteProductCategoryDeleteProcessor);
        $urlRewriteProductCategoryAction->setUpdateProcessor($urlRewriteProductCategoryUpdateProcessor);

        // initialize the product processor
        $processorType = static::getProcessorType();
        $productBunchProcessor = new $processorType();
        $productBunchProcessor->setConnection($connection);
        $productBunchProcessor->setProductRepository($productRepository);
        $productBunchProcessor->setProductWebsiteRepository($productWebsiteRepository);
        $productBunchProcessor->setProductDatetimeRepository($productDatetimeRepository);
        $productBunchProcessor->setProductDecimalRepository($productDecimalRepository);
        $productBunchProcessor->setProductIntRepository($productIntRepository);
        $productBunchProcessor->setProductTextRepository($productTextRepository);
        $productBunchProcessor->setProductVarcharRepository($productVarcharRepository);
        $productBunchProcessor->setCategoryProductRepository($categoryProductRepository);
        $productBunchProcessor->setStockStatusRepository($stockStatusRepository);
        $productBunchProcessor->setStockItemRepository($stockItemRepository);
        $productBunchProcessor->setUrlRewriteRepository($urlRewriteRepository);
        $productBunchProcessor->setUrlRewriteProductCategoryRepository($urlRewriteProductCategoryRepository);
        $productBunchProcessor->setEavAttributeOptionValueRepository($eavAttributeOptionValueRepository);
        $productBunchProcessor->setEavAttributeRepository($eavAttributeRepository);
        $productBunchProcessor->setCategoryProductAction($categoryProductAction);
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
        $productBunchProcessor->setUrlRewriteProductCategoryAction($urlRewriteProductCategoryAction);

        // return the instance
        return $productBunchProcessor;
    }
}
