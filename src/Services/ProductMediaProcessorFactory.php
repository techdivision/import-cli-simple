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

use TechDivision\Import\Configuration\SubjectInterface;
use TechDivision\Import\Product\Media\Actions\Processors\ProductMediaGalleryPersistProcessor;
use TechDivision\Import\Product\Media\Actions\Processors\ProductMediaGalleryValuePersistProcessor;
use TechDivision\Import\Product\Media\Actions\Processors\ProductMediaGalleryValueVideoPersistProcessor;
use TechDivision\Import\Product\Media\Actions\Processors\ProductMediaGalleryValueToEntityPersistProcessor;
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
     * @param \PDO                                               $connection    The PDO connection to use
     * @param TechDivision\Import\Configuration\SubjectInterface $configuration The subject configuration
     *
     * @return \TechDivision\Import\Product\Media\Services\ProductMediaProcessor The processor instance
     */
    public static function factory(\PDO $connection, SubjectInterface $configuration)
    {

        // load the utility class name
        $utilityClassName = $configuration->getUtilityClassName();

        // initialize the action that provides product media gallery CRUD functionality
        $productMediaGalleryPersistProcessor = new ProductMediaGalleryPersistProcessor();
        $productMediaGalleryPersistProcessor->setUtilityClassName($utilityClassName);
        $productMediaGalleryPersistProcessor->setConnection($connection);
        $productMediaGalleryPersistProcessor->init();
        $productMediaGalleryAction = new ProductMediaGalleryAction();
        $productMediaGalleryAction->setPersistProcessor($productMediaGalleryPersistProcessor);

        // initialize the action that provides product media gallery value CRUD functionality
        $productMediaGalleryValuePersistProcessor = new ProductMediaGalleryValuePersistProcessor();
        $productMediaGalleryValuePersistProcessor->setUtilityClassName($utilityClassName);
        $productMediaGalleryValuePersistProcessor->setConnection($connection);
        $productMediaGalleryValuePersistProcessor->init();
        $productMediaGalleryValueAction = new ProductMediaGalleryValueAction();
        $productMediaGalleryValueAction->setPersistProcessor($productMediaGalleryValuePersistProcessor);

        // initialize the action that provides product media gallery value to entity CRUD functionality
        $productMediaGalleryValueToEntityPersistProcessor = new ProductMediaGalleryValueToEntityPersistProcessor();
        $productMediaGalleryValueToEntityPersistProcessor->setUtilityClassName($utilityClassName);
        $productMediaGalleryValueToEntityPersistProcessor->setConnection($connection);
        $productMediaGalleryValueToEntityPersistProcessor->init();
        $productMediaGalleryValueToEntityAction = new ProductMediaGalleryValueToEntityAction();
        $productMediaGalleryValueToEntityAction->setPersistProcessor($productMediaGalleryValueToEntityPersistProcessor);

        // initialize the action that provides product media gallery value video CRUD functionality
        $productMediaGalleryValueVideoPersistProcessor = new ProductMediaGalleryValueVideoPersistProcessor();
        $productMediaGalleryValueVideoPersistProcessor->setUtilityClassName($utilityClassName);
        $productMediaGalleryValueVideoPersistProcessor->setConnection($connection);
        $productMediaGalleryValueVideoPersistProcessor->init();
        $productMediaGalleryValueVideoAction = new ProductMediaGalleryValueVideoAction();
        $productMediaGalleryValueVideoAction->setPersistProcessor($productMediaGalleryValueVideoPersistProcessor);

        // initialize the product media processor
        $processorType = ProductMediaProcessorFactory::getProcessorType();
        $productMediaProcessor = new $processorType();
        $productMediaProcessor->setConnection($connection);
        $productMediaProcessor->setProductMediaGalleryAction($productMediaGalleryAction);
        $productMediaProcessor->setProductMediaGalleryValueAction($productMediaGalleryValueAction);
        $productMediaProcessor->setProductMediaGalleryValueToEntityAction($productMediaGalleryValueToEntityAction);
        $productMediaProcessor->setProductMediaGalleryValueVideoAction($productMediaGalleryValueVideoAction);

        // return the instance
        return $productMediaProcessor;
    }
}
