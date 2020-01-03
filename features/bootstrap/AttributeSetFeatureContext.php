<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use TechDivision\Import\Utils\CommandNames;

/**
 * Defines application features from the specific context.
 */
class AttributeSetFeatureContext implements Context
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
     * @Given attribute sets have been imported
     */
    public function attributeSetsHaveBeenImported()
    {
        $this->filesWithAttributeSetsToBeUpdatedAreAvailable();
        $this->theAttributeSetImportProcessHasBeenStarted();
    }

    /**
     * @Given files with attribute sets to be updated are available
     * @Given files with attribute sets to be deleted are available
     * @Given files with attribute sets to be replaced are available
     */
    public function filesWithAttributeSetsToBeUpdatedAreAvailable()
    {
        $this->consoleContext->aThirdPartySystemHasCopiedTheFileIntoTheImportFolder(
            'vendor/techdivision/import-sample-data/generic/data/attributes-set/add-update/attribute-set-import_20190104-114000_01.csv',
            'var/importexport'
        );
    }

    /**
     * @Given the attribute set import process has been started
     */
    public function theAttributeSetImportProcessHasBeenStarted()
    {
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s', CommandNames::IMPORT_CREATE_OK_FILE));
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s add-update', CommandNames::IMPORT_ATTRIBUTES_SET));
    }

    /**
     * @Given the attribute set deletion process has been started
     */
    public function theAttributeSetDeletionProcessHasBeenStarted()
    {
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s', CommandNames::IMPORT_CREATE_OK_FILE));
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s delete', CommandNames::IMPORT_ATTRIBUTES_SET));
    }

    /**
     * @Given the attribute set replacement process has been started
     */
    public function theAttributeSetReplacementProcessHasBeenStarted()
    {
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s', CommandNames::IMPORT_CREATE_OK_FILE));
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s replace', CommandNames::IMPORT_ATTRIBUTES_SET));
    }
}
