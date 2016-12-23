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
use TechDivision\Import\Product\Link\Actions\ProductLinkAction;
use TechDivision\Import\Product\Link\Actions\ProductLinkAttributeAction;
use TechDivision\Import\Product\Link\Actions\ProductLinkAttributeIntAction;
use TechDivision\Import\Product\Link\Actions\ProductLinkAttributeDecimalAction;
use TechDivision\Import\Product\Link\Actions\ProductLinkAttributeVarcharAction;
use TechDivision\Import\Product\Link\Actions\Processors\ProductLinkCreateProcessor;
use TechDivision\Import\Product\Link\Actions\Processors\ProductLinkAttributeCreateProcessor;
use TechDivision\Import\Product\Link\Actions\Processors\ProductLinkAttributeIntCreateProcessor;
use TechDivision\Import\Product\Link\Actions\Processors\ProductLinkAttributeDecimalCreateProcessor;
use TechDivision\Import\Product\Link\Actions\Processors\ProductLinkAttributeVarcharCreateProcessor;

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

        // initialize the action that provides product link CRUD functionality
        $productLinkCreateProcessor = new ProductLinkCreateProcessor();
        $productLinkCreateProcessor->setUtilityClassName($utilityClassName);
        $productLinkCreateProcessor->setConnection($connection);
        $productLinkCreateProcessor->init();
        $productLinkAction = new ProductLinkAction();
        $productLinkAction->setCreateProcessor($productLinkCreateProcessor);

        // initialize the action that provides product link attribute CRUD functionality
        $productLinkAttributeCreateProcessor = new ProductLinkAttributeCreateProcessor();
        $productLinkAttributeCreateProcessor->setUtilityClassName($utilityClassName);
        $productLinkAttributeCreateProcessor->setConnection($connection);
        $productLinkAttributeCreateProcessor->init();
        $productLinkAttributeAction = new ProductLinkAttributeAction();
        $productLinkAttributeAction->setCreateProcessor($productLinkAttributeCreateProcessor);

        // initialize the action that provides product link attribute decimal CRUD functionality
        $productLinkAttributeDecimalCreateProcessor = new ProductLinkAttributeDecimalCreateProcessor();
        $productLinkAttributeDecimalCreateProcessor->setUtilityClassName($utilityClassName);
        $productLinkAttributeDecimalCreateProcessor->setConnection($connection);
        $productLinkAttributeDecimalCreateProcessor->init();
        $productLinkAttributeDecimalAction = new ProductLinkAttributeDecimalAction();
        $productLinkAttributeDecimalAction->setCreateProcessor($productLinkAttributeDecimalCreateProcessor);

        // initialize the action that provides product link attribute integer CRUD functionality
        $productLinkAttributeIntCreateProcessor = new ProductLinkAttributeIntCreateProcessor();
        $productLinkAttributeIntCreateProcessor->setUtilityClassName($utilityClassName);
        $productLinkAttributeIntCreateProcessor->setConnection($connection);
        $productLinkAttributeIntCreateProcessor->init();
        $productLinkAttributeIntAction = new ProductLinkAttributeIntAction();
        $productLinkAttributeIntAction->setCreateProcessor($productLinkAttributeIntCreateProcessor);

        // initialize the action that provides product link attribute varchar CRUD functionality
        $productLinkAttributeVarcharCreateProcessor = new ProductLinkAttributeVarcharCreateProcessor();
        $productLinkAttributeVarcharCreateProcessor->setUtilityClassName($utilityClassName);
        $productLinkAttributeVarcharCreateProcessor->setConnection($connection);
        $productLinkAttributeVarcharCreateProcessor->init();
        $productLinkAttributeVarcharAction = new ProductLinkAttributeVarcharAction();
        $productLinkAttributeVarcharAction->setCreateProcessor($productLinkAttributeVarcharCreateProcessor);

        // initialize the product link processor
        $processorType = ProductLinkProcessorFactory::getProcessorType();
        $productLinkProcessor = new $processorType();
        $productLinkProcessor->setConnection($connection);
        $productLinkProcessor->setProductLinkAction($productLinkAction);
        $productLinkProcessor->setProductLinkAttributeAction($productLinkAttributeAction);
        $productLinkProcessor->setProductLinkAttributeIntAction($productLinkAttributeIntAction);
        $productLinkProcessor->setProductLinkAttributeDecimalAction($productLinkAttributeDecimalAction);
        $productLinkProcessor->setProductLinkAttributeVarcharAction($productLinkAttributeVarcharAction);

        // return the instance
        return $productLinkProcessor;
    }
}
