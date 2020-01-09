<?php

/**
 * TechDivision\Import\Cli\Simple\Contexts\AttributeSetFeatureContext
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
 * Defines attribute set features from the specific context.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class AttributeSetFeatureContext implements Context
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
            'vendor/techdivision/import-sample-data/generic/data/attributes-set/add-update/attribute-set-import_20190104-114000_01.csv'
        );
    }

    /**
     * @Given the attribute set import process has been started
     */
    public function theAttributeSetImportProcessHasBeenStarted()
    {
        $this->consoleContext->theSimpleCommandHasBeenExecuted(sprintf('bin/import-simple %s', CommandNames::IMPORT_CREATE_OK_FILE));
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s add-update', CommandNames::IMPORT_ATTRIBUTES_SET));
    }

    /**
     * @Given the attribute set deletion process has been started
     */
    public function theAttributeSetDeletionProcessHasBeenStarted()
    {
        $this->consoleContext->theSimpleCommandHasBeenExecuted(sprintf('bin/import-simple %s', CommandNames::IMPORT_CREATE_OK_FILE));
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s delete', CommandNames::IMPORT_ATTRIBUTES_SET));
    }

    /**
     * @Given the attribute set replacement process has been started
     */
    public function theAttributeSetReplacementProcessHasBeenStarted()
    {
        $this->consoleContext->theSimpleCommandHasBeenExecuted(sprintf('bin/import-simple %s', CommandNames::IMPORT_CREATE_OK_FILE));
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s replace', CommandNames::IMPORT_ATTRIBUTES_SET));
    }
}
