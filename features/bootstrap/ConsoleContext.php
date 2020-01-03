<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Tester\Exception\PendingException;
use TechDivision\Import\Adapter\PhpFilesystemAdapter;
use Behat\Symfony2Extension\Context\KernelAwareContext;

/**
 * Defines application features from the specific context.
 */
class ConsoleContext implements Context, KernelAwareContext
{

    use Behat\Symfony2Extension\Context\KernelDictionary;

    /**
     * The Magento 2 installation directory.
     *
     * @var string
     */
    private $installDir;

    /**
     * The output of the last executed command.
     *
     * @var array
     */
    private $output = array();

    /**
     * The return value of the last executed command.
     *
     * @var integer
     */
    private $exitCode = 0;

    /** @BeforeFeature */
    public static function prepareForTheFeature()
    {
    }

    /** @BeforeScenario */
    public function before(BeforeScenarioScope $scope)
    {

        $this->installDir = $this->getContainer()->getParameter('install_dir');

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

    public function getExitCode()
    {
        return $this->exitCode;
    }

    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @Given a third party system has copied the file :arg1 into the import folder :arg2
     */
    public function aThirdPartySystemHasCopiedTheFileIntoTheImportFolder($arg1, $arg2)
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
        exec($this->appendInstallDir($arg1), $this->output, $this->exitCode);
        PHPUnit_Framework_Assert::assertNotEquals(1, $this->exitCode);
    }

    /**
     * @When the magento command :arg1 has been executed
     */
    public function theMagentoCommandHasBeenExecuted($arg1)
    {
        exec($this->prependInstallDir($arg1), $this->output, $this->exitCode);
        PHPUnit_Framework_Assert::assertNotEquals(1, $this->exitCode);
    }

    /**
     * @Given the magento index has been updated
     */
    public function theMagentoIndexHasBeenUpdated()
    {
        $this->theMagentoCommandHasBeenExecuted('bin/magento indexer:reindex');
    }

    /**
     * @Then a success message has to be rendered
     */
    public function assertSuccessMessage()
    {
        $this->assertMessage('/Successfully executed command \w+:\w+:?\w+? with serial \w+-\w+-\w+-\w+-\w+ in \d+:\d+:\d+ s/');
    }

    /**
     * @When the process has been finished
     * @When the import process has been finished
     * @When the deletion process has been finished
     * @When the replacement process has been finished
     * @When the attribute import process has been finished
     * @When the attribute deletion process has been finished
     * @When the attribute replacement process has been finished
     * @When the attribute set import process has been finished
     * @When the attribute set deletion process has been finished
     * @When the attribute set replacement process has been finished
     * @When the category import process has been finished
     * @When the category deletion process has been finished
     * @When the category replacement process has been finished
     * @When the product import process has been finished
     * @When the product deletion process has been finished
     * @When the product replacement process has been finished
     */
    public function assertExitCode()
    {
        PHPUnit_Framework_Assert::assertSame(0, $this->exitCode);
    }

    /**
     * @Then a message :arg1 has to be rendered
     */
    public function assertMessage($arg1)
    {
        PHPUnit_Framework_Assert::assertRegExp($arg1, array_pop($this->output));
    }

    protected function appendInstallDir($cmd)
    {
        return sprintf('%s --installation-dir=%s', $cmd, $this->installDir);
    }

    protected function prependInstallDir($cmd)
    {
        return sprintf('%s/%s', $this->installDir, $cmd);
    }
}
