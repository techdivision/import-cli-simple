<?php

/**
 * TechDivision\Import\Cli\Services\ProductMediaProcessorFactory
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
use TechDivision\Import\Product\Media\Repositories\ProductMediaGalleryRepository;
use TechDivision\Import\Product\Media\Repositories\ProductMediaGalleryValueRepository;
use TechDivision\Import\Product\Media\Repositories\ProductMediaGalleryValueToEntityRepository;
use TechDivision\Import\Product\Media\Actions\Processors\ProductMediaGalleryCreateProcessor;
use TechDivision\Import\Product\Media\Actions\Processors\ProductMediaGalleryUpdateProcessor;
use TechDivision\Import\Product\Media\Actions\Processors\ProductMediaGalleryValueCreateProcessor;
use TechDivision\Import\Product\Media\Actions\Processors\ProductMediaGalleryValueUpdateProcessor;
use TechDivision\Import\Product\Media\Actions\Processors\ProductMediaGalleryValueVideoCreateProcessor;
use TechDivision\Import\Product\Media\Actions\Processors\ProductMediaGalleryValueToEntityCreateProcessor;
use TechDivision\Import\Product\Media\Actions\ProductMediaGalleryAction;
use TechDivision\Import\Product\Media\Actions\ProductMediaGalleryValueAction;
use TechDivision\Import\Product\Media\Actions\ProductMediaGalleryValueVideoAction;
use TechDivision\Import\Product\Media\Actions\ProductMediaGalleryValueToEntityAction;

/**
 * Factory to create a new product media processor.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class ProductMediaProcessorFactory extends AbstractProductProcessorFactory
{

    /**
     * Return's the processor class name.
     *
     * @return string The processor class name
     */
    protected static function getProcessorType()
    {
        return 'TechDivision\Import\Product\Media\Services\ProductMediaProcessor';
    }

    /**
     * Factory method to create a new product media processor instance.
     *
     * @param \PDO                                                              $connection    The PDO connection to use
     * @param TechDivision\Import\Configuration\ProcessorConfigurationInterface $configuration The subject configuration
     *
     * @return \TechDivision\Import\Product\Media\Services\ProductMediaProcessor The processor instance
     */
    public static function factory(\PDO $connection, ProcessorConfigurationInterface $configuration)
    {

        // load the utility class name
        $utilityClassName = $configuration->getUtilityClassName();

        // initialize the repository that provides product media gallery query functionality
        $productMediaGalleryRepository = new ProductMediaGalleryRepository($connection, $utilityClassName);

        // initialize the repository that provides product media gallery value to entity query functionality
        $productMediaGalleryValueToEntityRepository = new ProductMediaGalleryValueToEntityRepository($connection, $utilityClassName);

        // initialize the repository that provides product media gallery value query functionality
        $productMediaGalleryValueRepository = new ProductMediaGalleryValueRepository($connection, $utilityClassName);

        // initialize the action that provides product media gallery CRUD functionality
        $productMediaGalleryAction = new ProductMediaGalleryAction(
            new ProductMediaGalleryCreateProcessor($connection, $utilityClassName),
            new ProductMediaGalleryUpdateProcessor($connection, $utilityClassName)
        );

        // initialize the action that provides product media gallery value CRUD functionality
        $productMediaGalleryValueAction = new ProductMediaGalleryValueAction(
            new ProductMediaGalleryValueCreateProcessor($connection, $utilityClassName),
            new ProductMediaGalleryValueUpdateProcessor($connection, $utilityClassName)
        );

        // initialize the action that provides product media gallery value to entity CRUD functionality
        $productMediaGalleryValueToEntityAction = new ProductMediaGalleryValueToEntityAction(
            new ProductMediaGalleryValueToEntityCreateProcessor($connection, $utilityClassName)
        );

        // initialize the action that provides product media gallery value video CRUD functionality
        $productMediaGalleryValueVideoAction = new ProductMediaGalleryValueVideoAction(
            new ProductMediaGalleryValueVideoCreateProcessor($connection, $utilityClassName)
        );

        // initialize and return the product media processor
        $processorType = static::getProcessorType();
        return new $processorType(
            $connection,
            $productMediaGalleryRepository,
            $productMediaGalleryValueRepository,
            $productMediaGalleryValueToEntityRepository,
            $productMediaGalleryAction,
            $productMediaGalleryValueAction,
            $productMediaGalleryValueToEntityAction,
            $productMediaGalleryValueVideoAction
        );
    }
}
