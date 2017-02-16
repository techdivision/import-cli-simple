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
use TechDivision\Import\Utils\BunchKeys;
use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\ConfigurationInterface;
use TechDivision\Import\Subjects\SubjectInterface;
use TechDivision\Import\Services\ImportProcessorInterface;
use TechDivision\Import\Services\RegistryProcessorInterface;
use TechDivision\Import\Subjects\ExportableSubjectInterface;
use TechDivision\Import\Cli\Exceptions\LineNotFoundException;

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
class Simple
{

    /**
     * The default style to write messages to the symfony console.
     *
     * @var string
     */
    const DEFAULT_STYLE = 'info';

    /**
     * The PID filename to use.
     *
     * @var string
     */
    const PID_FILENAME = 'importer.pid';

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
     * The matches for the last processed CSV filename.
     *
     * @var array
     */
    protected $matches = array();

    /**
     * The number of imported bunches.
     *
     * @var integer
     */
    protected $bunches = 0;

    /**
     * The PID for the running processes.
     *
     * @var array
     */
    protected $pid = null;

    /**
     * The CSV files that has to be imported.
     *
     * @var array
     */
    protected $files = array();

    /**
     * Set's the unique serial for this import process.
     *
     * @param string $serial The unique serial
     *
     * @return void
     */
    public function setSerial($serial)
    {
        $this->serial = $serial;
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
     * Set's the system logger.
     *
     * @param \Psr\Log\LoggerInterface $systemLogger The system logger
     *
     * @return void
     */
    public function setSystemLogger(LoggerInterface $systemLogger)
    {
        $this->systemLogger = $systemLogger;
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
     * Sets's the RegistryProcessor instance to handle the running threads.
     *
     * @param \TechDivision\Import\Services\RegistryProcessorInterface $registryProcessor The registry processor instance
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
     * Set's the input stream to read console information from.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input An IutputInterface instance
     *
     * @return void
     */
    public function setInput(InputInterface $input)
    {
        $this->input = $input;
    }

    /**
     * Return's the input stream to read console information from.
     *
     * @return \Symfony\Component\Console\Input\InputInterface An IutputInterface instance
     */
    protected function getInput()
    {
        return $this->input;
    }

    /**
     * Set's the output stream to write console information to.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output An OutputInterface instance
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
     * @return \Symfony\Component\Console\Output\OutputInterface An OutputInterface instance
     */
    protected function getOutput()
    {
        return $this->output;
    }

    /**
     * Return's the source directory that has to be watched for new files.
     *
     * @return string The source directory
     */
    protected function getSourceDir()
    {
        return $this->getConfiguration()->getSourceDir();
    }

    /**
     * Parse the temporary upload directory for new files to be imported.
     *
     * @return void
     * @throws \Exception Is thrown if the import can't be finished successfully
     */
    public function import()
    {

        // track the start time
        $startTime = microtime(true);

        try {
            // generate the serial for the new job
            $this->setSerial(Uuid::uuid4()->__toString());

            // prepare the global data for the import process
            $this->start();
            $this->setUp();
            $this->processSubjects();
            $this->archive();
            $this->tearDown();
            $this->finish();

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
            $this->finish();

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
     * Clears the PID file from a previous import process, if it has not
     * been cleaned up properly.
     *
     * @return void
     */
    public function clearPid()
    {

        // track the start time
        $startTime = microtime(true);

        try {
            // query whether or not a PID file exists and delete it
            if (file_exists($pidFilename = $this->getPidFilename())) {
                if (!unlink($pidFilename)) {
                    throw new \Exception(sprintf('Can\'t delete PID file %s', $pidFilename));
                }
            }

            // track the time needed for the import in seconds
            $endTime = microtime(true) - $startTime;

            // log a message that PID file has successfully been removed
            $this->log(sprintf('Successfully removed PID file %s in %f s', $pidFilename, $endTime), LogLevel::INFO);

        } catch (\Exception $e) {
            // track the time needed for the import in seconds
            $endTime = microtime(true) - $startTime;

            // log a message that the file import failed
            $this->getSystemLogger()->error($e->__toString());

            // log a message that PID file has not been removed
            $this->getSystemLogger()->error(sprintf('Can\'t remove PID file %s in %f s', $this->getPidFilename(), $endTime));

            // re-throw the exception
            throw $e;
        }
    }

    /**
     * This method start's the import process by initializing
     * the status and appends it to the registry.
     *
     * @return void
     * @throws \Exception Is thrown, an import process is already running
     */
    protected function start()
    {

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
            RegistryKeys::SOURCE_DIRECTORY => $this->getConfiguration()->getSourceDir()
        );

        // initialize the status information for the subjects */
        /** @var \TechDivision\Import\Configuration\SubjectInterface $subject */
        foreach ($this->getConfiguration()->getSubjects() as $subject) {
            $status[$subject->getPrefix()] = array();
        }

        // append it to the registry
        $this->getRegistryProcessor()->setAttribute($this->getSerial(), $status);
    }

    /**
     * Prepares the global data for the import process.
     *
     * @return void
     */
    protected function setUp()
    {

        // load the registry
        $importProcessor = $this->getImportProcessor();
        $registryProcessor = $this->getRegistryProcessor();

        // initialize the array for the global data
        $globalData = array();

        // initialize the global data
        $globalData[RegistryKeys::STORES] = $importProcessor->getStores();
        $globalData[RegistryKeys::LINK_TYPES] = $importProcessor->getLinkTypes();
        $globalData[RegistryKeys::TAX_CLASSES] = $importProcessor->getTaxClasses();
        $globalData[RegistryKeys::DEFAULT_STORE] = $importProcessor->getDefaultStore();
        $globalData[RegistryKeys::STORE_WEBSITES] = $importProcessor->getStoreWebsites();
        $globalData[RegistryKeys::LINK_ATTRIBUTES] = $importProcessor->getLinkAttributes();
        $globalData[RegistryKeys::ROOT_CATEGORIES] = $importProcessor->getRootCategories();
        $globalData[RegistryKeys::CORE_CONFIG_DATA] = $importProcessor->getCoreConfigData();
        $globalData[RegistryKeys::ATTRIBUTE_SETS] = $eavAttributeSets = $importProcessor->getEavAttributeSetsByEntityTypeId(4);

        // prepare the categories
        $categories = array();
        foreach ($importProcessor->getCategories() as $category) {
            // expload the entity IDs from the category path
            $entityIds = explode('/', $category[MemberNames::PATH]);

            // cut-off the root category
            array_shift($entityIds);

            // continue with the next category if no entity IDs are available
            if (sizeof($entityIds) === 0) {
                continue;
            }

            // initialize the array for the path elements
            $path = array();
            foreach ($importProcessor->getCategoryVarcharsByEntityIds($entityIds) as $cat) {
                $path[] = $cat[MemberNames::VALUE];
            }

            // append the catogory with the string path as key
            $categories[implode('/', $path)] = $category;
        }

        // initialize the array with the categories
        $globalData[RegistryKeys::CATEGORIES] = $categories;

        // prepare an array with the EAV attributes grouped by their attribute set name as keys
        $eavAttributes = array();
        foreach (array_keys($eavAttributeSets) as $eavAttributeSetName) {
            $eavAttributes[$eavAttributeSetName] = $importProcessor->getEavAttributesByEntityTypeIdAndAttributeSetName(4, $eavAttributeSetName);
        }

        // initialize the array with the EAV attributes
        $globalData[RegistryKeys::EAV_ATTRIBUTES] = $eavAttributes;

        // add the status with the global data
        $registryProcessor->mergeAttributesRecursive(
            $this->getSerial(),
            array(RegistryKeys::GLOBAL_DATA => $globalData)
        );

        // log a message that the global data has been prepared
        $this->log(
            sprintf(
                'Successfully prepared global data for import with serial %s',
                $this->getSerial()
            ),
            LogLevel::INFO
        );
    }

    /**
     * Process all the subjects defined in the system configuration.
     *
     * @return void
     * @throws \Exception Is thrown, if one of the subjects can't be processed
     */
    protected function processSubjects()
    {

        try {
            // load system logger and registry
            $importProcessor = $this->getImportProcessor();

            // load the subjects
            $subjects = $this->getConfiguration()->getSubjects();

            // start the transaction
            $importProcessor->getConnection()->beginTransaction();

            // process all the subjects found in the system configuration
            foreach ($subjects as $subject) {
                $this->processSubject($subject);
            }

            // commit the transaction
            $importProcessor->getConnection()->commit();

        } catch (\Exception $e) {
            // rollback the transaction
            $importProcessor->getConnection()->rollBack();

            // re-throw the exception
            throw $e;
        }
    }

    /**
     * Process the subject with the passed name/identifier.
     *
     * We create a new, fresh and separate subject for EVERY file here, because this would be
     * the starting point to parallelize the import process in a multithreaded/multiprocessed
     * environment.
     *
     * @param \TechDivision\Import\Configuration\SubjectInterface $subject The subject configuration
     *
     * @return void
     * @throws \Exception Is thrown, if the subject can't be processed
     */
    protected function processSubject(\TechDivision\Import\Configuration\SubjectInterface $subject)
    {

        // clear the filecache
        clearstatcache();

        // load the actual status
        $status = $this->getRegistryProcessor()->getAttribute($serial = $this->getSerial());

        // query whether or not the configured source directory is available
        if (!is_dir($sourceDir = $status[RegistryKeys::SOURCE_DIRECTORY])) {
            throw new \Exception(sprintf('Configured source directory %s is not available!', $sourceDir));
        }

        // initialize the array with the CSV files found in the source directory
        $this->files = glob(sprintf('%s/*.csv', $sourceDir));

        // query whether or not CSV files to be imported are available
        if (sizeof($this->files) === 0) {
            // log a message that no files to be imported are found
            $this->log(
                sprintf(
                    'Can\'t find any CSV files for subject %s to be imported in directory %s',
                    $subject->getClassName(),
                    $sourceDir
                ),
                LogLevel::INFO
            );
            return;
        }

        // immediately add the PID to lock this import process
        $this->addPid($this->getSerial());

        // sorting the files for the apropriate order
        usort($files, function ($a, $b) {
            return strcmp($a, $b);
        });

        // log a debug message
        $this->log(
            sprintf(
                'Now checking directory %s for files to be imported',
                $sourceDir
            ),
            LogLevel::DEBUG
        );

        // iterate through all CSV files and process the subjects
        foreach ($this->files as $pathname) {
            // query whether or not that the file is part of the actual bunch
            if ($this->isPartOfBunch($subject->getPrefix(), $pathname)) {
                // initialize the subject and import the bunch
                $subjectInstance = $this->subjectFactory($subject);

                // query whether or not the subject needs an OK file,
                // if yes remove the filename from the file
                if ($subjectInstance->needsOkFile()) {
                    $this->removeFromOkFile($pathname);
                }

                // finally import the CSV file
                $subjectInstance->import($serial, $pathname);

                // query whether or not, we've to export artefacts
                if ($subjectInstance instanceof ExportableSubjectInterface) {
                    $subjectInstance->export(
                        $this->matches[BunchKeys::FILENAME],
                        $this->matches[BunchKeys::COUNTER]
                    );
                }

                // raise the number of the imported bunches
                $this->bunches++;
            }
        }

        // finally remove the PID from the file and the PID file itself, if empty
        $this->removePid($serial);

        // reset the matches, because the exported artefacts
        $this->matches = array();

        // and and log a message that the subject has been processed
        $this->log(
            sprintf(
                'Successfully processed subject %s with %d bunch(es)!',
                $subject->getClassName(),
                $this->bunches
            ),
            LogLevel::DEBUG
        );
    }

    /**
     * Queries whether or not, the passed filename is part of a bunch or not.
     *
     * @param string $prefix   The prefix to query for
     * @param string $filename The filename to query for
     *
     * @return boolean TRUE if the filename is part, else FALSE
     */
    public function isPartOfBunch($prefix, $filename)
    {

        // initialize the pattern
        $pattern = '';

        // query whether or not, this is the first file to be processed
        if (sizeof($this->matches) === 0) {
            // initialize the pattern to query whether the FIRST file has to be processed or not
            $pattern = sprintf(
                '/^.*\/(?<%s>%s)_(?<%s>.*)_(?<%s>\d+)\\.csv$/',
                BunchKeys::PREFIX,
                $prefix,
                BunchKeys::FILENAME,
                BunchKeys::COUNTER
            );

        } else {
            // initialize the pattern to query whether the NEXT file is part of a bunch or not
            $pattern = sprintf(
                '/^.*\/(?<%s>%s)_(?<%s>%s)_(?<%s>\d+)\\.csv$/',
                BunchKeys::PREFIX,
                $this->matches[BunchKeys::PREFIX],
                BunchKeys::FILENAME,
                $this->matches[BunchKeys::FILENAME],
                BunchKeys::COUNTER
            );
        }

        // initialize the array for the matches
        $matches = array();

        // update the matches, if the pattern matches
        if ($result = preg_match($pattern, $filename, $matches)) {
            $this->matches = $matches;
        }

        // stop processing, if the filename doesn't match
        return (boolean) $result;
    }

    /**
     * Return's an array with the names of the expected OK files for the actual subject.
     *
     * @return array The array with the expected OK filenames
     */
    protected function getOkFilenames()
    {

        // load the array with the available bunch keys
        $bunchKeys = BunchKeys::getAllKeys();

        // initialize the array for the available okFilenames
        $okFilenames = array();

        // prepare the OK filenames based on the found CSV file information
        for ($i = 1; $i <= sizeof($bunchKeys); $i++) {
            // intialize the array for the parts of the names (prefix, filename + counter)
            $parts = array();
            // load the parts from the matches
            for ($z = 0; $z < $i; $z++) {
                $parts[] = $this->matches[$bunchKeys[$z]];
            }

            // query whether or not, the OK file exists, if yes append it
            if (file_exists($okFilename = sprintf('%s/%s.ok', $this->getSourceDir(), implode('_', $parts)))) {
                $okFilenames[] = $okFilename;
            }
        }

        // prepare and return the pattern for the OK file
        return $okFilenames;
    }

    /**
     * Query whether or not, the passed CSV filename is in the OK file. If the filename was found,
     * it'll be returned and the method return TRUE.
     *
     * If the filename is NOT in the OK file, the method return's FALSE and the CSV should NOT be
     * imported/moved.
     *
     * @param string $filename The CSV filename to query for
     *
     * @return void
     * @throws \Exception Is thrown, if the passed filename is NOT in the OK file or it can NOT be removed from it
     */
    protected function removeFromOkFile($filename)
    {

        try {
            // load the expected OK filenames
            $okFilenames = $this->getOkFilenames();

            // iterate over the found OK filenames (should usually be only one, but could be more)
            foreach ($okFilenames as $okFilename) {
                // if the OK filename matches the CSV filename AND the OK file is empty
                if (basename($filename, '.csv') === basename($okFilename, '.ok') && filesize($okFilename) === 0) {
                    unlink($okFilename);
                    return;
                }

                // else, remove the CSV filename from the OK file
                $this->removeLineFromFile(basename($filename), $okFilename);
                return;
            }

            // throw an exception if either no OK file has been found,
            // or the CSV file is not in one of the OK files
            throw new \Exception(
                sprintf(
                    'Can\'t found filename %s in one of the expected OK files: %s',
                    $filename,
                    implode(', ', $okFilenames)
                )
            );

        } catch (\Exception $e) {
            // wrap and re-throw the exception
            throw new \Exception(
                sprintf(
                    'Can\'t remove filename %s from OK file: %s',
                    $filename,
                    $okFilename
                ),
                null,
                $e
            );
        }
    }

    /**
     * Factory method to create new handler instances.
     *
     * @param \TechDivision\Import\Configuration\Subject $subject The subject configuration
     *
     * @return object The handler instance
     */
    public function subjectFactory($subject)
    {

        // load the subject class name
        $className = $subject->getClassName();

        // the database connection to use
        $connection = $this->getImportProcessor()->getConnection();

        // initialize a new handler with the passed class name
        $instance = new $className();

        // $instance the handler instance
        $instance->setConfiguration($subject);
        $instance->setSystemLogger($this->getSystemLogger());
        $instance->setRegistryProcessor($this->getRegistryProcessor());

        // instanciate and set the product processor, if specified
        if ($processorFactory = $subject->getProcessorFactory()) {
            $productProcessor = $processorFactory::factory($connection, $subject);
            $instance->setProductProcessor($productProcessor);
        }

        // return the subject instance
        return $instance;
    }

    /**
     * Lifecycle callback that will be inovked after the
     * import process has been finished.
     *
     * @return void
     * @throws \Exception Is thrown, if the
     */
    protected function archive()
    {

        // query whether or not, the import artefacts have to be archived
        if (!$this->getConfiguration()->haveArchiveArtefacts()) {
            $this->log(sprintf('Archiving functionality has not been activated'), LogLevel::INFO);
            return;
        }

        // if no files have been imported, return immediately
        if ($this->bunches === 0) {
            $this->log(sprintf('Found no files to archive'), LogLevel::INFO);
            return;
        }

        // clear the filecache
        clearstatcache();

        // load the actual status
        $status = $this->getRegistryProcessor()->getAttribute($this->getSerial());

        // query whether or not the configured source directory is available
        if (!is_dir($sourceDir = $status[RegistryKeys::SOURCE_DIRECTORY])) {
            throw new \Exception(sprintf('Configured source directory %s is not available!', $sourceDir));
        }

        // init file iterator on source directory
        $fileIterator = new \FilesystemIterator($sourceDir);

        // log the number of files that has to be archived
        $this->log(sprintf('Found %d files to archive in directory %s', $this->bunches, $sourceDir), LogLevel::INFO);

        // initialize the directory to create the archive in
        $archiveDir = sprintf('%s/%s', $this->getConfiguration()->getTargetDir(), $this->getConfiguration()->getArchiveDir());

        // query whether or not the directory already exists
        if (!is_dir($archiveDir)) {
            mkdir($archiveDir);
        }

        // create the ZIP archive
        $archive = new \ZipArchive();
        $archive->open($archiveFile = sprintf('%s/%s.zip', $archiveDir, $this->getSerial()), \ZipArchive::CREATE);

        // iterate through all files and add them to the ZIP archive
        foreach ($fileIterator as $filename) {
            $archive->addFile($filename);
        }

        // save the ZIP archive
        $archive->close();

        // finally remove the directory with the imported files
        $this->removeDir($sourceDir);

        // and and log a message that the import artefacts have been archived
        $this->log(sprintf('Successfully archived imported files to %s!', $archiveFile), LogLevel::INFO);
    }

    /**
     * Removes the passed directory recursively.
     *
     * @param string $src Name of the directory to remove
     *
     * @return void
     * @throws \Exception Is thrown, if the directory can not be removed
     */
    protected function removeDir($src)
    {

        // open the directory
        $dir = opendir($src);

        // remove files/folders recursively
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                $full = $src . '/' . $file;
                if (is_dir($full)) {
                    $this->removeDir($full);
                } else {
                    if (!unlink($full)) {
                        throw new \Exception(sprintf('Can\'t remove file %s', $full));
                    }
                }
            }
        }

        // close handle and remove directory itself
        closedir($dir);
        if (!rmdir($src)) {
            throw new \Exception(sprintf('Can\'t remove directory %s', $src));
        }
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
        return sprintf('%s/%s', sys_get_temp_dir(), Simple::PID_FILENAME);
    }

    /**
     * Persist the passed PID to PID filename.
     *
     * @param string $pid The PID of the actual import process to added
     *
     * @return void
     * @throws \Exception Is thrown, if the PID can not be added
     */
    protected function addPid($pid)
    {

        // open the PID file
        $fh = fopen($pidFilename = $this->getPidFilename(), 'a');

        // append the PID to the PID file
        if (fwrite($fh, $pid . PHP_EOL) === false) {
            throw new \Exception(sprintf('Can\'t write PID %s to PID file %s', $pid, $pidFilename));
        }

        // close the file handle
        fclose($fh);
    }

    /**
     * Remove's the actual PID from the PID file.
     *
     * @param string $pid The PID of the actual import process to be removed
     *
     * @return void
     * @throws \Exception Is thrown, if the PID can not be removed
     */
    protected function removePid($pid)
    {
        try {
            // remove the PID from the PID file
            $this->removeLineFromFile($pid, $this->getPidFilename());
        } catch (LineNotFoundException $lnfe) {
            $this->getSystemLogger()->notice(sprintf('PID %s is can not be found in PID file %s', $pid, $this->getPidFilename()));
        } catch (\Exception $e) {
            throw new \Exception(sprintf('Can\'t remove PID %s from PID file %s', $pid, $this->getPidFilename()), null, $e);
        }
    }

    /**
     * Lifecycle callback that will be inovked after the
     * import process has been finished.
     *
     * @return void
     */
    protected function tearDown()
    {
    }

    /**
     * This method finishes the import process and cleans the registry.
     *
     * @return void
     */
    protected function finish()
    {
        // remove the import status from the registry
        $this->getRegistryProcessor()->removeAttribute($this->getSerial());
    }


    /**
     * Remove's the passed line from the file with the passed name.
     *
     * @param string $line     The line to be removed
     * @param string $filename The name of the file the line has to be removed
     *
     * @return void
     * @throws \Exception Is thrown, if the line is not found or can not be removed
     */
    protected function removeLineFromFile($line, $filename)
    {

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
}
