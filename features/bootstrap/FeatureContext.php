<?php

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends MinkContext implements Context, KernelAwareContext
{

    use Behat\Symfony2Extension\Context\KernelDictionary;

    /** @BeforeScenario */
    public function before(BeforeScenarioScope $scope)
    {
        $this->setMinkParameter('base_url', $this->getContainer()->getParameter('base_url'));
    }
}
