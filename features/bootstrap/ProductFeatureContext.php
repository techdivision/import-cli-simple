<?php

use PHPUnit\Framework\Assert;
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use TechDivision\Import\Utils\CommandNames;

/**
 * Defines application features from the specific context.
 */
class ProductFeatureContext implements Context
{

    /**
     * @var \ConsoleContext
     */
    private $consoleContext;

    /**
     * @var \FeatureContext
     */
    private $featureContext;

    /** @BeforeScenario */
    public function before(BeforeScenarioScope $scope)
    {

        // load the environment
        /** @var Behat\Behat\Context\Environment\InitializedContextEnvironment $environment */
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

        /** @var \Behat\Mink\Element\NodeElement $title */
        $title = $this->featureContext->getSession()->getPage()->find('css', 'title');
        Assert::assertSame($arg1, $title->getText());

        if ($this->featureContext->getSession()->getStatusCode() === 200 && $arg2 !== null) {
            /** @var \Behat\Mink\Element\NodeElement $price */
            $price = $this->featureContext->getSession()->getPage()->find('xpath', '//*[@class="price"]');
            Assert::assertSame($arg2, $price->getText());
        }
    }

    /**
     * @Given files with products to be updated are available
     * @Given files with products to be deleted are available
     */
    public function filesWithProductsToBeUpdatedAreAvailable()
    {
        for ($i = 1; $i < 5; $i++) {
            $this->consoleContext->aThirdPartySystemHasCopiedTheFileIntoTheImportFolder(
                sprintf('vendor/techdivision/import-sample-data/generic/data/products/add-update/product-import_20161021-161909_0%s.csv', $i),
                'var/importexport'
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
                sprintf('vendor/techdivision/import-sample-data/generic/data/products/replace/product-import_20161021-161909_0%s.csv', $i),
                'var/importexport'
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
