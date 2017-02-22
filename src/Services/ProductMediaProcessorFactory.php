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
        $productMediaGalleryRepository = new ProductMediaGalleryRepository();
        $productMediaGalleryRepository->setUtilityClassName($utilityClassName);
        $productMediaGalleryRepository->setConnection($connection);
        $productMediaGalleryRepository->init();

        // initialize the repository that provides product media gallery value to entity query functionality
        $productMediaGalleryValueToEntityRepository = new ProductMediaGalleryValueToEntityRepository();
        $productMediaGalleryValueToEntityRepository->setUtilityClassName($utilityClassName);
        $productMediaGalleryValueToEntityRepository->setConnection($connection);
        $productMediaGalleryValueToEntityRepository->init();

        // initialize the repository that provides product media gallery value query functionality
        $productMediaGalleryValueRepository = new ProductMediaGalleryValueRepository();
        $productMediaGalleryValueRepository->setUtilityClassName($utilityClassName);
        $productMediaGalleryValueRepository->setConnection($connection);
        $productMediaGalleryValueRepository->init();

        // initialize the action that provides product media gallery CRUD functionality
        $productMediaGalleryCreateProcessor = new ProductMediaGalleryCreateProcessor();
        $productMediaGalleryCreateProcessor->setUtilityClassName($utilityClassName);
        $productMediaGalleryCreateProcessor->setConnection($connection);
        $productMediaGalleryCreateProcessor->init();
        $productMediaGalleryUpdateProcessor = new ProductMediaGalleryUpdateProcessor();
        $productMediaGalleryUpdateProcessor->setUtilityClassName($utilityClassName);
        $productMediaGalleryUpdateProcessor->setConnection($connection);
        $productMediaGalleryUpdateProcessor->init();
        $productMediaGalleryAction = new ProductMediaGalleryAction();
        $productMediaGalleryAction->setCreateProcessor($productMediaGalleryCreateProcessor);
        $productMediaGalleryAction->setUpdateProcessor($productMediaGalleryUpdateProcessor);

        // initialize the action that provides product media gallery value CRUD functionality
        $productMediaGalleryValueCreateProcessor = new ProductMediaGalleryValueCreateProcessor();
        $productMediaGalleryValueCreateProcessor->setUtilityClassName($utilityClassName);
        $productMediaGalleryValueCreateProcessor->setConnection($connection);
        $productMediaGalleryValueCreateProcessor->init();
        $productMediaGalleryValueUpdateProcessor = new ProductMediaGalleryValueUpdateProcessor();
        $productMediaGalleryValueUpdateProcessor->setUtilityClassName($utilityClassName);
        $productMediaGalleryValueUpdateProcessor->setConnection($connection);
        $productMediaGalleryValueUpdateProcessor->init();
        $productMediaGalleryValueAction = new ProductMediaGalleryValueAction();
        $productMediaGalleryValueAction->setCreateProcessor($productMediaGalleryValueCreateProcessor);
        $productMediaGalleryValueAction->setUpdateProcessor($productMediaGalleryValueUpdateProcessor);

        // initialize the action that provides product media gallery value to entity CRUD functionality
        $productMediaGalleryValueToEntityCreateProcessor = new ProductMediaGalleryValueToEntityCreateProcessor();
        $productMediaGalleryValueToEntityCreateProcessor->setUtilityClassName($utilityClassName);
        $productMediaGalleryValueToEntityCreateProcessor->setConnection($connection);
        $productMediaGalleryValueToEntityCreateProcessor->init();
        $productMediaGalleryValueToEntityAction = new ProductMediaGalleryValueToEntityAction();
        $productMediaGalleryValueToEntityAction->setCreateProcessor($productMediaGalleryValueToEntityCreateProcessor);

        // initialize the action that provides product media gallery value video CRUD functionality
        $productMediaGalleryValueVideoCreateProcessor = new ProductMediaGalleryValueVideoCreateProcessor();
        $productMediaGalleryValueVideoCreateProcessor->setUtilityClassName($utilityClassName);
        $productMediaGalleryValueVideoCreateProcessor->setConnection($connection);
        $productMediaGalleryValueVideoCreateProcessor->init();
        $productMediaGalleryValueVideoAction = new ProductMediaGalleryValueVideoAction();
        $productMediaGalleryValueVideoAction->setCreateProcessor($productMediaGalleryValueVideoCreateProcessor);

        // initialize the product media processor
        $processorType = static::getProcessorType();
        $productMediaProcessor = new $processorType();
        $productMediaProcessor->setConnection($connection);
        $productMediaProcessor->setProductMediaGalleryRepository($productMediaGalleryRepository);
        $productMediaProcessor->setProductMediaGalleryValueRepository($productMediaGalleryValueRepository);
        $productMediaProcessor->setProductMediaGalleryValueToEntityRepository($productMediaGalleryValueToEntityRepository);
        $productMediaProcessor->setProductMediaGalleryRepository($productMediaGalleryRepository);
        $productMediaProcessor->setProductMediaGalleryAction($productMediaGalleryAction);
        $productMediaProcessor->setProductMediaGalleryValueAction($productMediaGalleryValueAction);
        $productMediaProcessor->setProductMediaGalleryValueToEntityAction($productMediaGalleryValueToEntityAction);
        $productMediaProcessor->setProductMediaGalleryValueVideoAction($productMediaGalleryValueVideoAction);

        // return the instance
        return $productMediaProcessor;
    }
}
