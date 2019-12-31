<?php

/**
 * Defines application features from the specific context.
 */
class ProductFeatureContext extends FeatureContext
{

  /**
    * @Then the page :arg1 should be available
    */
    public function thePageShouldBeAvailable($arg1)
    {

        $this->visitPath($arg1);
        PHPUnit_Framework_Assert::assertSame(200, $this->getSession()->getStatusCode());
    }

    /**
     * @Then the page :arg1 should contain the price :arg2
     */
    public function thePageShouldContainThePrice($arg1, $arg2)
    {

        $this->visitPath($arg1);

        /** @var \Behat\Mink\Element\NodeElement $price */
        $prices = $this->getSession()->getPage()->findAll('xpath', '//*[@id="product-price-21606"]/span');

        foreach ($prices as $price) {
            echo "Found value: " . $price->getValue() . PHP_EOL;
            PHPUnit_Framework_Assert::assertSame($arg2, $price->getValue());
        }
    }
}
