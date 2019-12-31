<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Tester\Exception\PendingException;
use TechDivision\Import\Adapter\PhpFilesystemAdapter;
use Behat\MinkExtension\Context\MinkContext;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends MinkContext implements Context
{

    /**
     * The Magento 2 installation directory.
     *
     * @var string
     */
    protected $installDir;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     *
     * @param string $installDir The Magento 2 installation directory
     */
    public function __construct($installDir)
    {
        $this->installDir = $installDir;
    }

    /** @BeforeFeature */
    public static function prepareForTheFeature()
    {
    }

    /** @BeforeScenario */
    public function before(BeforeScenarioScope $scope)
    {

        $filesystemAdapter = new PhpFilesystemAdapter();

        foreach (glob(sprintf('%s/var/importexport/*', $this->installDir)) as $file) {
            if (is_file($file)) {
                $filesystemAdapter->delete($file);
            } else {
                $filesystemAdapter->removeDir($file, true);
            }
        }
    }

    /** @AfterScenario */
    public function after(AfterScenarioScope $scope)
    {
    }

    protected function appendInstallDir($cmd)
    {
        return sprintf('%s --installation-dir=%s', $cmd, $this->installDir);
    }

    protected function prependInstallDir($cmd)
    {
        return sprintf('%s/%s', $this->installDir, $cmd);
    }

    /**
     * @Given a third party system has copied the file :arg1 into the import folder :arg2
     */
    public function aThirdPartySystemHasCopiedTheFileIntoTheImportFolder2($arg1, $arg2)
    {

        if (copy($arg1, $dest = sprintf('%s/%s/%s', $this->installDir, $arg2, basename($arg1)))) {
            return;
        }

        throw new \Exception(sprintf('Can\'t copy file %s to %s', $arg1, $dest));
    }

    /**
    * @Given that a new file :arg1 containing data is available
    */
    public function thatANewFileContainingDataIsAvailable($arg1)
    {

        if (is_file($this->prependInstallDir($arg1))) {
            return;
        }

        throw new \Exception(sprintf('Can\'t find file %s', $arg1));
    }

    /**
     * @When the command :arg1 has been executed
     */
    public function theCommandHasBeenExecuted($arg1)
    {
        $ret = 0;
        system($this->appendInstallDir($arg1), $ret);
        PHPUnit_Framework_Assert::assertNotEquals(1, $ret);
    }

    /**
     * @When the magento command :arg1 has been executed
     */
    public function theMagentoCommandHasBeenExecuted($arg1)
    {
        $ret = 0;
        system($this->prependInstallDir($arg1), $ret);
        PHPUnit_Framework_Assert::assertNotEquals(1, $ret);
    }

    /**
     * @Then all the data in the file :arg1 has been imported
     */
    public function allTheDataInTheFileHasBeenImported2($arg1)
    {
        throw new PendingException();
    }
}
