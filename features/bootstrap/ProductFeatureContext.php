<?php

/**
 * Defines application features from the specific context.
 */
class ProductFeatureContext extends FeatureContext
{

    /**
     * @Then the page :arg1 should return status :arg2
     */
    public function thePageShouldReturnStatus($arg1, $arg2)
    {

        $this->visitPath($arg1);

        PHPUnit_Framework_Assert::assertSame((integer) $arg2, $this->getSession()->getStatusCode());
    }

    /**
     * @Then the page :arg1 should return status :arg2 has title :arg3 and contain price :arg4
     */
    public function thePageShouldReturnStatusHasTitleAndContainPrice($arg1, $arg2, $arg3, $arg4)
    {

        $this->visitPath($arg1);

        PHPUnit_Framework_Assert::assertSame((integer) $arg2, $this->getSession()->getStatusCode());

        /** @var \Behat\Mink\Element\NodeElement $title */
        $title = $this->getSession()->getPage()->find('css', 'title');
        PHPUnit_Framework_Assert::assertSame($arg3, $title->getText());

        /** @var \Behat\Mink\Element\NodeElement $price */
        $price = $this->getSession()->getPage()->find('xpath', '//*[@class="price"]');
        PHPUnit_Framework_Assert::assertSame($arg4, $price->getText());
    }
}
