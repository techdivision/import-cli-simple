<?php

/**
 * TechDivision\Import\Cli\Simple
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
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Cli;

use Rhumsaa\Uuid\Uuid;
use Monolog\Logger;
use Psr\Log\LogLevel;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\FormatterHelper;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\ApplicationInterface;
use TechDivision\Import\ConfigurationInterface;
use TechDivision\Import\Configuration\PluginConfigurationInterface;
use TechDivision\Import\Services\ImportProcessorInterface;
use TechDivision\Import\Services\RegistryProcessorInterface;
use TechDivision\Import\Cli\Exceptions\LineNotFoundException;
use TechDivision\Import\Cli\Exceptions\FileNotFoundException;

/**
 * The M2IF - Console Tool implementation.
 *
 * This is a example console tool implementation that should give developers an impression
 * on how the M2IF could be used to implement their own Magento 2 importer.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class Simple implements ApplicationInterface
{

    /**
     * The default style to write messages to the symfony console.
     *
     * @var string
     */
    const DEFAULT_STYLE = 'info';

    /**
     * The TechDivision company name as ANSI art.
     *
     * @var string
     */
    protected $ansiArt = ' _______        _     _____  _       _     _
|__   __|      | |   |  __ \(_)     (_)   (_)
   | | ___  ___| |__ | |  | |___   ___ ___ _  ___  _ __
   | |/ _ \/ __| \'_ \| |  | | \ \ / / / __| |/ _ \| \'_ \
   | |  __/ (__| | | | |__| | |\ V /| \__ \ | (_) | | | |
   |_|\___|\___|_| |_|_____/|_| \_/ |_|___/_|\___/|_| |_|
