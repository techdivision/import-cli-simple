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
use TechDivision\Import\Product\Media\Actions\Processors\ProductMediaGalleryCreateProcessor;
use TechDivision\Import\Product\Media\Actions\Processors\ProductMediaGalleryValueCreateProcessor;
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
        $productMediaGalleryCreateProcessor = new ProductMediaGalleryCreateProcessor();
        $productMediaGalleryCreateProcessor->setUtilityClassName($utilityClassName);
        $productMediaGalleryCreateProcessor->setConnection($connection);
        $productMediaGalleryCreateProcessor->init();
        $productMediaGalleryAction = new ProductMediaGalleryAction();
        $productMediaGalleryAction->setCreateProcessor($productMediaGalleryCreateProcessor);

        // initialize the action that provides product media gallery value CRUD functionality
        $productMediaGalleryValueCreateProcessor = new ProductMediaGalleryValueCreateProcessor();
        $productMediaGalleryValueCreateProcessor->setUtilityClassName($utilityClassName);
        $productMediaGalleryValueCreateProcessor->setConnection($connection);
        $productMediaGalleryValueCreateProcessor->init();
        $productMediaGalleryValueAction = new ProductMediaGalleryValueAction();
        $productMediaGalleryValueAction->setCreateProcessor($productMediaGalleryValueCreateProcessor);

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
