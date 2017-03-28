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
        $eavAttributeRepository = new EavAttributeRepository($connection, $utilityClassName);

        // initialize the repository that provides category query functionality
        $categoryRepository = new CategoryRepository($connection, $utilityClassName);

        // initialize the repository that provides category datetime attribute query functionality
        $categoryDatetimeRepository = new CategoryDatetimeRepository($connection, $utilityClassName);

        // initialize the repository that provides category decimal attribute query functionality
        $categoryDecimalRepository = new CategoryDecimalRepository($connection, $utilityClassName);

        // initialize the repository that provides category integer attribute query functionality
        $categoryIntRepository = new CategoryIntRepository($connection, $utilityClassName);

        // initialize the repository that provides category text attribute query functionality
        $categoryTextRepository = new CategoryTextRepository($connection, $utilityClassName);

        // initialize the repository that provides category varchar attribute query functionality
        $categoryVarcharRepository = new CategoryVarcharRepository($connection, $utilityClassName);

        // initialize the repository that provides URL rewrite query functionality
        $urlRewriteRepository = new UrlRewriteRepository($connection, $utilityClassName);

        // initialize the action that provides category datetime attribute CRUD functionality
        $categoryDatetimeAction = new CategoryDatetimeAction(
            new CategoryDatetimeCreateProcessor($connection, $utilityClassName),
            new CategoryDatetimeUpdateProcessor($connection, $utilityClassName)
        );

        // initialize the action that provides category decimal attribute CRUD functionality
        $categoryDecimalAction = new CategoryDecimalAction(
            new CategoryDecimalCreateProcessor($connection, $utilityClassName),
            new CategoryDecimalUpdateProcessor($connection, $utilityClassName)
        );

        // initialize the action that provides category integer attribute CRUD functionality
        $categoryIntAction = new CategoryIntAction(
            new CategoryIntCreateProcessor($connection, $utilityClassName),
            new CategoryIntUpdateProcessor($connection, $utilityClassName)
        );

        // initialize the action that provides category text attribute CRUD functionality
        $categoryTextAction = new CategoryTextAction(
            new CategoryTextCreateProcessor($connection, $utilityClassName),
            new CategoryTextUpdateProcessor($connection, $utilityClassName)
        );

        // initialize the action that provides category varchar attribute CRUD functionality
        $categoryVarcharAction = new CategoryVarcharAction(
            new CategoryVarcharCreateProcessor($connection, $utilityClassName),
            new CategoryVarcharUpdateProcessor($connection, $utilityClassName)
        );

        // initialize the action that provides category CRUD functionality
        $categoryAction = new CategoryAction(
            new CategoryCreateProcessor($connection, $utilityClassName),
            new CategoryUpdateProcessor($connection, $utilityClassName),
            new CategoryDeleteProcessor($connection, $utilityClassName)
        );

        // initialize the action that provides URL rewrite CRUD functionality
        $urlRewriteAction = new UrlRewriteAction(
            new UrlRewriteCreateProcessor($connection, $utilityClassName),
            new UrlRewriteUpdateProcessor($connection, $utilityClassName),
            new UrlRewriteDeleteProcessor($connection, $utilityClassName)
        );

        // initialize the category assembler
        $categoryAssembler = new CategoryAssembler($categoryRepository, $categoryVarcharRepository);

        // initialize and return the category processor
        $processorType = static::getProcessorType();
        return new $processorType(
            $connection,
            $categoryRepository,
            $categoryDatetimeRepository,
            $categoryDecimalRepository,
            $categoryIntRepository,
            $categoryTextRepository,
            $categoryVarcharRepository,
            $eavAttributeRepository,
            $urlRewriteRepository,
            $categoryDatetimeAction,
            $categoryDecimalAction,
            $categoryIntAction,
            $categoryAction,
            $categoryTextAction,
            $categoryVarcharAction,
            $urlRewriteAction,
            $categoryAssembler
        );
    }
}
