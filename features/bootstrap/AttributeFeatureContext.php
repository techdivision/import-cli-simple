<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use TechDivision\Import\Utils\CommandNames;

/**
 * Defines application features from the specific context.
 */
class AttributeFeatureContext implements Context
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
     * @Given attributes have been imported
     */
    public function attributesHaveBeenImported()
    {
        $this->filesWithAttributesToBeUpdatedAreAvailable();
        $this->theAttributeImportProcessHasBeenStarted();
    }

    /**
     * @Given files with attributes to be updated are available
     * @Given files with attributes to be replaced are available
     * @Given files with attributes to be deleted are available
     */
    public function filesWithAttributesToBeUpdatedAreAvailable()
    {
        $this->consoleContext->aThirdPartySystemHasCopiedTheFileIntoTheImportFolder(
            'vendor/techdivision/import-sample-data/generic/data/attributes/add-update/attribute-import_20170428-124902_01.csv',
            'var/importexport'
        );
    }

    /**
     * @Given the attribute(s) import process has been started
     */
    public function theAttributeImportProcessHasBeenStarted()
    {
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s', CommandNames::IMPORT_CREATE_OK_FILE));
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s add-update', CommandNames::IMPORT_ATTRIBUTES));
    }

    /**
     * @Given the attribute(s) deletion process has been started
     */
    public function theAttributeDeletionProcessHasBeenStarted()
    {
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s', CommandNames::IMPORT_CREATE_OK_FILE));
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s delete', CommandNames::IMPORT_ATTRIBUTES));
    }

    /**
     * @Given the attribute(s) replacement process has been started
     */
    public function theAttributeReplacementProcessHasBeenStarted()
    {
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s', CommandNames::IMPORT_CREATE_OK_FILE));
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s replace', CommandNames::IMPORT_ATTRIBUTES));
    }
}
