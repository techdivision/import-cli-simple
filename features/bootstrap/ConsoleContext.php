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
     * @Then all the data in the file :arg1 has been imported
     */
    public function allTheDataInTheFileHasBeenImported2($arg1)
    {
        throw new PendingException();
    }

    public function getExitCode()
    {
        return $this->exitCode;
    }

    public function getOutput()
    {
        return $this->output;
    }
}
