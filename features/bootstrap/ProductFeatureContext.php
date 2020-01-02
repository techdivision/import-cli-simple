<?php

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

        /** @var Behat\Behat\Context\Environment\InitializedContextEnvironment $environment */
        $environment = $scope->getEnvironment();

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
        PHPUnit_Framework_Assert::assertSame($arg1, $title->getText());

        if ($this->featureContext->getSession()->getStatusCode() === 200 && $arg2 !== null) {
            /** @var \Behat\Mink\Element\NodeElement $price */
            $price = $this->featureContext->getSession()->getPage()->find('xpath', '//*[@class="price"]');
            PHPUnit_Framework_Assert::assertSame($arg2, $price->getText());
        }
    }

    /**
     * @Given files with products to be updated are available
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
     * @Given the import process has been started
     */
    public function theImportProcessHasBeenStarted()
    {
        $this->consoleContext->theCommandHasBeenExecuted('bin/import-simple import:create:ok-file');
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s', CommandNames::IMPORT_PRODUCTS));
    }

    /**
     * @When the import process has been finished
     */
    public function assertExitCode()
    {

        PHPUnit_Framework_Assert::assertSame(0, $this->consoleContext->getExitCode());

        $this->assertSuccessMessage();
    }

    /**
     * @Then a success message has to be rendered
     */
    public function assertSuccessMessage()
    {
        $this->assertMessage(
            sprintf(
                '/Successfully executed command %s with serial \w+-\w+-\w+-\w+-\w+ in \d+:\d+:\d+ s/',
                CommandNames::IMPORT_PRODUCTS
            )
        );
    }

    /**
     * @Then a message :arg1 has to be rendered
     */
    public function assertMessage($arg1)
    {

        $output = $this->consoleContext->getOutput();

        PHPUnit_Framework_Assert::assertRegExp($arg1, array_pop($output));
    }
}
