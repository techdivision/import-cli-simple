<?php

/**
 * TechDivision\Import\Cli\Simple\Contexts\MsiFeatureContext
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
 * Defines MSI features from the specific context.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class MsiFeatureContext implements Context
{

    /**
     * @var \TechDivision\Import\Cli\Simple\Contexts\ConsoleContext
     */
    private $consoleContext;

    /**
     * @var \TechDivision\Import\Cli\Simple\Contexts\FeatureContext
     */
    private $featureContext;

    /**
     * @var \TechDivision\Import\Cli\Simple\Contexts\ProductFeatureContext
     */
    private $productFeatureContext;

    /** @BeforeScenario */
    public function before(BeforeScenarioScope $scope)
    {

        // load the environment
        /** @var \Behat\Behat\Context\Environment\InitializedContextEnvironment $environment */
        $environment = $scope->getEnvironment();

        // make console, feature and product feature context available
        $this->consoleContext = $environment->getContext(ConsoleContext::class);
        $this->featureContext = $environment->getContext(FeatureContext::class);
        $this->productFeatureContext = $environment->getContext(ProductFeatureContext::class);
    }

    /**
     * @Then title, price and status are :arg1
     * @Then title, price and status are :arg1, ,( )
     * @Then title, price and status are :arg1, :arg2,
     * @Then title, price and status are :arg1, , :arg3
     * @Then title, price and status are :arg1, :arg2, :arg3
     */
    public function assertTitlePriceAndStatus($arg1, $arg2 = null, $arg3 = null)
    {
        $this->productFeatureContext->assertTitleAndPrice($arg1, $arg2);
        $this->featureContext->assertPageContainsText($arg3);
    }

    /**
     * @Given MSI has been imported
     */
    public function msiHasBeenImported()
    {
        $this->filesWithMsiToBeUpdatedAreAvailable();
        $this->theMsiImportProcessHasBeenStarted();
    }

    /**
     * @Given files with MSI to be updated are available
     * @Given files with MSI to be deleted are available
     */
    public function filesWithMsiToBeUpdatedAreAvailable()
    {
        $this->consoleContext->aThirdPartySystemHasCopiedTheFileIntoTheImportFolder(
            'vendor/techdivision/import-sample-data/generic/data/msi/add-update/product-import-inventory-msi_20190423-100739_01.csv'
        );
    }

    /**
     * @Given files with MSI to be replaced are available
     */
    public function filesWithMsiToBeReplacedAreAvailable()
    {
        $this->consoleContext->aThirdPartySystemHasCopiedTheFileIntoTheImportFolder(
            'vendor/techdivision/import-sample-data/generic/data/msi/replace/product-import-inventory-msi_20190423-100739_01.csv'
        );
    }

    /**
     * @Given the MSI import process has been started
     */
    public function theMsiImportProcessHasBeenStarted()
    {
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s', CommandNames::IMPORT_CREATE_OK_FILE));
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s add-update', CommandNames::IMPORT_PRODUCTS_INVENTORY_MSI));
    }

    /**
     * @Given the MSI deletion process has been started
     */
    public function theMsiDeletionProcessHasBeenStarted()
    {
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s', CommandNames::IMPORT_CREATE_OK_FILE));
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s delete', CommandNames::IMPORT_PRODUCTS_INVENTORY_MSI));
    }

    /**
     * @Given the MSI replacement process has been started
     */
    public function theMsiReplacementProcessHasBeenStarted()
    {
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s', CommandNames::IMPORT_CREATE_OK_FILE));
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s replace', CommandNames::IMPORT_PRODUCTS_INVENTORY_MSI));
    }
}
