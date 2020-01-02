<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

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

        /** @var Behat\Behat\Context\Environment\InitializedContextEnvironment $environment */
        $environment = $scope->getEnvironment();

        $this->consoleContext = $environment->getContext(ConsoleContext::class);
    }

    /**
     * @Given categories have been imported
     */
    public function categoriesHaveBeenImported()
    {

        $this->consoleContext->aThirdPartySystemHasCopiedTheFileIntoTheImportFolder(
            'vendor/techdivision/import-sample-data/generic/data/categories/add-update/category-import_20161024-194026_01.csv',
            'var/importexport'
        );

        $this->consoleContext->theCommandHasBeenExecuted('bin/import-simple import:create:ok-file');
        $this->consoleContext->theCommandHasBeenExecuted('bin/import-simple import:categories');
    }
}
