<?php

/**
 * TechDivision\Import\Cli\Services\ProductLinkProcessorFactory
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
use TechDivision\Import\Product\Link\Repositories\ProductLinkRepository;
use TechDivision\Import\Product\Link\Repositories\ProductLinkAttributeIntRepository;
use TechDivision\Import\Product\Link\Actions\ProductLinkAction;
use TechDivision\Import\Product\Link\Actions\ProductLinkAttributeIntAction;
use TechDivision\Import\Product\Link\Actions\Processors\ProductLinkCreateProcessor;
use TechDivision\Import\Product\Link\Actions\Processors\ProductLinkUpdateProcessor;
use TechDivision\Import\Product\Link\Actions\Processors\ProductLinkAttributeIntCreateProcessor;
use TechDivision\Import\Product\Link\Actions\Processors\ProductLinkAttributeIntUpdateProcessor;

/**
 * Factory to create a new product link processor.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class ProductLinkProcessorFactory extends AbstractProductProcessorFactory
{

    /**
     * Return's the processor class name.
     *
     * @return string The processor class name
     */
    protected static function getProcessorType()
    {
        return 'TechDivision\Import\Product\Link\Services\ProductLinkProcessor';
    }

    /**
     * Factory method to create a new product link processor instance.
     *
     * @param \PDO                                       $connection    The PDO connection to use
     * @param TechDivision\Import\ConfigurationInterface $configuration The subject configuration
     *
     * @return \TechDivision\Import\Product\Link\Services\ProducLinkProcessor The processor instance
     */
    public static function factory(\PDO $connection, SubjectInterface $configuration)
    {

        // load the utility class name
        $utilityClassName = $configuration->getUtilityClassName();

        // initialize the repository that provides product link query functionality
        $productLinkRepository = new ProductLinkRepository();
        $productLinkRepository->setUtilityClassName($utilityClassName);
        $productLinkRepository->setConnection($connection);
        $productLinkRepository->init();

        // initialize the repository that provides product link attribute integer query functionality
        $productLinkAttributeIntRepository = new ProductLinkAttributeIntRepository();
        $productLinkAttributeIntRepository->setUtilityClassName($utilityClassName);
        $productLinkAttributeIntRepository->setConnection($connection);
        $productLinkAttributeIntRepository->init();

        // initialize the action that provides product link CRUD functionality
        $productLinkCreateProcessor = new ProductLinkCreateProcessor();
        $productLinkCreateProcessor->setUtilityClassName($utilityClassName);
        $productLinkCreateProcessor->setConnection($connection);
        $productLinkCreateProcessor->init();
        $productLinkUpdateProcessor = new ProductLinkUpdateProcessor();
        $productLinkUpdateProcessor->setUtilityClassName($utilityClassName);
        $productLinkUpdateProcessor->setConnection($connection);
        $productLinkUpdateProcessor->init();
        $productLinkAction = new ProductLinkAction();
        $productLinkAction->setCreateProcessor($productLinkCreateProcessor);
        $productLinkAction->setUpdateProcessor($productLinkUpdateProcessor);

        // initialize the action that provides product link attribute integer CRUD functionality
        $productLinkAttributeIntCreateProcessor = new ProductLinkAttributeIntCreateProcessor();
        $productLinkAttributeIntCreateProcessor->setUtilityClassName($utilityClassName);
        $productLinkAttributeIntCreateProcessor->setConnection($connection);
        $productLinkAttributeIntCreateProcessor->init();
        $productLinkAttributeIntUpdateProcessor = new ProductLinkAttributeIntUpdateProcessor();
        $productLinkAttributeIntUpdateProcessor->setUtilityClassName($utilityClassName);
        $productLinkAttributeIntUpdateProcessor->setConnection($connection);
        $productLinkAttributeIntUpdateProcessor->init();
        $productLinkAttributeIntAction = new ProductLinkAttributeIntAction();
        $productLinkAttributeIntAction->setCreateProcessor($productLinkAttributeIntCreateProcessor);
        $productLinkAttributeIntAction->setUpdateProcessor($productLinkAttributeIntUpdateProcessor);

        // initialize the product link processor
        $processorType = static::getProcessorType();
        $productLinkProcessor = new $processorType();
        $productLinkProcessor->setConnection($connection);
        $productLinkProcessor->setProductLinkRepository($productLinkRepository);
        $productLinkProcessor->setProductLinkAttributeIntRepository($productLinkAttributeIntRepository);
        $productLinkProcessor->setProductLinkAction($productLinkAction);
        $productLinkProcessor->setProductLinkAttributeIntAction($productLinkAttributeIntAction);

        // return the instance
        return $productLinkProcessor;
    }
}
