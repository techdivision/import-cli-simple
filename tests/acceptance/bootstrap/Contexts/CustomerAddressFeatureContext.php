<?php

/**
 * TechDivision\Import\Cli\Simple\Contexts\CustomerAddressFeatureContext
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
 * Defines customer address features from the specific context.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class CustomerAddressFeatureContext implements Context
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
     * @Given customer addresses have been imported
     */
    public function customerAddressesHaveBeenImported()
    {
        $this->filesWithCustomerAddressesToBeUpdatedAreAvailable();
        $this->theCustomerAddressImportProcessHasBeenStarted();
    }

    /**
     * @Given files with customer addresses to be updated are available
     * @Given files with customer addresses to be deleted are available
     * @Given files with customer addresses to be replaced are available
     */
    public function filesWithCustomerAddressesToBeUpdatedAreAvailable()
    {
        $this->consoleContext->aThirdPartySystemHasCopiedTheFileIntoTheImportFolder(
            'vendor/techdivision/import-sample-data/generic/data/customers-address/add-update/customer-address-import_20181217-085523_01.csv'
        );
    }

    /**
     * @Given the customer address import process has been started
     */
    public function theCustomerAddressImportProcessHasBeenStarted()
    {
        $this->consoleContext->theSimpleCommandHasBeenExecuted(sprintf('bin/import-simple %s', CommandNames::IMPORT_CREATE_OK_FILE));
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s add-update', CommandNames::IMPORT_CUSTOMERS_ADDRESS));
    }

    /**
     * @Given the customer address deletion process has been started
     */
    public function theCustomerAddressDeletionProcessHasBeenStarted()
    {
        $this->consoleContext->theSimpleCommandHasBeenExecuted(sprintf('bin/import-simple %s', CommandNames::IMPORT_CREATE_OK_FILE));
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s delete', CommandNames::IMPORT_CUSTOMERS_ADDRESS));
    }

    /**
     * @Given the customer address replacement process has been started
     */
    public function theCustomerAddressReplacementProcessHasBeenStarted()
    {
        $this->consoleContext->theSimpleCommandHasBeenExecuted(sprintf('bin/import-simple %s', CommandNames::IMPORT_CREATE_OK_FILE));
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s replace', CommandNames::IMPORT_CUSTOMERS_ADDRESS));
    }
}
