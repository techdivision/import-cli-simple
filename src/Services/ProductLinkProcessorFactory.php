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

use TechDivision\Import\Configuration\ProcessorConfigurationInterface;
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
     * @param \PDO                                                              $connection    The PDO connection to use
     * @param TechDivision\Import\Configuration\ProcessorConfigurationInterface $configuration The subject configuration
     *
     * @return \TechDivision\Import\Product\Link\Services\ProducLinkProcessor The processor instance
     */
    public static function factory(\PDO $connection, ProcessorConfigurationInterface $configuration)
    {

        // load the utility class name
        $utilityClassName = $configuration->getUtilityClassName();

        // initialize the repository that provides product link query functionality
        $productLinkRepository = new ProductLinkRepository($connection, $utilityClassName);

        // initialize the repository that provides product link attribute integer query functionality
        $productLinkAttributeIntRepository = new ProductLinkAttributeIntRepository($connection, $utilityClassName);

        // initialize the action that provides product link CRUD functionality
        $productLinkAction = new ProductLinkAction(
            new ProductLinkCreateProcessor($connection, $utilityClassName),
            new ProductLinkUpdateProcessor($connection, $utilityClassName)
        );

        // initialize the action that provides product link attribute integer CRUD functionality
        $productLinkAttributeIntAction = new ProductLinkAttributeIntAction(
            new ProductLinkAttributeIntCreateProcessor($connection, $utilityClassName),
            new ProductLinkAttributeIntUpdateProcessor($connection, $utilityClassName)
        );

        // initialize and return the product link processor
        $processorType = static::getProcessorType();
        return new $processorType(
            $connection,
            $productLinkRepository,
            $productLinkAttributeIntRepository,
            $productLinkAction,
            $productLinkAttributeIntAction
        );
    }
}
