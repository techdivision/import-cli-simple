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

    private $dbHost = '';
    private $dbPort = '';
    private $dbUser = '';
    private $dbName = '';
    private $dbPassword = '';
    private $magentoEdition = '';
    private $magentoVersion = '';

    /**
     * The environment used to execute the Magento commands.
     *
     * @var \TechDivision\Import\Cli\Simple\Contexts\DockerEnvironment
     */
    private $env;

    /** @BeforeScenario */
    public function before(BeforeScenarioScope $scope)
    {

        // initialize the environment and the source directory
        /** @var \TechDivision\Import\Cli\Simple\Contexts\DockerEnvironment */
        $this->env = $this->getContainer()->get('environment.docker');
        $this->sourceDir = $this->getContainer()->getParameter('source.dir');
        $this->dbHost = $this->getContainer()->getParameter('db.host');
        $this->dbPort = $this->getContainer()->getParameter('db.port');
        $this->dbUser = $this->getContainer()->getParameter('db.user');
        $this->dbName = $this->getContainer()->getParameter('db.name');
        $this->dbPassword = $this->getContainer()->getParameter('db.password');
        $this->magentoEdition = $this->getContainer()->getParameter('magento.install_edition');
        $this->magentoVersion = $this->getContainer()->getParameter('magento.install_version');

        // create a new filesystem
        $filesystemAdapter = new PhpFilesystemAdapter();

        // query whether or not the source directory has to be created
        if (is_dir($this->sourceDir) === false) {
            mkdir($this->sourceDir, 0755, true);
        }

        // clean-up the source directory, if not empty
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

        // copy the passed file to the source directory
        if (copy($arg1, $dest = sprintf('%s/%s', $this->sourceDir, basename($arg1)))) {
            return;
        }

        // throw an exception if it is NOT possible to copy the file
        throw new \Exception(sprintf('Can\'t copy file %s to %s', $arg1, $dest));
    }

    /**
     * @When the simple command :arg1 has been executed
     */
    public function theSimpleCommandHasBeenExecuted($arg1)
    {

        // execute the simple command and assert that the exit code is NOT one
        exec($this->appendGenericConfig($arg1), $this->output, $this->exitCode);
        if ($this->exitCode <> 0) {
            var_export($this->output);
        }
        $this->assertExitCode();
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
     * @Given the magento customer grid index has been updated
     *
     * @return void
     */
    public function theMagentoCustomerGridIndexHasBeenUpdated()
    {
        $this->env->updateMagentoIndex(array('customer_grid'));
    }

    /**
     * @Then a success message has to be rendered
     */
    public function assertSuccessMessage()
    {
        $this->assertMessage('/Successfully executed command \w+:\w+:?\w+?:?\w+? with serial \w+-\w+-\w+-\w+-\w+ in \d+:\d+:\d+ s/');
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
     * @When the tier price import process has been finished
     * @When the tier price deletion process has been finished
     * @When the tier price replacement process has been finished
     * @When the customer import process has been finished
     * @When the customer deletion process has been finished
     * @When the customer replacement process has been finished
     * @When the customer address import process has been finished
     * @When the customer address deletion process has been finished
     * @When the customer address replacement process has been finished
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

    /**
     * Append the database connection.
     *
     * @param string $cmd The command to execute
     *
     * @return string The command with the appended DB connection string
     */
    protected function appendDbConnection($cmd)
    {
        return sprintf(
            '%s --db-username=%s --db-password=%s --db-pdo-dsn="mysql:host=%s;port=%d;dbname=%s;charset=utf8"',
            $cmd,
            $this->dbUser,
            $this->dbPassword,
            $this->dbHost,
            $this->dbPort,
            $this->dbName
        );
    }

    /**
     * Append the generic configuration.
     *
     * @param string $cmd The command to execute
     *
     * @return string The command with the appended generic configuration
     */
    protected function appendGenericConfig($cmd)
    {
        return sprintf(
            '%s --custom-configuration-dir=tests/acceptance/app/etc/configuration --magento-edition=%s --magento-version=%s --source-dir=%s',
            $cmd,
            $this->magentoEdition,
            $this->magentoVersion,
            $this->sourceDir
        );
    }
}
