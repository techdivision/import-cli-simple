<?php

/**
 * TechDivision\Import\Cli\Simple\Contexts\TierPriceFeatureContext
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
 * Defines tier price features from the specific context.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class TierPriceFeatureContext implements Context
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
     * @Given tier prices have been imported
     */
    public function tierPricesHaveBeenImported()
    {
        $this->filesWithTierPricesToBeUpdatedAreAvailable();
        $this->theTierPriceImportProcessHasBeenStarted();
    }

    /**
     * @Given files with tier prices to be updated are available
     * @Given files with tier prices to be deleted are available
     */
    public function filesWithTierPricesToBeUpdatedAreAvailable()
    {
        $this->consoleContext->aThirdPartySystemHasCopiedTheFileIntoTheImportFolder(
            'vendor/techdivision/import-sample-data/generic/data/tier-prices/add-update/product-import-tier-price_20190425-131237_01.csv'
        );
    }

    /**
     * @Given files with tier prices to be replaced are available
     */
    public function filesWithTierPricesToBeReplacedAreAvailable()
    {
        $this->consoleContext->aThirdPartySystemHasCopiedTheFileIntoTheImportFolder(
            'vendor/techdivision/import-sample-data/generic/data/tier-prices/replace/product-import-tier-price_20190425-131237_01.csv'
            );
    }

    /**
     * @Given the tier price import process has been started
     */
    public function theTierPriceImportProcessHasBeenStarted()
    {
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s', CommandNames::IMPORT_CREATE_OK_FILE));
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s add-update', CommandNames::IMPORT_PRODUCTS_TIER_PRICE));
    }

    /**
     * @Given the tier price deletion process has been started
     */
    public function theTierPriceDeletionProcessHasBeenStarted()
    {
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s', CommandNames::IMPORT_CREATE_OK_FILE));
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s delete', CommandNames::IMPORT_PRODUCTS_TIER_PRICE));
    }

    /**
     * @Given the tier price replacement process has been started
     */
    public function theTierReplacementProcessHasBeenStarted()
    {
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s', CommandNames::IMPORT_CREATE_OK_FILE));
        $this->consoleContext->theCommandHasBeenExecuted(sprintf('bin/import-simple %s replace', CommandNames::IMPORT_PRODUCTS_TIER_PRICE));
    }
}
