<?php

/**
 * TechDivision\Import\Cli\Simple\Contexts\CategoryFeatureContext
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
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Cli\Simple\Contexts;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use TechDivision\Import\Utils\CommandNames;

/**
 * Defines category features from the specific context.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class CategoryFeatureContext implements Context
{

    /**
     * @var \TechDivision\Import\Cli\Simple\Contexts\ConsoleContext
     */
    private $consoleContext;

    /** @BeforeScenario */
    public function before(BeforeScenarioScope $scope)
    {

        // load the environment
        /** @var \Behat\Behat\Context\Environment\InitializedContextEnvironment $environment */
        $environment = $scope->getEnvironment();

        // make the console context available
        $this->consoleContext = $environment->getContext(ConsoleContext::class);
    }

    /**
     * @Given categories have been imported
     */
    public function categoriesHaveBeenImported()
    {
        $this->filesWithCategoriesToBeUpdatedAreAvailable();
        $this->theCategoryImportProcessHasBeenStarted();
    }

    /**
     * @Given files with categories to be updated are available
     * @Given files with categories to be deleted are available
     * @Given files with categories to be replaced are available
     */
    public function filesWithCategoriesToBeUpdatedAreAvailable()
    {
        $this->consoleContext->aThirdPartySystemHasCopiedTheFileIntoTheImportFolder(
            'vendor/techdivision/import-sample-data/generic/data/categories/add-update/category-import_20161024-194026_01.csv'
        );
    }

    /**
     * @Given the category import process has been started
     */
    public function theCategoryImportProcessHasBeenStarted()
    {
        $this->consoleContext->theSimpleCommandHasBeenExecuted(sprintf('bin/import-simple %s', CommandNames::IMPORT_CREATE_OK_FILE));
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s add-update', CommandNames::IMPORT_CATEGORIES));
    }

    /**
     * @Given the category deletion process has been started
     */
    public function theCategoryDeletionProcessHasBeenStarted()
    {
        $this->consoleContext->theSimpleCommandHasBeenExecuted(sprintf('bin/import-simple %s', CommandNames::IMPORT_CREATE_OK_FILE));
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s delete', CommandNames::IMPORT_CATEGORIES));
    }

    /**
     * @Given the category replacement process has been started
     */
    public function theCategoryReplacementProcessHasBeenStarted()
    {
        $this->consoleContext->theSimpleCommandHasBeenExecuted(sprintf('bin/import-simple %s', CommandNames::IMPORT_CREATE_OK_FILE));
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s replace', CommandNames::IMPORT_CATEGORIES));
    }
}
