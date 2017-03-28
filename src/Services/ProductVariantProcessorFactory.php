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

use TechDivision\Import\Configuration\ProcessorConfigurationInterface;
use TechDivision\Import\Repositories\EavAttributeRepository;
use TechDivision\Import\Repositories\EavAttributeOptionValueRepository;
use TechDivision\Import\Product\Variant\Repositories\ProductRelationRepository;
use TechDivision\Import\Product\Variant\Repositories\ProductSuperLinkRepository;
use TechDivision\Import\Product\Variant\Repositories\ProductSuperAttributeRepository;
use TechDivision\Import\Product\Variant\Repositories\ProductSuperAttributeLabelRepository;
use TechDivision\Import\Product\Variant\Actions\ProductRelationAction;
use TechDivision\Import\Product\Variant\Actions\ProductSuperAttributeAction;
use TechDivision\Import\Product\Variant\Actions\ProductSuperAttributeLabelAction;
use TechDivision\Import\Product\Variant\Actions\ProductSuperLinkAction;
use TechDivision\Import\Product\Variant\Actions\Processors\ProductRelationCreateProcessor;
use TechDivision\Import\Product\Variant\Actions\Processors\ProductSuperLinkCreateProcessor;
use TechDivision\Import\Product\Variant\Actions\Processors\ProductSuperAttributeCreateProcessor;
use TechDivision\Import\Product\Variant\Actions\Processors\ProductSuperAttributeUpdateProcessor;
use TechDivision\Import\Product\Variant\Actions\Processors\ProductSuperAttributeLabelCreateProcessor;
use TechDivision\Import\Product\Variant\Actions\Processors\ProductSuperAttributeLabelUpdateProcessor;

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
     * @param \PDO                                                              $connection    The PDO connection to use
     * @param TechDivision\Import\Configuration\ProcessorConfigurationInterface $configuration The subject configuration
     *
     * @return \TechDivision\Import\Product\Variant\Services\ProductVariantProcessor The processor instance
     */
    public static function factory(\PDO $connection, ProcessorConfigurationInterface $configuration)
    {

        // load the utility class name
        $utilityClassName = $configuration->getUtilityClassName();

        // initialize the repository that provides EAV attribute query functionality
        $eavAttributeRepository = new EavAttributeRepository($connection, $utilityClassName);

        // initialize the repository that provides EAV attribute option value query functionality
        $eavAttributeOptionValueRepository = new EavAttributeOptionValueRepository($connection, $utilityClassName);

        // initialize the repository that provides product relation query functionality
        $productRelationRepository = new ProductRelationRepository($connection, $utilityClassName);

        // initialize the repository that provides product super link query functionality
        $productSuperLinkRepository = new ProductSuperLinkRepository($connection, $utilityClassName);

        // initialize the repository that provides product super attribute query functionality
        $productSuperAttributeRepository = new ProductSuperAttributeRepository($connection, $utilityClassName);

        // initialize the repository that provides product super attribute label query functionality
        $productSuperAttributeLabelRepository = new ProductSuperAttributeLabelRepository($connection, $utilityClassName);

        // initialize the action that provides product relation CRUD functionality
        $productRelationAction = new ProductRelationAction(
            new ProductRelationCreateProcessor($connection, $utilityClassName)
        );

        // initialize the action that provides product super attribute CRUD functionality
        $productSuperAttributeAction = new ProductSuperAttributeAction(
            new ProductSuperAttributeCreateProcessor($connection, $utilityClassName),
            new ProductSuperAttributeUpdateProcessor($connection, $utilityClassName)
        );

        // initialize the action that provides product super attribute label CRUD functionality
        $productSuperAttributeLabelAction = new ProductSuperAttributeLabelAction(
            new ProductSuperAttributeLabelCreateProcessor($connection, $utilityClassName),
            new ProductSuperAttributeLabelUpdateProcessor($connection, $utilityClassName)
        );

        // initialize the action that provides product super link CRUD functionality
        $productSuperLinkAction = new ProductSuperLinkAction(
            new ProductSuperLinkCreateProcessor($connection, $utilityClassName)
        );

        // initialize and return the product variant processor
        $processorType = static::getProcessorType();
        return new $processorType(
            $connection,
            $eavAttributeOptionValueRepository,
            $eavAttributeRepository,
            $productRelationRepository,
            $productSuperLinkRepository,
            $productSuperAttributeRepository,
            $productSuperAttributeLabelRepository,
            $productRelationAction,
            $productSuperLinkAction,
            $productSuperAttributeAction,
            $productSuperAttributeLabelAction
        );
    }
}
