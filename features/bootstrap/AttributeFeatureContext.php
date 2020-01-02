<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

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

        /** @var Behat\Behat\Context\Environment\InitializedContextEnvironment $environment */
        $environment = $scope->getEnvironment();

        $this->consoleContext = $environment->getContext(ConsoleContext::class);
    }

    /**
     * @Given attributes have been imported
     */
    public function attributesHaveBeenImported()
    {

        $this->consoleContext->aThirdPartySystemHasCopiedTheFileIntoTheImportFolder(
            'vendor/techdivision/import-sample-data/generic/data/attributes/add-update/attribute-import_20170428-124902_01.csv',
            'var/importexport'
        );

        $this->consoleContext->theCommandHasBeenExecuted('bin/import-simple import:create:ok-file');
        $this->consoleContext->theCommandHasBeenExecuted('bin/import-simple import:attributes');
    }
}
