<?php

/**
 * TechDivision\Import\Cli\Simple\Contexts\AttributeFeatureContext
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
 * Defines attribute features from the specific context.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class AttributeFeatureContext implements Context
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
            'vendor/techdivision/import-sample-data/generic/data/attributes/add-update/attribute-import_20170428-124902_01.csv'
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
