<?php

/**
 * TechDivision\Import\Cli\Simple\Contexts\ConsoleContext
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
use TechDivision\Import\Adapter\PhpFilesystemAdapter;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Behat\Symfony2Extension\Context\KernelAwareContext;

/**
 * Defines console features from the specific context.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class ConsoleContext implements Context, KernelAwareContext
{

    /**
     * @var \Behat\Symfony2Extension\Context\KernelDictionary
     */
    use KernelDictionary;

    /**
     * The Magento 2 installation directory.
     *
     * @var string
     */
    private $sourceDir;

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

    /**
     *
     * @var \TechDivision\Import\Cli\Simple\Contexts\DockerEnvironment
     */
    private $env;

    /** @BeforeScenario */
    public function before(BeforeScenarioScope $scope)
    {

        /** @var \TechDivision\Import\Cli\Simple\Contexts\DockerEnvironment */
        $this->env = $this->getContainer()->get('environment.docker');
        $this->sourceDir = $this->getContainer()->getParameter('source.dir');

        // create a new filesystem
        $filesystemAdapter = new PhpFilesystemAdapter();

        if (is_dir($this->sourceDir) === false) {
            mkdir($this->sourceDir, 0755, true);
            echo "Successfully created directory: $this->sourceDir" . PHP_EOL;
        }

        foreach (glob(sprintf('%s/*', $this->sourceDir)) as $file) {
            if (is_file($file)) {
                $filesystemAdapter->delete($file);
            } else {
                $filesystemAdapter->removeDir($file, true);
            }
        }
    }

    /**
     * Return's the exit code of the las executed command.
     *
     * @return integer The exit code
     */
    public function getExitCode()
    {
        return $this->exitCode;
    }

    /**
     * Return's the output of the last executed command.
     *
     * @return array The output of the last executed command
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @Given a third party system has copied the file :arg1 into the import folder
     */
    public function aThirdPartySystemHasCopiedTheFileIntoTheImportFolder($arg1)
    {

        if (copy($arg1, $dest = sprintf('%s/%s', $this->sourceDir, basename($arg1)))) {
            return;
        }

        throw new \Exception(sprintf('Can\'t copy file %s to %s', $arg1, $dest));
    }

    /**
     * @When the simple command :arg1 has been executed
     */
    public function theSimpleCommandHasBeenExecuted($arg1)
    {
        exec($this->appendGenericConfig($arg1), $this->output, $this->exitCode);
        Assert::assertNotEquals(1, $this->exitCode);
    }

    /**
     * @When the command :arg1 has been executed
     */
    public function theCommandHasBeenExecuted($arg1)
    {
        $this->theSimpleCommandHasBeenExecuted($this->appendDbConnection($this->appendGenericConfig($arg1)));
    }

    /**
     * @When the magento command :arg1 has been executed
     */
    public function theMagentoCommandHasBeenExecuted($arg1)
    {
        $this->env->executeMagentoCommand($arg1);
    }

    /**
     * @Given the magento index has been updated
     */
    public function theMagentoIndexHasBeenUpdated()
    {
        $this->env->updateMagentoIndex();
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
        Assert::assertSame(0, $this->exitCode);
    }

    /**
     * @Then a message :arg1 has to be rendered
     */
    public function assertMessage($arg1)
    {
        Assert::assertRegExp($arg1, array_pop($this->output));
    }

    protected function appendDbConnection($cmd)
    {
        return sprintf('%s --db-username=magento --db-password=magento --db-pdo-dsn="mysql:host=127.0.1.1;port=9306;dbname=magento;charset=utf8"', $cmd);
    }

    protected function appendGenericConfig($cmd)
    {
        return sprintf('%s --custom-configuration-dir=tests/acceptance/app/etc/configuration --magento-edition=ce --magento-version=2.3.3 --source-dir=%s', $cmd, $this->sourceDir);
    }
}
