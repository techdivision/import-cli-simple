<?php

/**
 * TechDivision\Import\Cli\Simple\Contexts\DockerEnvironment
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

/**
 * Defines a Docker specific context.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class DockerEnvironment
{

    /**
     * The Magento 2 installation directory.
     *
     * @var string
     */
    private $installDir = '/var/www/dist';

    /**
     * The Docker container name with the Magento 2 installation directory.
     *
     * @var string
     */
    private $containerName = 'magento';

    /**
     * The return value of the last executed command.
     *
     * @var integer
     */
    private $exitCode = 0;

    /**
     * The fully prepared command that has to be executed.
     *
     * @var string
     */
    private $cmd;

    /**
     * The output of the last executed command.
     *
     * @var array
     */
    private $output = array();

    /**
     * Initialize the Docker environment with the container name and the Magento
     * installation directory within the container.
     *
     * @param string|null $containerName The container name
     * @param string|null $installDir    The installation directory
     */
    public function __construct($containerName = null, $installDir = null)
    {

        // set the container name, if passed
        if ($containerName !== null) {
            $this->containerName = $containerName;
        }

        // set the installation directory, if passed
        if ($installDir !== null) {
            $this->installDir = $installDir;
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
     * Executes the passed Magento command and asserts that the exit code against the passed one.
     *
     * @return void
     */
    public function executeMagentoCommand($arg1, $exitCode = 0)
    {

        // initialize the command
        $this->cmd = $this->prependExecutionEnvironment($arg1);

        // executes the prepared command
        exec($this->cmd, $this->output, $this->exitCode);

        // render the command and the output if an error occurs
        if ($this->exitCode <> $exitCode) {
            echo sprintf('Executed command: %s with output %s', $this->cmd, print_r($this->output, true)) . PHP_EOL;
        }

        // assert that the exit code matches the passed one
        Assert::assertEquals($exitCode, $this->exitCode);
    }

    /**
     * Updates the Magento index.
     *
     * @return void
     */
    public function updateMagentoIndex(array $indexNames = array())
    {

        // prepare the command to run the Magento indexer
        $cmd = trim(sprintf('bin/magento indexer:reindex %s', implode(' ', $indexNames)));

        // execute the Magento command
        $this->executeMagentoCommand($cmd);
    }

    /**
     * Prepends the execution environment to the passed command.
     *
     * @param string $cmd The Magento command to be executed
     *
     * @return string The command with the prepended execution environment
     */
    protected function prependExecutionEnvironment($cmd)
    {
        return sprintf('docker exec %s php %s', $this->containerName, $this->prependInstallDir($cmd));
    }

    /**
     * Prepends the Magento installation directory to the passed command.
     *
     * @param string $cmd The Magento command to be executed
     *
     * @return string The command with the prepended installation directory
     */
    protected function prependInstallDir($cmd)
    {
        return sprintf('%s/%s', $this->installDir, $cmd);
    }
}
