<?php

/**
 * TechDivision\Import\Cli\Services\EeProductMediaProcessorFactory
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
use TechDivision\Import\Product\Media\Ee\Repositories\ProductMediaGalleryValueRepository;
use TechDivision\Import\Product\Media\Ee\Repositories\ProductMediaGalleryValueToEntityRepository;

/**
 * A SLSB providing methods to load product data using a PDO connection.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class EeProductMediaProcessorFactory extends ProductMediaProcessorFactory
{

    /**
     * Return's the processor class name.
     *
     * @return string The processor class name
     */
    protected static function getProcessorType()
    {
        return 'TechDivision\Import\Product\Media\Ee\Services\EeProductMediaProcessor';
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
        $productMediaProcessor = parent::factory($connection, $configuration);

        // load the utility class name
        $utilityClassName = $configuration->getUtilityClassName();

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

        // initialize the product media processor
        $productMediaProcessor->setProductMediaGalleryValueRepository($productMediaGalleryValueRepository);
        $productMediaProcessor->setProductMediaGalleryValueToEntityRepository($productMediaGalleryValueToEntityRepository);

        // return the instance
        return $productMediaProcessor;
    }
}
