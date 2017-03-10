<?php

/**
 * TechDivision\Import\Cli\Services\CategoryBunchProcessorFactory
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
use TechDivision\Import\Assembler\CategoryAssembler;
use TechDivision\Import\Actions\UrlRewriteAction;
use TechDivision\Import\Actions\Processors\UrlRewriteCreateProcessor;
use TechDivision\Import\Actions\Processors\UrlRewriteDeleteProcessor;
use TechDivision\Import\Actions\Processors\UrlRewriteUpdateProcessor;
use TechDivision\Import\Repositories\UrlRewriteRepository;
use TechDivision\Import\Repositories\EavAttributeRepository;
use TechDivision\Import\Category\Repositories\CategoryRepository;
use TechDivision\Import\Category\Repositories\CategoryDatetimeRepository;
use TechDivision\Import\Category\Repositories\CategoryDecimalRepository;
use TechDivision\Import\Category\Repositories\CategoryIntRepository;
use TechDivision\Import\Category\Repositories\CategoryTextRepository;
use TechDivision\Import\Category\Repositories\CategoryVarcharRepository;
use TechDivision\Import\Category\Actions\CategoryAction;
use TechDivision\Import\Category\Actions\CategoryVarcharAction;
use TechDivision\Import\Category\Actions\CategoryTextAction;
use TechDivision\Import\Category\Actions\CategoryIntAction;
use TechDivision\Import\Category\Actions\CategoryDecimalAction;
use TechDivision\Import\Category\Actions\CategoryDatetimeAction;
use TechDivision\Import\Category\Actions\Processors\CategoryDeleteProcessor;
use TechDivision\Import\Category\Actions\Processors\CategoryUpdateProcessor;
use TechDivision\Import\Category\Actions\Processors\CategoryCreateProcessor;
use TechDivision\Import\Category\Actions\Processors\CategoryDatetimeCreateProcessor;
use TechDivision\Import\Category\Actions\Processors\CategoryDatetimeUpdateProcessor;
use TechDivision\Import\Category\Actions\Processors\CategoryDecimalCreateProcessor;
use TechDivision\Import\Category\Actions\Processors\CategoryDecimalUpdateProcessor;
use TechDivision\Import\Category\Actions\Processors\CategoryIntCreateProcessor;
use TechDivision\Import\Category\Actions\Processors\CategoryIntUpdateProcessor;
use TechDivision\Import\Category\Actions\Processors\CategoryTextCreateProcessor;
use TechDivision\Import\Category\Actions\Processors\CategoryTextUpdateProcessor;
use TechDivision\Import\Category\Actions\Processors\CategoryVarcharCreateProcessor;
use TechDivision\Import\Category\Actions\Processors\CategoryVarcharUpdateProcessor;

/**
 * Factory to create a new category bunch processor.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class CategoryBunchProcessorFactory extends AbstractCategoryProcessorFactory
{

    /**
     * Return's the processor class name.
     *
     * @return string The processor class name
     */
    protected static function getProcessorType()
    {
        return 'TechDivision\Import\Category\Services\CategoryBunchProcessor';
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

        // load the utility class name
        $utilityClassName = $configuration->getUtilityClassName();

        // initialize the repository that provides EAV attribute query functionality
        $eavAttributeRepository = new EavAttributeRepository();
        $eavAttributeRepository->setUtilityClassName($utilityClassName);
        $eavAttributeRepository->setConnection($connection);
        $eavAttributeRepository->init();

        // initialize the repository that provides category query functionality
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

        // initialize the repository that provides URL rewrite query functionality
        $urlRewriteRepository = new UrlRewriteRepository();
        $urlRewriteRepository->setUtilityClassName($utilityClassName);
        $urlRewriteRepository->setConnection($connection);
        $urlRewriteRepository->init();

        // initialize the action that provides category datetime attribute CRUD functionality
        $categoryDatetimeCreateProcessor = new CategoryDatetimeCreateProcessor();
        $categoryDatetimeCreateProcessor->setUtilityClassName($utilityClassName);
        $categoryDatetimeCreateProcessor->setConnection($connection);
        $categoryDatetimeCreateProcessor->init();
        $categoryDatetimeUpdateProcessor = new CategoryDatetimeUpdateProcessor();
        $categoryDatetimeUpdateProcessor->setUtilityClassName($utilityClassName);
        $categoryDatetimeUpdateProcessor->setConnection($connection);
        $categoryDatetimeUpdateProcessor->init();
        $categoryDatetimeAction = new CategoryDatetimeAction();
        $categoryDatetimeAction->setCreateProcessor($categoryDatetimeCreateProcessor);
        $categoryDatetimeAction->setUpdateProcessor($categoryDatetimeUpdateProcessor);

        // initialize the action that provides category decimal attribute CRUD functionality
        $categoryDecimalCreateProcessor = new CategoryDecimalCreateProcessor();
        $categoryDecimalCreateProcessor->setUtilityClassName($utilityClassName);
        $categoryDecimalCreateProcessor->setConnection($connection);
        $categoryDecimalCreateProcessor->init();
        $categoryDecimalUpdateProcessor = new CategoryDecimalUpdateProcessor();
        $categoryDecimalUpdateProcessor->setUtilityClassName($utilityClassName);
        $categoryDecimalUpdateProcessor->setConnection($connection);
        $categoryDecimalUpdateProcessor->init();
        $categoryDecimalAction = new CategoryDecimalAction();
        $categoryDecimalAction->setCreateProcessor($categoryDecimalCreateProcessor);
        $categoryDecimalAction->setUpdateProcessor($categoryDecimalUpdateProcessor);

        // initialize the action that provides category integer attribute CRUD functionality
        $categoryIntCreateProcessor = new CategoryIntCreateProcessor();
        $categoryIntCreateProcessor->setUtilityClassName($utilityClassName);
        $categoryIntCreateProcessor->setConnection($connection);
        $categoryIntCreateProcessor->init();
        $categoryIntUpdateProcessor = new CategoryIntUpdateProcessor();
        $categoryIntUpdateProcessor->setUtilityClassName($utilityClassName);
        $categoryIntUpdateProcessor->setConnection($connection);
        $categoryIntUpdateProcessor->init();
        $categoryIntAction = new CategoryIntAction();
        $categoryIntAction->setCreateProcessor($categoryIntCreateProcessor);
        $categoryIntAction->setUpdateProcessor($categoryIntUpdateProcessor);

        // initialize the action that provides category text attribute CRUD functionality
        $categoryTextCreateProcessor = new CategoryTextCreateProcessor();
        $categoryTextCreateProcessor->setUtilityClassName($utilityClassName);
        $categoryTextCreateProcessor->setConnection($connection);
        $categoryTextCreateProcessor->init();
        $categoryTextUpdateProcessor = new CategoryTextUpdateProcessor();
        $categoryTextUpdateProcessor->setUtilityClassName($utilityClassName);
        $categoryTextUpdateProcessor->setConnection($connection);
        $categoryTextUpdateProcessor->init();
        $categoryTextAction = new CategoryTextAction();
        $categoryTextAction->setCreateProcessor($categoryTextCreateProcessor);
        $categoryTextAction->setUpdateProcessor($categoryTextUpdateProcessor);

        // initialize the action that provides category varchar attribute CRUD functionality
        $categoryVarcharCreateProcessor = new CategoryVarcharCreateProcessor();
        $categoryVarcharCreateProcessor->setUtilityClassName($utilityClassName);
        $categoryVarcharCreateProcessor->setConnection($connection);
        $categoryVarcharCreateProcessor->init();
        $categoryVarcharUpdateProcessor = new CategoryVarcharUpdateProcessor();
        $categoryVarcharUpdateProcessor->setUtilityClassName($utilityClassName);
        $categoryVarcharUpdateProcessor->setConnection($connection);
        $categoryVarcharUpdateProcessor->init();
        $categoryVarcharAction = new CategoryVarcharAction();
        $categoryVarcharAction->setCreateProcessor($categoryVarcharCreateProcessor);
        $categoryVarcharAction->setUpdateProcessor($categoryVarcharUpdateProcessor);

        // initialize the action that provides category CRUD functionality
        $categoryCreateProcessor = new CategoryCreateProcessor();
        $categoryCreateProcessor->setUtilityClassName($utilityClassName);
        $categoryCreateProcessor->setConnection($connection);
        $categoryCreateProcessor->init();
        $categoryDeleteProcessor = new CategoryDeleteProcessor();
        $categoryDeleteProcessor->setUtilityClassName($utilityClassName);
        $categoryDeleteProcessor->setConnection($connection);
        $categoryDeleteProcessor->init();
        $categoryUpdateProcessor = new CategoryUpdateProcessor();
        $categoryUpdateProcessor->setUtilityClassName($utilityClassName);
        $categoryUpdateProcessor->setConnection($connection);
        $categoryUpdateProcessor->init();
        $categoryAction = new CategoryAction();
        $categoryAction->setCreateProcessor($categoryCreateProcessor);
        $categoryAction->setDeleteProcessor($categoryDeleteProcessor);
        $categoryAction->setUpdateProcessor($categoryUpdateProcessor);

        // initialize the action that provides URL rewrite CRUD functionality
        $urlRewriteCreateProcessor = new UrlRewriteCreateProcessor();
        $urlRewriteCreateProcessor->setUtilityClassName($utilityClassName);
        $urlRewriteCreateProcessor->setConnection($connection);
        $urlRewriteCreateProcessor->init();
        $urlRewriteDeleteProcessor = new UrlRewriteDeleteProcessor();
        $urlRewriteDeleteProcessor->setUtilityClassName($utilityClassName);
        $urlRewriteDeleteProcessor->setConnection($connection);
        $urlRewriteDeleteProcessor->init();
        $urlRewriteUpdateProcessor = new UrlRewriteUpdateProcessor();
        $urlRewriteUpdateProcessor->setUtilityClassName($utilityClassName);
        $urlRewriteUpdateProcessor->setConnection($connection);
        $urlRewriteUpdateProcessor->init();
        $urlRewriteAction = new UrlRewriteAction();
        $urlRewriteAction->setCreateProcessor($urlRewriteCreateProcessor);
        $urlRewriteAction->setDeleteProcessor($urlRewriteDeleteProcessor);
        $urlRewriteAction->setUpdateProcessor($urlRewriteUpdateProcessor);

        // initialize the category assembler
        $categoryAssembler = new CategoryAssembler($categoryRepository, $categoryVarcharRepository);

        // initialize the category processor
        $processorType = static::getProcessorType();
        $categoryBunchProcessor = new $processorType();
        $categoryBunchProcessor->setConnection($connection);
        $categoryBunchProcessor->setCategoryRepository($categoryRepository);
        $categoryBunchProcessor->setCategoryDatetimeRepository($categoryDatetimeRepository);
        $categoryBunchProcessor->setCategoryDecimalRepository($categoryDecimalRepository);
        $categoryBunchProcessor->setCategoryIntRepository($categoryIntRepository);
        $categoryBunchProcessor->setCategoryTextRepository($categoryTextRepository);
        $categoryBunchProcessor->setCategoryVarcharRepository($categoryVarcharRepository);
        $categoryBunchProcessor->setEavAttributeRepository($eavAttributeRepository);
        $categoryBunchProcessor->setUrlRewriteRepository($urlRewriteRepository);
        $categoryBunchProcessor->setCategoryDatetimeAction($categoryDatetimeAction);
        $categoryBunchProcessor->setCategoryDecimalAction($categoryDecimalAction);
        $categoryBunchProcessor->setCategoryIntAction($categoryIntAction);
        $categoryBunchProcessor->setCategoryAction($categoryAction);
        $categoryBunchProcessor->setCategoryTextAction($categoryTextAction);
        $categoryBunchProcessor->setCategoryVarcharAction($categoryVarcharAction);
        $categoryBunchProcessor->setUrlRewriteAction($urlRewriteAction);
        $categoryBunchProcessor->setCategoryAssembler($categoryAssembler);

        // return the instance
        return $categoryBunchProcessor;
    }
}
