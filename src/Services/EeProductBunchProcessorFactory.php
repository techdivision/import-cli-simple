<?php

/**
 * TechDivision\Import\Cli\Services\EeProductBunchProcessorFactory
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
use TechDivision\Import\Product\Ee\Repositories\ProductDatetimeRepository;
use TechDivision\Import\Product\Ee\Repositories\ProductDecimalRepository;
use TechDivision\Import\Product\Ee\Repositories\ProductIntRepository;
use TechDivision\Import\Product\Ee\Repositories\ProductTextRepository;
use TechDivision\Import\Product\Ee\Repositories\ProductVarcharRepository;
use TechDivision\Import\Product\Ee\Actions\SequenceProductAction;
use TechDivision\Import\Product\Ee\Actions\Processors\ProductUpdateProcessor;
use TechDivision\Import\Product\Ee\Actions\Processors\SequenceProductCreateProcessor;
use TechDivision\Import\Repositories\EavAttributeOptionValueRepository;
use TechDivision\Import\Repositories\EavAttributeRepository;
use TechDivision\Import\Product\Repositories\ProductRepository;
use TechDivision\Import\Product\Repositories\ProductWebsiteRepository;
use TechDivision\Import\Product\Repositories\CategoryProductRepository;
use TechDivision\Import\Product\Repositories\StockStatusRepository;
use TechDivision\Import\Product\Repositories\StockItemRepository;
use TechDivision\Import\Repositories\UrlRewriteRepository;
use TechDivision\Import\Product\Repositories\UrlRewriteProductCategoryRepository;
use TechDivision\Import\Product\Actions\CategoryProductAction;
use TechDivision\Import\Product\Actions\ProductDatetimeAction;
use TechDivision\Import\Product\Actions\ProductDecimalAction;
use TechDivision\Import\Product\Actions\ProductIntAction;
use TechDivision\Import\Product\Actions\ProductTextAction;
use TechDivision\Import\Product\Actions\ProductVarcharAction;
use TechDivision\Import\Product\Actions\ProductAction;
use TechDivision\Import\Product\Actions\ProductWebsiteAction;
use TechDivision\Import\Product\Actions\StockItemAction;
use TechDivision\Import\Product\Actions\StockStatusAction;
use TechDivision\Import\Actions\UrlRewriteAction;
use TechDivision\Import\Product\Actions\UrlRewriteProductCategoryAction;
use TechDivision\Import\Product\Actions\Processors\CategoryProductCreateProcessor;
use TechDivision\Import\Product\Actions\Processors\CategoryProductUpdateProcessor;
use TechDivision\Import\Product\Actions\Processors\CategoryProductDeleteProcessor;
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
use TechDivision\Import\Product\Actions\Processors\ProductCreateProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductDeleteProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductWebsiteCreateProcessor;
use TechDivision\Import\Product\Actions\Processors\ProductWebsiteDeleteProcessor;
use TechDivision\Import\Product\Actions\Processors\StockItemCreateProcessor;
use TechDivision\Import\Product\Actions\Processors\StockItemUpdateProcessor;
use TechDivision\Import\Product\Actions\Processors\StockItemDeleteProcessor;
use TechDivision\Import\Product\Actions\Processors\StockStatusCreateProcessor;
use TechDivision\Import\Product\Actions\Processors\StockStatusUpdateProcessor;
use TechDivision\Import\Product\Actions\Processors\StockStatusDeleteProcessor;
use TechDivision\Import\Actions\Processors\UrlRewriteCreateProcessor;
use TechDivision\Import\Actions\Processors\UrlRewriteUpdateProcessor;
use TechDivision\Import\Actions\Processors\UrlRewriteDeleteProcessor;
use TechDivision\Import\Product\Actions\Processors\UrlRewriteProductCategoryCreateProcessor;
use TechDivision\Import\Product\Actions\Processors\UrlRewriteProductCategoryUpdateProcessor;
use TechDivision\Import\Product\Actions\Processors\UrlRewriteProductCategoryDeleteProcessor;

/**
 * A SLSB providing methods to load product data using a PDO connection.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class EeProductBunchProcessorFactory extends ProductBunchProcessorFactory
{

    /**
     * Return's the processor class name.
     *
     * @return string The processor class name
     */
    protected static function getProcessorType()
    {
        return 'TechDivision\Import\Product\Ee\Services\EeProductBunchProcessor';
    }

    /**
     * Factory method to create a new product processor instance.
     *
     * @param \PDO                                                              $connection    The PDO connection to use
     * @param TechDivision\Import\Configuration\ProcessorConfigurationInterface $configuration The subject configuration
     *
     * @return \TechDivision\Import\Product\Services\ProductProcessor The processor instance
     */
    public static function factory(\PDO $connection, ProcessorConfigurationInterface $configuration)
    {

        // load the utility class name
        $utilityClassName = $configuration->getUtilityClassName();

        // initialize the repository that provides EAV attribute option value query functionality
        $eavAttributeOptionValueRepository = new EavAttributeOptionValueRepository($connection, $utilityClassName);

        // initialize the repository that provides EAV attribute query functionality
        $eavAttributeRepository = new EavAttributeRepository($connection, $utilityClassName);

        // initialize the repository that provides product query functionality
        $productRepository = new ProductRepository($connection, $utilityClassName);

        // initialize the repository that provides product website relation query functionality
        $productWebsiteRepository = new ProductWebsiteRepository($connection, $utilityClassName);

        // initialize the repository that provides product datetime attribute query functionality
        $productDatetimeRepository = new ProductDatetimeRepository($connection, $utilityClassName);

        // initialize the repository that provides product decimal attribute query functionality
        $productDecimalRepository = new ProductDecimalRepository($connection, $utilityClassName);

        // initialize the repository that provides product integer attribute query functionality
        $productIntRepository = new ProductIntRepository($connection, $utilityClassName);

        // initialize the repository that provides product text attribute query functionality
        $productTextRepository = new ProductTextRepository($connection, $utilityClassName);

        // initialize the repository that provides product varchar attribute query functionality
        $productVarcharRepository = new ProductVarcharRepository($connection, $utilityClassName);

        // initialize the repository that provides category product relation query functionality
        $categoryProductRepository = new CategoryProductRepository($connection, $utilityClassName);

        // initialize the repository that provides stock status query functionality
        $stockStatusRepository = new StockStatusRepository($connection, $utilityClassName);

        // initialize the repository that provides stock item query functionality
        $stockItemRepository = new StockItemRepository($connection, $utilityClassName);

        // initialize the repository that provides URL rewrite query functionality
        $urlRewriteRepository = new UrlRewriteRepository($connection, $utilityClassName);

        // initialize the repository that provides URL rewrite product category query functionality
        $urlRewriteProductCategoryRepository = new UrlRewriteProductCategoryRepository($connection, $utilityClassName);

        // initialize the action that provides sequence product CRUD functionality
        $sequenceProductAction = new SequenceProductAction(
            new SequenceProductCreateProcessor($connection, $utilityClassName)
        );

        // initialize the action that provides category product relation CRUD functionality
        $categoryProductAction = new CategoryProductAction(
            new CategoryProductCreateProcessor($connection, $utilityClassName),
            new CategoryProductUpdateProcessor($connection, $utilityClassName),
            new CategoryProductDeleteProcessor($connection, $utilityClassName)
        );

        // initialize the action that provides product datetime attribute CRUD functionality
        $productDatetimeAction = new ProductDatetimeAction(
            new ProductDatetimeCreateProcessor($connection, $utilityClassName),
            new ProductDatetimeUpdateProcessor($connection, $utilityClassName)
        );

        // initialize the action that provides product decimal attribute CRUD functionality
        $productDecimalAction = new ProductDecimalAction(
            new ProductDecimalCreateProcessor($connection, $utilityClassName),
            new ProductDecimalUpdateProcessor($connection, $utilityClassName)
        );

        // initialize the action that provides product integer attribute CRUD functionality
        $productIntAction = new ProductIntAction(
            new ProductIntCreateProcessor($connection, $utilityClassName),
            new ProductIntUpdateProcessor($connection, $utilityClassName)
        );

        // initialize the action that provides product text attribute CRUD functionality
        $productTextAction = new ProductTextAction(
            new ProductTextCreateProcessor($connection, $utilityClassName),
            new ProductTextUpdateProcessor($connection, $utilityClassName)
        );

        // initialize the action that provides product varchar attribute CRUD functionality
        $productVarcharAction = new ProductVarcharAction(
            new ProductVarcharCreateProcessor($connection, $utilityClassName),
            new ProductVarcharUpdateProcessor($connection, $utilityClassName)
        );

        // initialize the action that provides product CRUD functionality
        $productAction = new ProductAction(
            new ProductCreateProcessor($connection, $utilityClassName),
            new ProductUpdateProcessor($connection, $utilityClassName),
            new ProductDeleteProcessor($connection, $utilityClassName)
        );

        // initialize the action that provides provides product website CRUD functionality
        $productWebsiteAction = new ProductWebsiteAction(
            new ProductWebsiteCreateProcessor($connection, $utilityClassName),
            null,
            new ProductWebsiteDeleteProcessor($connection, $utilityClassName)
        );

        // initialize the action that provides stock item CRUD functionality
        $stockItemAction = new StockItemAction(
            new StockItemCreateProcessor($connection, $utilityClassName),
            new StockItemUpdateProcessor($connection, $utilityClassName),
            new StockItemDeleteProcessor($connection, $utilityClassName)
        );

        // initialize the action that provides stock status CRUD functionality
        $stockStatusAction = new StockStatusAction(
            new StockStatusCreateProcessor($connection, $utilityClassName),
            new StockStatusUpdateProcessor($connection, $utilityClassName),
            new StockStatusDeleteProcessor($connection, $utilityClassName)
        );

        // initialize the action that provides URL rewrite CRUD functionality
        $urlRewriteAction = new UrlRewriteAction(
            new UrlRewriteCreateProcessor($connection, $utilityClassName),
            new UrlRewriteUpdateProcessor($connection, $utilityClassName),
            new UrlRewriteDeleteProcessor($connection, $utilityClassName)
        );

        // initialize the action that provides URL rewrite CRUD functionality
        $urlRewriteProductCategoryAction = new UrlRewriteProductCategoryAction(
            new UrlRewriteProductCategoryCreateProcessor($connection, $utilityClassName),
            new UrlRewriteProductCategoryUpdateProcessor($connection, $utilityClassName),
            new UrlRewriteProductCategoryDeleteProcessor($connection, $utilityClassName)
        );

        // initialize the product processor
        $processorType = static::getProcessorType();
        return new $processorType(
            $connection,
            $sequenceProductAction,
            $productRepository,
            $productWebsiteRepository,
            $productDatetimeRepository,
            $productDecimalRepository,
            $productIntRepository,
            $productTextRepository,
            $productVarcharRepository,
            $categoryProductRepository,
            $stockStatusRepository,
            $stockItemRepository,
            $urlRewriteRepository,
            $urlRewriteProductCategoryRepository,
            $eavAttributeOptionValueRepository,
            $eavAttributeRepository,
            $categoryProductAction,
            $productDatetimeAction,
            $productDecimalAction,
            $productIntAction,
            $productAction,
            $productTextAction,
            $productVarcharAction,
            $productWebsiteAction,
            $stockItemAction,
            $stockStatusAction,
            $urlRewriteAction,
            $urlRewriteProductCategoryAction
        );
    }
}
