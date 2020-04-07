<?php

/**
 * TechDivision\Import\Cli\Simple\Contexts\ProductFeatureContext
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

use PHPUnit\Framework\Assert;
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use TechDivision\Import\Utils\CommandNames;

/**
 * Defines product features from the specific context.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class ProductFeatureContext implements Context
{

    /**
     * @var \TechDivision\Import\Cli\Simple\Contexts\ConsoleContext
     */
    private $consoleContext;

    /**
     * @var \TechDivision\Import\Cli\Simple\Contexts\FeatureContext
     */
    private $featureContext;

    /** @BeforeScenario */
    public function before(BeforeScenarioScope $scope)
    {

        // load the environment
        /** @var \Behat\Behat\Context\Environment\InitializedContextEnvironment $environment */
        $environment = $scope->getEnvironment();

        // make the console and the feature context available
        $this->consoleContext = $environment->getContext(ConsoleContext::class);
        $this->featureContext = $environment->getContext(FeatureContext::class);
    }

    /**
     * @Then title and price are :arg1,( )
     * @Then title and price are :arg1, :arg2
     */
    public function assertTitleAndPrice($arg1, $arg2 = null)
    {

        // load and validate the title
        /** @var \Behat\Mink\Element\NodeElement $title */
        $title = $this->featureContext->getSession()->getPage()->find('css', 'title');
        Assert::assertEquals($arg1, $title->getText());

        // validate the price if the page has been loaded successfully
        if ($this->featureContext->getSession()->getStatusCode() === 200 && $arg2 !== null) {
            /** @var \Behat\Mink\Element\NodeElement $price */
            $price = $this->featureContext->getSession()->getPage()->find('xpath', '//*[@class="price"]');
            Assert::assertEquals($arg2, $price->getText());
        }
    }

    /**
     * @Given products have been imported
     */
    public function productsHaveBeenImported()
    {
        $this->filesWithProductsToBeUpdatedAreAvailable();
        $this->theProductImportProcessHasBeenStarted();
    }

    /**
     * @Given products have been replaced
     */
    public function productsHaveBeenReplaced()
    {
        $this->filesWithProductsToBeReplacedAreAvailable();
        $this->theProductImportProcessHasBeenStarted();
    }

    /**
     * @Given files with products to be updated are available
     * @Given files with products to be deleted are available
     */
    public function filesWithProductsToBeUpdatedAreAvailable()
    {
        for ($i = 1; $i < 5; $i++) {
            $this->consoleContext->aThirdPartySystemHasCopiedTheFileIntoTheImportFolder(
                sprintf('vendor/techdivision/import-sample-data/generic/data/products/add-update/product-import_20161021-161909_0%s.csv', $i)
            );
        }
    }

    /**
     * @Given files with products to be replaced are available
     */
    public function filesWithProductsToBeReplacedAreAvailable()
    {
        for ($i = 1; $i < 5; $i++) {
            $this->consoleContext->aThirdPartySystemHasCopiedTheFileIntoTheImportFolder(
                sprintf('vendor/techdivision/import-sample-data/generic/data/products/replace/product-import_20161021-161909_0%s.csv', $i)
            );
        }
    }

    /**
     * @Given the product import process has been started
     */
    public function theProductImportProcessHasBeenStarted()
    {
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s', CommandNames::IMPORT_CREATE_OK_FILE));
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s add-update', CommandNames::IMPORT_PRODUCTS));
    }

    /**
     * @Given the product deletion process has been started
     */
    public function theProductDeletionProcessHasBeenStarted()
    {
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s', CommandNames::IMPORT_CREATE_OK_FILE));
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s delete', CommandNames::IMPORT_PRODUCTS));
    }

    /**
     * @Given the product replacement process has been started
     */
    public function theProductReplacementProcessHasBeenStarted()
    {
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s', CommandNames::IMPORT_CREATE_OK_FILE));
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s replace', CommandNames::IMPORT_PRODUCTS));
    }
}
