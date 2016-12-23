<?php

/**
 * TechDivision\Import\Cli\Services\ProductVariantProcessorFactory
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
use TechDivision\Import\Repositories\EavAttributeRepository;
use TechDivision\Import\Repositories\EavAttributeOptionValueRepository;
use TechDivision\Import\Product\Variant\Actions\ProductRelationAction;
use TechDivision\Import\Product\Variant\Actions\ProductSuperAttributeAction;
use TechDivision\Import\Product\Variant\Actions\ProductSuperAttributeLabelAction;
use TechDivision\Import\Product\Variant\Actions\ProductSuperLinkAction;
use TechDivision\Import\Product\Variant\Actions\Processors\ProductRelationCreateProcessor;
use TechDivision\Import\Product\Variant\Actions\Processors\ProductSuperLinkCreateProcessor;
use TechDivision\Import\Product\Variant\Actions\Processors\ProductSuperAttributeCreateProcessor;
use TechDivision\Import\Product\Variant\Actions\Processors\ProductSuperAttributeLabelCreateProcessor;

/**
 * Factory to create a new product variant processor.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class ProductVariantProcessorFactory extends AbstractProductProcessorFactory
{

    /**
     * Return's the processor class name.
     *
     * @return string The processor class name
     */
    protected static function getProcessorType()
    {
        return 'TechDivision\Import\Product\Variant\Services\ProductVariantProcessor';
    }

    /**
     * Factory method to create a new product variant processor instance.
     *
     * @param \PDO                                               $connection    The PDO connection to use
     * @param TechDivision\Import\Configuration\SubjectInterface $configuration The subject configuration
     *
     * @return \TechDivision\Import\Product\Variant\Services\ProductVariantProcessor The processor instance
     */
    public static function factory(\PDO $connection, SubjectInterface $configuration)
    {

        // load the utility class name
        $utilityClassName = $configuration->getUtilityClassName();

        // initialize the repository that provides EAV attribute query functionality
        $eavAttributeRepository = new EavAttributeRepository();
        $eavAttributeRepository->setUtilityClassName($utilityClassName);
        $eavAttributeRepository->setConnection($connection);
        $eavAttributeRepository->init();

        // initialize the repository that provides EAV attribute option value query functionality
        $eavAttributeOptionValueRepository = new EavAttributeOptionValueRepository();
        $eavAttributeOptionValueRepository->setUtilityClassName($utilityClassName);
        $eavAttributeOptionValueRepository->setConnection($connection);
        $eavAttributeOptionValueRepository->init();

        // initialize the action that provides product relation CRUD functionality
        $productRelationCreateProcessor = new ProductRelationCreateProcessor();
        $productRelationCreateProcessor->setUtilityClassName($utilityClassName);
        $productRelationCreateProcessor->setConnection($connection);
        $productRelationCreateProcessor->init();
        $productRelationAction = new ProductRelationAction();
        $productRelationAction->setCreateProcessor($productRelationCreateProcessor);

        // initialize the action that provides product super attribute CRUD functionality
        $productSuperAttributeCreateProcessor = new ProductSuperAttributeCreateProcessor();
        $productSuperAttributeCreateProcessor->setUtilityClassName($utilityClassName);
        $productSuperAttributeCreateProcessor->setConnection($connection);
        $productSuperAttributeCreateProcessor->init();
        $productSuperAttributeAction = new ProductSuperAttributeAction();
        $productSuperAttributeAction->setCreateProcessor($productSuperAttributeCreateProcessor);

        // initialize the action that provides product super attribute label CRUD functionality
        $productSuperAttributeLabelCreateProcessor = new ProductSuperAttributeLabelCreateProcessor();
        $productSuperAttributeLabelCreateProcessor->setUtilityClassName($utilityClassName);
        $productSuperAttributeLabelCreateProcessor->setConnection($connection);
        $productSuperAttributeLabelCreateProcessor->init();
        $productSuperAttributeLabelAction = new ProductSuperAttributeLabelAction();
        $productSuperAttributeLabelAction->setCreateProcessor($productSuperAttributeLabelCreateProcessor);

        // initialize the action that provides product super link CRUD functionality
        $productSuperLinkCreateProcessor = new ProductSuperLinkCreateProcessor();
        $productSuperLinkCreateProcessor->setUtilityClassName($utilityClassName);
        $productSuperLinkCreateProcessor->setConnection($connection);
        $productSuperLinkCreateProcessor->init();
        $productSuperLinkAction = new ProductSuperLinkAction();
        $productSuperLinkAction->setCreateProcessor($productSuperLinkCreateProcessor);

        // initialize the product variant processor
        $processorType = ProductVariantProcessorFactory::getProcessorType();
        $productVariantProcessor = new $processorType();
        $productVariantProcessor->setConnection($connection);
        $productVariantProcessor->setEavAttributeOptionValueRepository($eavAttributeOptionValueRepository);
        $productVariantProcessor->setEavAttributeRepository($eavAttributeRepository);
        $productVariantProcessor->setProductRelationAction($productRelationAction);
        $productVariantProcessor->setProductSuperLinkAction($productSuperLinkAction);
        $productVariantProcessor->setProductSuperAttributeAction($productSuperAttributeAction);
        $productVariantProcessor->setProductSuperAttributeLabelAction($productSuperAttributeLabelAction);

        // return the instance
        return $productVariantProcessor;
    }
}
