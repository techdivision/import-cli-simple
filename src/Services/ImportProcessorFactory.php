<?php

/**
 * TechDivision\Import\Cli\Services\ImportProcessorFactory
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

use TechDivision\Import\ConfigurationInterface;
use TechDivision\Import\Services\ImportProcessor;
use TechDivision\Import\Assembler\CategoryAssembler;
use TechDivision\Import\Repositories\StoreRepository;
use TechDivision\Import\Repositories\TaxClassRepository;
use TechDivision\Import\Repositories\LinkTypeRepository;
use TechDivision\Import\Repositories\CategoryRepository;
use TechDivision\Import\Repositories\StoreWebsiteRepository;
use TechDivision\Import\Repositories\EavAttributeRepository;
use TechDivision\Import\Repositories\EavEntityTypeRepository;
use TechDivision\Import\Repositories\LinkAttributeRepository;
use TechDivision\Import\Repositories\CoreConfigDataRepository;
use TechDivision\Import\Repositories\CategoryVarcharRepository;
use TechDivision\Import\Repositories\EavAttributeSetRepository;
use TechDivision\Import\Utils\Generators\CoreConfigDataUidGenerator;

/**
 * Factory to create a new import processor.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class ImportProcessorFactory
{

    /**
     * Factory method to create a new import processor instance.
     *
     * @param \PDO                                       $connection    The PDO connection to use
     * @param TechDivision\Import\ConfigurationInterface $configuration The subject configuration
     *
     * @return object The processor instance
     */
    public static function factory(\PDO $connection, ConfigurationInterface $configuration)
    {

        // extract Magento edition/version
        $utilityClassName = $configuration->getUtilityClassName();

        // initialize the repository that provides category query functionality
        $categoryRepository = new CategoryRepository($connection, $utilityClassName);

        // initialize the repository that provides category varchar value query functionality
        $categoryVarcharRepository = new CategoryVarcharRepository($connection, $utilityClassName);

        // initialize the repository that provides EAV attribute query functionality
        $eavAttributeRepository = new EavAttributeRepository($connection, $utilityClassName);

        // initialize the repository that provides EAV attribute set query functionality
        $eavAttributeSetRepository = new EavAttributeSetRepository($connection, $utilityClassName);

        // initialize the repository that provides EAV entity type query functionality
        $eavEntityTypeRepository = new EavEntityTypeRepository($connection, $utilityClassName);

        // initialize the repository that provides store query functionality
        $storeRepository = new StoreRepository($connection, $utilityClassName);

        // initialize the repository that provides store website query functionality
        $storeWebsiteRepository = new StoreWebsiteRepository($connection, $utilityClassName);

        // initialize the repository that provides tax class query functionality
        $taxClassRepository = new TaxClassRepository($connection, $utilityClassName);

        // initialize the repository that provides link type query functionality
        $linkTypeRepository = new LinkTypeRepository($connection, $utilityClassName);

        // initialize the repository that provides link attribute query functionality
        $linkAttributeRepository = new LinkAttributeRepository($connection, $utilityClassName);

        // initialize the repository that provides core config data functionality
        $coreConfigDataRepository = new CoreConfigDataRepository(new CoreConfigDataUidGenerator(), $connection, $utilityClassName);

        // initialize the category assembler
        $categoryAssembler = new CategoryAssembler($categoryRepository, $categoryVarcharRepository);

        // initialize the import processor
        $importProcessor = new ImportProcessor(
            $connection,
            $categoryAssembler,
            $categoryRepository,
            $categoryVarcharRepository,
            $eavAttributeRepository,
            $eavAttributeSetRepository,
            $eavEntityTypeRepository,
            $storeRepository,
            $storeWebsiteRepository,
            $taxClassRepository,
            $linkTypeRepository,
            $linkAttributeRepository,
            $coreConfigDataRepository
        );

        // return the initialize import processor instance
        return $importProcessor;
    }
}