';

    /**
     * The log level => console style mapping.
     *
     * @var array
     */
    protected $logLevelStyleMapping = array(
        LogLevel::INFO      => 'info',
        LogLevel::DEBUG     => 'comment',
        LogLevel::ERROR     => 'error',
        LogLevel::ALERT     => 'error',
        LogLevel::CRITICAL  => 'error',
        LogLevel::EMERGENCY => 'error',
        LogLevel::WARNING   => 'error',
        LogLevel::NOTICE    => 'info'
    );

    /**
     * The PID for the running processes.
     *
     * @var array
     */
    protected $pid;

    /**
     * The actions unique serial.
     *
     * @var string
     */
    protected $serial;

    /**
     * The system logger implementation.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $systemLogger;

    /**
     * The RegistryProcessor instance to handle running threads.
     *
     * @var \TechDivision\Import\Services\RegistryProcessorInterface
     */
    protected $registryProcessor;

    /**
     * The processor to read/write the necessary import data.
     *
     * @var \TechDivision\Import\Services\ImportProcessorInterface
     */
    protected $importProcessor;

    /**
     * The system configuration.
     *
     * @var \TechDivision\Import\ConfigurationInterface
     */
    protected $configuration;

    /**
     * The input stream to read console information from.
     *
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    protected $input;

    /**
     * The output stream to write console information to.
     *
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * The plugins to be processed.
     *
     * @var array
     */
    protected $plugins = array();

    /**
     * The constructor to initialize the instance.
     *
     * @param \Psr\Log\LoggerInterface                                 $systemLogger      The system logger
     * @param \TechDivision\Import\Services\RegistryProcessorInterface $registryProcessor The registry processor instance
     * @param \TechDivision\Import\Services\ImportProcessorInterface   $importProcessor   The import processor instance
     * @param \TechDivision\Import\ConfigurationInterface              $configuration     The system configuration
     * @param \Symfony\Component\Console\Input\InputInterface          $input             An InputInterface instance
     * @param \Symfony\Component\Console\Output\OutputInterface        $output            An OutputInterface instance
     */
    public function __construct(
        LoggerInterface $systemLogger,
        RegistryProcessorInterface $registryProcessor,
        ImportProcessorInterface $importProcessor,
        ConfigurationInterface $configuration,
        InputInterface $input,
        OutputInterface $output
    ) {

        // register the shutdown function
        register_shutdown_function(array($this, 'shutdown'));

        // initialize the values
        $this->systemLogger = $systemLogger;
        $this->registryProcessor = $registryProcessor;
        $this->importProcessor = $importProcessor;
        $this->configuration = $configuration;
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * The shutdown handler to catch fatal errors.
     *
     * This method is need to make sure, that an existing PID file will be removed
     * if a fatal error has been triggered.
     *
     * @return void
     */
    public function shutdown()
    {

        // check if there was a fatal error caused shutdown
        if ($lastError = error_get_last()) {
            // initialize error type and message
            $type = 0;
            $message = '';
            // extract the last error values
            extract($lastError);
            // query whether we've a fatal/user error
            if ($type === E_ERROR || $type === E_USER_ERROR) {
                // clean-up the PID file
                $this->unlock();
                // log the fatal error message
                $this->log($message, LogLevel::ERROR);
            }
        }
    }

    /**
     * Return's the system logger.
     *
     * @return \Psr\Log\LoggerInterface The system logger instance
     */
    public function getSystemLogger()
    {
        return $this->systemLogger;
    }

    /**
     * Return's the RegistryProcessor instance to handle the running threads.
     *
     * @return \TechDivision\Import\Services\RegistryProcessor The registry processor instance
     */
    public function getRegistryProcessor()
    {
        return $this->registryProcessor;
    }

    /**
     * Return's the import processor instance.
     *
     * @return \TechDivision\Import\Services\ImportProcessorInterface The import processor instance
     */
    public function getImportProcessor()
    {
        return $this->importProcessor;
    }

    /**
     * Return's the system configuration.
     *
     * @return \TechDivision\Import\ConfigurationInterface The system configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Return's the input stream to read console information from.
     *
     * @return \Symfony\Component\Console\Input\InputInterface An IutputInterface instance
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * Return's the output stream to write console information to.
     *
     * @return \Symfony\Component\Console\Output\OutputInterface An OutputInterface instance
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Return's the unique serial for this import process.
     *
     * @return string The unique serial
     */
    public function getSerial()
    {
        return $this->serial;
    }

    /**
     * Persist the UUID of the actual import process to the PID file.
     *
     * @return void
     * @throws \Exception Is thrown, if the PID can not be added
     */
    public function lock()
    {

        // query whether or not, the PID has already been set
        if ($this->pid === $this->getSerial()) {
            return;
        }

        // if not, initialize the PID
        $this->pid = $this->getSerial();

        // open the PID file
        $fh = fopen($pidFilename = $this->getPidFilename(), 'a');

        // append the PID to the PID file
        if (fwrite($fh, $this->pid . PHP_EOL) === false) {
            throw new \Exception(sprintf('Can\'t write PID %s to PID file %s', $this->pid, $pidFilename));
        }

        // close the file handle
        fclose($fh);
    }

    /**
     * Remove's the UUID of the actual import process from the PID file.
     *
     * @return void
     * @throws \Exception Is thrown, if the PID can not be removed
     */
    public function unlock()
    {
        try {
            // remove the PID from the PID file if set
            if ($this->pid === $this->getSerial()) {
                $this->removeLineFromFile($this->pid, $this->getPidFilename());
            }

        } catch (FileNotFoundException $fnfe) {
            $this->getSystemLogger()->notice(sprintf('PID file %s doesn\'t exist', $this->getPidFilename()));
        } catch (LineNotFoundException $lnfe) {
            $this->getSystemLogger()->notice(sprintf('PID %s is can not be found in PID file %s', $this->pid, $this->getPidFilename()));
        } catch (\Exception $e) {
            throw new \Exception(sprintf('Can\'t remove PID %s from PID file %s', $this->pid, $this->getPidFilename()), null, $e);
        }
    }

    /**
     * Remove's the passed line from the file with the passed name.
     *
     * @param string $line     The line to be removed
     * @param string $filename The name of the file the line has to be removed
     *
     * @return void
     * @throws \Exception Is thrown, if the file doesn't exists, the line is not found or can not be removed
     */
    public function removeLineFromFile($line, $filename)
    {

        // query whether or not the filename
        if (!file_exists($filename)) {
            throw new FileNotFoundException(sprintf('File %s doesn\' exists', $filename));
        }

        // open the PID file
        $fh = fopen($filename, 'r+');

        // initialize the array for the PIDs found in the PID file
        $lines = array();

        // initialize the flag if the line has been found
        $found = false;

        // read the lines with the PIDs from the PID file
        while (($buffer = fgets($fh, 4096)) !== false) {
            // remove the new line
            $buffer = trim($buffer, PHP_EOL);
            // if the line is the one to be removed, ignore the line
            if ($line === $buffer) {
                $found = true;
                continue;
            }

            // add the found PID to the array
            $lines[] = $buffer;
        }

        // query whether or not, we found the line
        if (!$found) {
            throw new LineNotFoundException(sprintf('Line %s can not be found in file %s', $line, $filename));
        }

        // if there are NO more lines, delete the file
        if (sizeof($lines) === 0) {
            fclose($fh);
            unlink($filename);
            return;
        }

        // empty the file and rewind the file pointer
        ftruncate($fh, 0);
        rewind($fh);

        // append the existing lines to the file
        foreach ($lines as $ln) {
            if (fwrite($fh, $ln . PHP_EOL) === false) {
                throw new \Exception(sprintf('Can\'t write %s to file %s', $ln, $filename));
            }
        }

        // finally close the file
        fclose($fh);
    }

    /**
     * Process the given operation.
     *
     * @return void
     * @throws \Exception Is thrown if the operation can't be finished successfully
     */
    public function process()
    {

        try {
            // track the start time
            $startTime = microtime(true);

            // start the transaction
            $this->getImportProcessor()->getConnection()->beginTransaction();

            // prepare the global data for the import process
            $this->setUp();

            // process the plugins defined in the configuration
            foreach ($this->getConfiguration()->getPlugins() as $pluginConfiguration) {
                $this->pluginFactory($pluginConfiguration)->process();
            }

            // tear down the  instance
            $this->tearDown();

            // commit the transaction
            $this->getImportProcessor()->getConnection()->commit();

            // track the time needed for the import in seconds
            $endTime = microtime(true) - $startTime;

            // log a message that import has been finished
            $this->log(
                sprintf(
                    'Successfully finished import with serial %s in %f s',
                    $this->getSerial(),
                    $endTime
                ),
                LogLevel::INFO
            );

        } catch (\Exception $e) {
            // tear down
            $this->tearDown();

            // rollback the transaction
            $this->getImportProcessor()->getConnection()->rollBack();

            // finally, if a PID has been set (because CSV files has been found),
            // remove it from the PID file to unlock the importer
            $this->unlock();

            // track the time needed for the import in seconds
            $endTime = microtime(true) - $startTime;

            // log a message that the file import failed
            $this->getSystemLogger()->error($e->__toString());

            // log a message that import has been finished
            $this->getSystemLogger()->info(
                sprintf(
                    'Can\'t finish import with serial %s in %f s',
                    $this->getSerial(),
                    $endTime
                )
            );

            // re-throw the exception
            throw $e;
        }
    }

    /**
     * Factory method to create new plugin instances.
     *
     * @param \TechDivision\Import\Configuration\PluginConfigurationInterface $pluginConfiguration The plugin configuration instance
     *
     * @return object The plugin instance
     */
    protected function pluginFactory(PluginConfigurationInterface $pluginConfiguration)
    {

        // load the plugin class name
        $className = $pluginConfiguration->getClassName();

        // initialize and return the plugin instance
        return new $className($this, $pluginConfiguration);
    }

    /**
     * Lifecycle callback that will be inovked before the
     * import process has been started.
     *
     * @return void
     */
    protected function setUp()
    {

        // generate the serial for the new job
        $this->serial = Uuid::uuid4()->__toString();

        // query whether or not an import is running AND an existing PID has to be ignored
        if (file_exists($pidFilename = $this->getPidFilename()) && !$this->getConfiguration()->isIgnorePid()) {
            throw new \Exception(sprintf('At least one import process is already running (check PID: %s)', $pidFilename));
        } elseif (file_exists($pidFilename = $this->getPidFilename()) && $this->getConfiguration()->isIgnorePid()) {
            $this->log(sprintf('At least one import process is already running (PID: %s)', $pidFilename), LogLevel::WARNING);
        }

        // write the TechDivision ANSI art icon to the console
        $this->log($this->ansiArt);

        // log the debug information, if debug mode is enabled
        if ($this->getConfiguration()->isDebugMode()) {
            // log the system's PHP configuration
            $this->log(sprintf('PHP version: %s', phpversion()), LogLevel::DEBUG);
            $this->log('-------------------- Loaded Extensions -----------------------', LogLevel::DEBUG);
            $this->log(implode(', ', $loadedExtensions = get_loaded_extensions()), LogLevel::DEBUG);
            $this->log('--------------------------------------------------------------', LogLevel::DEBUG);

            // write a warning for low performance, if XDebug extension is activated
            if (in_array('xdebug', $loadedExtensions)) {
                $this->log('Low performance exptected, as result of enabled XDebug extension!', LogLevel::WARNING);
            }
        }

        // log a message that import has been started
        $this->log(
            sprintf(
                'Now start import with serial %s (operation: %s)',
                $this->getSerial(),
                $this->getConfiguration()->getOperationName()
            ),
            LogLevel::INFO
        );

        // initialize the status
        $status = array(
            RegistryKeys::STATUS => 1,
            RegistryKeys::BUNCHES => 0,
            RegistryKeys::SOURCE_DIRECTORY => $this->getConfiguration()->getSourceDir()
        );

        // append it to the registry
        $this->getRegistryProcessor()->setAttribute($this->getSerial(), $status);
    }

    /**
     * Lifecycle callback that will be inovked after the
     * import process has been finished.
     *
     * @return void
     */
    protected function tearDown()
    {
        $this->getRegistryProcessor()->removeAttribute($this->getSerial());
    }

    /**
     * Simple method that writes the passed method the the console and the
     * system logger, if configured and a log level has been passed.
     *
     * @param string $msg      The message to log
     * @param string $logLevel The log level to use
     *
     * @return void
     */
    protected function log($msg, $logLevel = null)
    {

        // initialize the formatter helper
        $helper = new FormatterHelper();

        // map the log level to the console style
        $style = $this->mapLogLevelToStyle($logLevel);

        // format the message, according to the passed log level and write it to the console
        $this->getOutput()->writeln($logLevel ? $helper->formatBlock($msg, $style) : $msg);

        // log the message if a log level has been passed
        if ($logLevel && $systemLogger = $this->getSystemLogger()) {
            $systemLogger->log($logLevel, $msg);
        }
    }

    /**
     * Map's the passed log level to a valid symfony console style.
     *
     * @param string $logLevel The log level to map
     *
     * @return string The apropriate symfony console style
     */
    protected function mapLogLevelToStyle($logLevel)
    {

        // query whether or not the log level is mapped
        if (isset($this->logLevelStyleMapping[$logLevel])) {
            return $this->logLevelStyleMapping[$logLevel];
        }

        // return the default style => info
        return Simple::DEFAULT_STYLE;
    }

    /**
     * Return's the PID filename to use.
     *
     * @return string The PID filename
     */
    protected function getPidFilename()
    {
        return $this->getConfiguration()->getPidFilename();
    }
}
