<?php

/**
 * TechDivision\Import\Cli\Services\EeCategoryBunchProcessorFactory
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
use TechDivision\Import\Category\Ee\Repositories\CategoryRepository;
use TechDivision\Import\Category\Ee\Repositories\CategoryDatetimeRepository;
use TechDivision\Import\Category\Ee\Repositories\CategoryDecimalRepository;
use TechDivision\Import\Category\Ee\Repositories\CategoryIntRepository;
use TechDivision\Import\Category\Ee\Repositories\CategoryTextRepository;
use TechDivision\Import\Category\Ee\Repositories\CategoryVarcharRepository;
use TechDivision\Import\Category\Ee\Actions\SequenceCategoryAction;
use TechDivision\Import\Category\Ee\Actions\Processors\CategoryUpdateProcessor;
use TechDivision\Import\Category\Ee\Actions\Processors\SequenceCategoryCreateProcessor;

/**
 * Factory to create a new category bunch processor.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class EeCategoryBunchProcessorFactory extends CategoryBunchProcessorFactory
{

    /**
     * Return's the processor class name.
     *
     * @return string The processor class name
     */
    protected static function getProcessorType()
    {
        return 'TechDivision\Import\Category\Ee\Services\EeCategoryBunchProcessor';
    }

    /**
     * Factory method to create a new category processor instance.
     *
     * @param \PDO                                                               $connection    The PDO connection to use
     * @param \TechDivision\Import\Configuration\ProcessorConfigurationInterface $configuration The subject configuration
     *
     * @return \TechDivision\Import\Category\Services\CategoryProcessorInterface The processor instance
     */
    public static function factory(\PDO $connection, ProcessorConfigurationInterface $configuration)
    {

        // initialize the category processor
        $categoryBunchProcessor = parent::factory($connection, $configuration);

        // load the utility class name
        $utilityClassName = $configuration->getUtilityClassName();

        // initialize the action that provides category CRUD functionality
        $categoryUpdateProcessor = new CategoryUpdateProcessor();
        $categoryUpdateProcessor->setUtilityClassName($utilityClassName);
        $categoryUpdateProcessor->setConnection($connection);
        $categoryUpdateProcessor->init();

        // override the category update processor to support Magento 2 EE scheduled updates functionality
        $categoryBunchProcessor->getCategoryAction()->setUpdateProcessor($categoryUpdateProcessor);

        // initialize the action that provides sequence category CRUD functionality
        $sequenceCategoryCreateProcessor = new SequenceCategoryCreateProcessor();
        $sequenceCategoryCreateProcessor->setUtilityClassName($utilityClassName);
        $sequenceCategoryCreateProcessor->setConnection($connection);
        $sequenceCategoryCreateProcessor->init();
        $sequenceCategoryAction = new SequenceCategoryAction();
        $sequenceCategoryAction->setCreateProcessor($sequenceCategoryCreateProcessor);

        // initialize the repository that provides category attribute query functionality
        $categoryRepository = new CategoryRepository();
        $categoryRepository->setUtilityClassName($utilityClassName);
        $categoryRepository->setConnection($connection);
        $categoryRepository->init();

        // initialize the repository that provides category datetime attribute query functionality
        $categoryDatetimeRepository = new CategoryDatetimeRepository();
        $categoryDatetimeRepository->setUtilityClassName($utilityClassName);
        $categoryDatetimeRepository->setConnection($connection);
        $categoryDatetimeRepository->init();

        // initialize the repository that provides category decimal attribute query functionality
        $categoryDecimalRepository = new CategoryDecimalRepository();
        $categoryDecimalRepository->setUtilityClassName($utilityClassName);
        $categoryDecimalRepository->setConnection($connection);
        $categoryDecimalRepository->init();

        // initialize the repository that provides category integer attribute query functionality
        $categoryIntRepository = new CategoryIntRepository();
        $categoryIntRepository->setUtilityClassName($utilityClassName);
        $categoryIntRepository->setConnection($connection);
        $categoryIntRepository->init();

        // initialize the repository that provides category text attribute query functionality
        $categoryTextRepository = new CategoryTextRepository();
        $categoryTextRepository->setUtilityClassName($utilityClassName);
        $categoryTextRepository->setConnection($connection);
        $categoryTextRepository->init();

        // initialize the repository that provides category varchar attribute query functionality
        $categoryVarcharRepository = new CategoryVarcharRepository();
        $categoryVarcharRepository->setUtilityClassName($utilityClassName);
        $categoryVarcharRepository->setConnection($connection);
        $categoryVarcharRepository->init();

        // initialize the category bunch processor
        $categoryBunchProcessor->setCategoryRepository($categoryRepository);
        $categoryBunchProcessor->setCategoryDatetimeRepository($categoryDatetimeRepository);
        $categoryBunchProcessor->setCategoryDecimalRepository($categoryDecimalRepository);
        $categoryBunchProcessor->setCategoryIntRepository($categoryIntRepository);
        $categoryBunchProcessor->setCategoryTextRepository($categoryTextRepository);
        $categoryBunchProcessor->setCategoryVarcharRepository($categoryVarcharRepository);
        $categoryBunchProcessor->setSequenceCategoryAction($sequenceCategoryAction);

        // return the instance
        return $categoryBunchProcessor;
    }
}
