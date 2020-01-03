<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use TechDivision\Import\Utils\CommandNames;

/**
 * Defines application features from the specific context.
 */
class CategoryFeatureContext implements Context
{

    /**
     * @var \ConsoleContext
     */
    private $consoleContext;

    /** @BeforeScenario */
    public function before(BeforeScenarioScope $scope)
    {

        // load the environment
        /** @var Behat\Behat\Context\Environment\InitializedContextEnvironment $environment */
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
            'vendor/techdivision/import-sample-data/generic/data/categories/add-update/category-import_20161024-194026_01.csv',
            'var/importexport'
        );
    }

    /**
     * @Given the category import process has been started
     */
    public function theCategoryImportProcessHasBeenStarted()
    {
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s', CommandNames::IMPORT_CREATE_OK_FILE));
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s add-update', CommandNames::IMPORT_CATEGORIES));
    }

    /**
     * @Given the category deletion process has been started
     */
    public function theCategoryDeletionProcessHasBeenStarted()
    {
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s', CommandNames::IMPORT_CREATE_OK_FILE));
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s delete', CommandNames::IMPORT_CATEGORIES));
    }

    /**
     * @Given the category replacement process has been started
     */
    public function theCategoryReplacementProcessHasBeenStarted()
    {
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s', CommandNames::IMPORT_CREATE_OK_FILE));
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s replace', CommandNames::IMPORT_CATEGORIES));
    }
}
