<?php

/**
 * Defines application features from the specific context.
 */
class ProductFeatureContext extends FeatureContext
{

    /**
     * @Given is on our page
     */
    public function isOnOurPage()
    {
    }

    /**
     * @When he is on :arg1
     */
    public function heIsOn($arg1)
    {
        $this->visitPath($arg1);
    }

    /**
     * @Then the page should return status :arg1
     */
    public function thePageShouldReturnStatus2($arg1)
    {
        PHPUnit_Framework_Assert::assertSame((integer) $arg1, $this->getSession()->getStatusCode());
    }

    /**
     * @Then has title :arg1 and contain price :arg2
     */
    public function hasTitleAndContainPrice($arg1, $arg2)
    {

        /** @var \Behat\Mink\Element\NodeElement $title */
        $title = $this->getSession()->getPage()->find('css', 'title');
        PHPUnit_Framework_Assert::assertSame($arg1, $title->getText());

        /** @var \Behat\Mink\Element\NodeElement $price */
        $price = $this->getSession()->getPage()->find('xpath', '//*[@class="price"]');
        PHPUnit_Framework_Assert::assertSame($arg2, $price->getText());
    }
}
