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
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\DependencyInjection\TaggedContainerInterface;
use TechDivision\Import\Utils\LoggerKeys;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Utils\EntityTypeCodes;
use TechDivision\Import\ApplicationInterface;
use TechDivision\Import\ConfigurationInterface;
use TechDivision\Import\Cli\Utils\SynteticServiceKeys;
use TechDivision\Import\Exceptions\LineNotFoundException;
use TechDivision\Import\Exceptions\FileNotFoundException;
use TechDivision\Import\Exceptions\ImportAlreadyRunningException;
use TechDivision\Import\Configuration\PluginConfigurationInterface;
use TechDivision\Import\Services\ImportProcessorInterface;
use TechDivision\Import\Services\RegistryProcessorInterface;

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
     * The array with the default entity type code => import directory mappings.
     *
     * @var array
     */
    protected static $defaultDirectories = array(
        EntityTypeCodes::CATALOG_PRODUCT  => 'products',
        EntityTypeCodes::CATALOG_CATEGORY => 'categories'
    );

    /**
     * The array with the default entity type => configuration mapping.
     *
     * @var array
     */
    protected static $defaultConfigurations = array(
        EntityTypeCodes::CATALOG_PRODUCT  => 'techdivision/import-product',
        EntityTypeCodes::CATALOG_CATEGORY => 'techdivision/import-category'
    );

    /**
     * The Magento Edition specific default libraries.
     *
     * @var array
     */
    protected static $defaultLibraries = array(
        'ce' => array(
            'techdivision/import',
            'techdivision/import-category',
            'techdivision/import-product',
            'techdivision/import-product-bundle',
            'techdivision/import-product-link',
            'techdivision/import-product-media',
            'techdivision/import-product-variant'
        ),
        'ee' => array(
            'techdivision/import',
            'techdivision/import-ee',
            'techdivision/import-category',
            'techdivision/import-category-ee',
            'techdivision/import-product',
            'techdivision/import-product-ee',
            'techdivision/import-product-bundle',
            'techdivision/import-product-bundle-ee',
            'techdivision/import-product-link',
            'techdivision/import-product-link-ee',
            'techdivision/import-product-media',
            'techdivision/import-product-media-ee',
            'techdivision/import-product-variant',
            'techdivision/import-product-variant-ee'
        )
    );

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
     * The array with the system logger instances.
     *
     * @var array
     */
    protected $systemLoggers;

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
     * The DI container builder instance.
     *
     * @var \Symfony\Component\DependencyInjection\TaggedContainerInterface
     */
    protected $container;

    /**
     * The system configuration.
     *
     * @var \TechDivision\Import\ConfigurationInterface
     */
    protected $configuration;

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
     * The flag that stop's processing the operation.
     *
     * @var boolean
     */
    protected $stopped = false;

    /**
     * The filehandle for the PID file.
     *
     * @var resource
     */
    protected $fh;

    /**
     * The constructor to initialize the instance.
     *
     * @param \Symfony\Component\DependencyInjection\TaggedContainerInterface $container         The DI container instance
     * @param \TechDivision\Import\Services\RegistryProcessorInterface        $registryProcessor The registry processor instance
     * @param \TechDivision\Import\Services\ImportProcessorInterface          $importProcessor   The import processor instance
     * @param \TechDivision\Import\ConfigurationInterface                     $configuration     The system configuration
     * @param \Symfony\Component\Console\Output\OutputInterface               $output            An OutputInterface instance
     * @param array                                                           $systemLoggers     The array with the system logger instances
     */
    public function __construct(
        TaggedContainerInterface $container,
        RegistryProcessorInterface $registryProcessor,
        ImportProcessorInterface $importProcessor,
        ConfigurationInterface $configuration,
        OutputInterface $output,
        array $systemLoggers
    ) {

        // register the shutdown function
        register_shutdown_function(array($this, 'shutdown'));

        // initialize the instance with the passed values
        $this->setOutput($output);
        $this->setContainer($container);
        $this->setConfiguration($configuration);
        $this->setSystemLoggers($systemLoggers);
        $this->setImportProcessor($importProcessor);
        $this->setRegistryProcessor($registryProcessor);
    }

    /**
     * Set's the container instance.
     *
     * @param \Symfony\Component\DependencyInjection\TaggedContainerInterface $container The container instance
     *
     * @return void
     */
    public function setContainer(TaggedContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Return's the container instance.
     *
     * @return \Symfony\Component\DependencyInjection\TaggedContainerInterface The container instance
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Set's the output stream to write console information to.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output The output stream
     *
     * @return void
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * Return's the output stream to write console information to.
     *
     * @return \Symfony\Component\Console\Output\OutputInterface The output stream
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Set's the system configuration.
     *
     * @param \TechDivision\Import\ConfigurationInterface $configuration The system configuration
     *
     * @return void
     */
    public function setConfiguration(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
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
     * Set's the RegistryProcessor instance to handle the running threads.
     *
     * @param \TechDivision\Import\Services\RegistryProcessor $registryProcessor The registry processor instance
     *
     * @return void
     */
    public function setRegistryProcessor(RegistryProcessorInterface $registryProcessor)
    {
        $this->registryProcessor = $registryProcessor;
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
     * Set's the import processor instance.
     *
     * @param \TechDivision\Import\Services\ImportProcessorInterface $importProcessor The import processor instance
     *
     * @return void
     */
    public function setImportProcessor(ImportProcessorInterface $importProcessor)
    {
        $this->importProcessor = $importProcessor;
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
     * The array with the system loggers.
     *
     * @param array $systemLoggers The system logger instances
     *
     * @return void
     */
    public function setSystemLoggers(array $systemLoggers)
    {
        $this->systemLoggers = $systemLoggers;
    }

    /**
     * Return's the Magento Edition specific default libraries. Supported Magento Editions are CE or EE.
     *
     * @param string $magentoEdition The Magento Edition to return the libraries for
     *
     * @return array The Magento Edition specific default libraries
     * @throws \Exception Is thrown, if the passed Magento Edition is NOT supported
     */
    public static function getDefaultLibraries($magentoEdition)
    {

        // query whether or not, default libraries for the passed edition are available
        if (isset(self::$defaultLibraries[$edition = strtolower($magentoEdition)])) {
            return self::$defaultLibraries[$edition];
        }

        // throw an exception, if the passed edition is not supported
        throw new \Exception(
            sprintf(
                'Default libraries for Magento \'%s\' not supported (MUST be one of CE or EE)',
                $magentoEdition
            )
        );
    }

    /**
     * Return's the entity types specific default configuration file.
     *
     * @param string $entityType The entity type to return the configuration file for
     *
     * @return string The name of the library to query for the default configuration file
     * @throws \Exception Is thrown, if no default configuration for the passed entity type is available
     */
    public static function getDefaultConfiguration($entityTypeCode)
    {

        // query whether or not, a default configuration file for the passed entity type is available
        if (isset(self::$defaultConfigurations[$entityTypeCode])) {
            return self::$defaultConfigurations[$entityTypeCode];
        }

        // throw an exception, if the passed entity type is not supported
        throw new \Exception(
            sprintf(
                'Entity Type Code \'%s\' not supported (MUST be one of catalog_product or catalog_category)',
                $entityTypeCode
            )
        );
    }

    /**
     * Return's the entity types specific default import directory.
     *
     * @param string $entityType The entity type to return the default import directory for
     *
     * @return string The default default import directory
     * @throws \Exception Is thrown, if no default import directory for the passed entity type is available
     */
    public static function getDefaultDirectory($entityTypeCode)
    {

        // query whether or not, a default configuration file for the passed entity type is available
        if (isset(self::$defaultDirectories[$entityTypeCode])) {
            return self::$defaultDirectories[$entityTypeCode];
        }

        // throw an exception, if the passed entity type is not supported
        throw new \Exception(
            sprintf(
                'Entity Type Code \'%s\' not supported (MUST be one of catalog_product or catalog_category)',
                $entityTypeCode
            )
        );
    }

    /**
     * Return's the logger with the passed name, by default the system logger.
     *
     * @param string $name The name of the requested system logger
     *
     * @return \Psr\Log\LoggerInterface The logger instance
     * @throws \Exception Is thrown, if the requested logger is NOT available
     */
    public function getSystemLogger($name = LoggerKeys::SYSTEM)
    {

        // query whether or not, the requested logger is available
        if (isset($this->systemLoggers[$name])) {
            return $this->systemLoggers[$name];
        }

        // throw an exception if the requested logger is NOT available
        throw new \Exception(
            sprintf(
                'The requested logger \'%s\' is not available',
                $name
            )
        );
    }

    /**
     * Query whether or not the system logger with the passed name is available.
     *
     * @param string $name The name of the requested system logger
     *
     * @return boolean TRUE if the logger with the passed name exists, else FALSE
     */
    public function hasSystemLogger($name = LoggerKeys::SYSTEM)
    {
        return isset($this->systemLoggers[$name]);
    }

    /**
     * Return's the array with the system logger instances.
     *
     * @return array The logger instance
     */
    public function getSystemLoggers()
    {
        return $this->systemLoggers;
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
     * Persist the UUID of the actual import process to the PID file.
     *
     * @return void
     * @throws \Exception Is thrown, if the PID can not be locked or the PID can not be added
     * @throws \TechDivision\Import\Exceptions\ImportAlreadyRunningException Is thrown, if a import process is already running
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
        $this->fh = fopen($filename = $this->getPidFilename(), 'a+');

        // try to lock the PID file exclusive
        if (!flock($this->fh, LOCK_EX|LOCK_NB)) {
            throw new ImportAlreadyRunningException(sprintf('PID file %s is already in use', $filename));
        }

        // append the PID to the PID file
        if (fwrite($this->fh, $this->pid . PHP_EOL) === false) {
            throw new \Exception(sprintf('Can\'t write PID %s to PID file %s', $this->pid, $filename));
        }
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
            if ($this->pid === $this->getSerial() && is_resource($this->fh)) {
                // remove the PID from the file
                $this->removeLineFromFile($this->pid, $this->fh);

                // finally unlock/close the PID file
                flock($this->fh, LOCK_UN);
                fclose($this->fh);

                // if the PID file is empty, delete the file
                if (filesize($filename = $this->getPidFilename()) === 0) {
                    unlink($filename);
                }
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
     * @param string   $line The line to be removed
     * @param resource $fh   The file handle of the file the line has to be removed
     *
     * @return void
     * @throws \Exception Is thrown, if the file doesn't exists, the line is not found or can not be removed
     */
    public function removeLineFromFile($line, $fh)
    {

        // initialize the array for the PIDs found in the PID file
        $lines = array();

        // initialize the flag if the line has been found
        $found = false;

        // rewind the file pointer
        rewind($fh);

        // read the lines with the PIDs from the PID file
        while (($buffer = fgets($fh, 4096)) !== false) {
            // remove the new line
            $buffer = trim($buffer);
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
            throw new LineNotFoundException(sprintf('Line %s can not be found', $line));
        }

        // empty the file and rewind the file pointer
        ftruncate($fh, 0);
        rewind($fh);

        // append the existing lines to the file
        foreach ($lines as $ln) {
            if (fwrite($fh, $ln . PHP_EOL) === false) {
                throw new \Exception(sprintf('Can\'t write %s to file', $ln));
            }
        }
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
                // query whether or not the operation has been stopped
                if ($this->isStopped()) {
                    break;
                }
                // process the plugin if not
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

        } catch (ImportAlreadyRunningException $iare) {
            // tear down
            $this->tearDown();

            // rollback the transaction
            $this->getImportProcessor()->getConnection()->rollBack();

            // finally, if a PID has been set (because CSV files has been found),
            // remove it from the PID file to unlock the importer
            $this->unlock();

            // track the time needed for the import in seconds
            $endTime = microtime(true) - $startTime;

            // log a warning, because another import process is already running
            $this->getSystemLogger()->warning($iare->__toString());

            // log a message that import has been finished
            $this->getSystemLogger()->info(
                sprintf(
                    'Can\'t finish import with serial because another import process is running %s in %f s',
                    $this->getSerial(),
                    $endTime
                )
            );

            // re-throw the exception
            throw $iare;

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
            foreach ($this->systemLoggers as $systemLogger) {
                $systemLogger->error($e->__toString());
            }

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
     * Stop processing the operation.
     *
     * @param string $reason The reason why the operation has been stopped
     *
     * @return void
     */
    public function stop($reason)
    {

        // log a message that the operation has been stopped
        $this->log($reason, LogLevel::INFO);

        // stop processing the plugins by setting the flag to TRUE
        $this->stopped = true;
    }

    /**
     * Return's TRUE if the operation has been stopped, else FALSE.
     *
     * @return boolean TRUE if the process has been stopped, else FALSE
     */
    public function isStopped()
    {
        return $this->stopped;
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
        $this->getContainer()->set(sprintf('configuration.%s', $id = $pluginConfiguration->getId()), $pluginConfiguration);
        return $this->getContainer()->get($id);
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

        // write the TechDivision ANSI art icon to the console
        $this->log($this->ansiArt);

        // log the debug information, if debug mode is enabled
        if ($this->getConfiguration()->isDebugMode()) {
            // load the application from the DI container
            /** @var TechDivision\Import\Cli\Application $application */
            $application = $this->getContainer()->get(SynteticServiceKeys::APPLICATION);
            // log the system's PHP configuration
            $this->log(sprintf('PHP version: %s', phpversion()), LogLevel::DEBUG);
            $this->log(sprintf('App version: %s', $application->getVersion()), LogLevel::DEBUG);
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
            RegistryKeys::SOURCE_DIRECTORY => $this->getConfiguration()->getSourceDir(),
            RegistryKeys::MISSING_OPTION_VALUES => array()
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
