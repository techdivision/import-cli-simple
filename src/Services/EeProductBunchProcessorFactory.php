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

use TechDivision\Import\Configuration\SubjectConfigurationInterface;
use TechDivision\Import\Product\Ee\Repositories\ProductDatetimeRepository;
use TechDivision\Import\Product\Ee\Repositories\ProductDecimalRepository;
use TechDivision\Import\Product\Ee\Repositories\ProductIntRepository;
use TechDivision\Import\Product\Ee\Repositories\ProductTextRepository;
use TechDivision\Import\Product\Ee\Repositories\ProductVarcharRepository;
use TechDivision\Import\Product\Ee\Actions\SequenceProductAction;
use TechDivision\Import\Product\Ee\Actions\Processors\ProductUpdateProcessor;
use TechDivision\Import\Product\Ee\Actions\Processors\SequenceProductCreateProcessor;

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
     * @param \PDO                                                            $connection    The PDO connection to use
     * @param TechDivision\Import\Configuration\SubjectConfigurationInterface $configuration The subject configuration
     *
     * @return \TechDivision\Import\Product\Services\ProductProcessor The processor instance
     */
    public static function factory(\PDO $connection, SubjectConfigurationInterface $configuration)
    {

        // initialize the product processor
        $productBunchProcessor = parent::factory($connection, $configuration);

        // load the utility class name
        $utilityClassName = $configuration->getUtilityClassName();

        // initialize the action that provides product CRUD functionality
        $productUpdateProcessor = new ProductUpdateProcessor();
        $productUpdateProcessor->setUtilityClassName($utilityClassName);
        $productUpdateProcessor->setConnection($connection);
        $productUpdateProcessor->init();

        // override the product update processor to support Magento 2 EE scheduled updates functionality
        $productBunchProcessor->getProductAction()->setUpdateProcessor($productUpdateProcessor);

        // initialize the action that provides sequence product CRUD functionality
        $sequenceProductCreateProcessor = new SequenceProductCreateProcessor();
        $sequenceProductCreateProcessor->setUtilityClassName($utilityClassName);
        $sequenceProductCreateProcessor->setConnection($connection);
        $sequenceProductCreateProcessor->init();
        $sequenceProductAction = new SequenceProductAction();
        $sequenceProductAction->setCreateProcessor($sequenceProductCreateProcessor);

        // initialize the repository that provides product datetime attribute query functionality
        $productDatetimeRepository = new ProductDatetimeRepository();
        $productDatetimeRepository->setUtilityClassName($utilityClassName);
        $productDatetimeRepository->setConnection($connection);
        $productDatetimeRepository->init();

        // initialize the repository that provides product decimal attribute query functionality
        $productDecimalRepository = new ProductDecimalRepository();
        $productDecimalRepository->setUtilityClassName($utilityClassName);
        $productDecimalRepository->setConnection($connection);
        $productDecimalRepository->init();

        // initialize the repository that provides product integer attribute query functionality
        $productIntRepository = new ProductIntRepository();
        $productIntRepository->setUtilityClassName($utilityClassName);
        $productIntRepository->setConnection($connection);
        $productIntRepository->init();

        // initialize the repository that provides product text attribute query functionality
        $productTextRepository = new ProductTextRepository();
        $productTextRepository->setUtilityClassName($utilityClassName);
        $productTextRepository->setConnection($connection);
        $productTextRepository->init();

        // initialize the repository that provides product varchar attribute query functionality
        $productVarcharRepository = new ProductVarcharRepository();
        $productVarcharRepository->setUtilityClassName($utilityClassName);
        $productVarcharRepository->setConnection($connection);
        $productVarcharRepository->init();

        // initialize the product bunch processor
        $productBunchProcessor->setProductDatetimeRepository($productDatetimeRepository);
        $productBunchProcessor->setProductDecimalRepository($productDecimalRepository);
        $productBunchProcessor->setProductIntRepository($productIntRepository);
        $productBunchProcessor->setProductTextRepository($productTextRepository);
        $productBunchProcessor->setProductVarcharRepository($productVarcharRepository);
        $productBunchProcessor->setSequenceProductAction($sequenceProductAction);

        // return the instance
        return $productBunchProcessor;
    }
}
