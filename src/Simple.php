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
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */

namespace TechDivision\Import\Cli;

use Rhumsaa\Uuid\Uuid;
use Psr\Log\LoggerInterface;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use League\Flysystem\FilesystemInterface;
use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\ConfigurationInterface;
use TechDivision\Import\Services\ImportProcessorInterface;
use TechDivision\Import\Services\RegistryProcessorInterface;
use TechDivision\Import\Utils\ConfigurationKeys;

/**
 * A SLSB that handles the product import process.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
class Simple
{

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
     * @param \TechDivision\Import\Services\RegistryProcessorInterface $registryProcessor
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
     * @return \TechDivision\Import\Services\RegistryProcessor The instance
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
     * Return's the prefix for the import files.
     *
     * @return string The prefix
     */
    public function getPrefix()
    {
        return $this->getConfiguration()->getPrefix();
    }

    /**
     * Return's the source directory that has to be watched for new files.
     *
     * @return string The source directory
     */
    public function getSourceDir()
    {
        return $this->getConfiguration()->getSourceDir();
    }

    /**
     * Parse the temporary upload directory for new files to be imported.
     *
     * @return void
     */
    public function import()
    {

        // track the start time
        $startTime = microtime(true);

        // generate the serial for the new job
        $this->setSerial(Uuid::uuid4()->__toString());

        // prepare the global data for the import process
        $this->start();
        $this->setUp();
        $this->processSubjects();
        $this->tearDown();
        $this->finish();

        // track the time needed for the import in seconds
        $endTime = microtime(true) - $startTime;

        // log a message that import has been finished
        $this->getSystemLogger()->debug(sprintf('Successfully finished import with serial %s in %f s', $this->getSerial(), $endTime));
    }

    /**
     * This method start's the import process by initializing
     * the status and appends it to the registry.
     *
     * @return void
     */
    public function start()
    {

        // initialize the status
        $status = array('status' => 1);

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
    public function setUp()
    {

        try {
            // load the registry
            $importProcessor = $this->getImportProcessor();
            $registryProcessor = $this->getRegistryProcessor();

            // initialize the array for the global data
            $globalData = array();

            // initialize the global data
            $globalData[RegistryKeys::STORES] = $importProcessor->getStores();
            $globalData[RegistryKeys::LINK_TYPES] = $importProcessor->getLinkTypes();
            $globalData[RegistryKeys::TAX_CLASSES] = $importProcessor->getTaxClasses();
            $globalData[RegistryKeys::STORE_WEBSITES] = $importProcessor->getStoreWebsites();
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
            $registryProcessor->mergeAttributesRecursive($this->getSerial(), array('globalData' => $globalData));

        } catch (\Exception $e) {
            $this->getSystemLogger()->error($e->__toString());
        }
    }

    /**
     * Process all the subjects defined in the system configuration.
     *
     * @return void
     */
    public function processSubjects()
    {

        try {
            // load system logger and registry
            $systemLogger = $this->getSystemLogger();
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
            // log a message with the stack trace
            $systemLogger->error($e->__toString());

            // rollback the transaction
            $importProcessor->getConnection()->rollBack();
        }
    }

    /**
     * Process the subject with the passed name/identifier.
     *
     * @param \TechDivision\Import\Configuration\Subject $subject The subject configuration
     *
     * @return void
     */
    public function processSubject($subject)
    {

        // load the system logger
        $systemLogger = $this->getSystemLogger();

        // init file iterator on deployment directory
        $fileIterator = new \FilesystemIterator($sourceDir = $subject->getSourceDir());

        // clear the filecache
        clearstatcache();

        // prepare the regex to find the files to be imported
        $regex = sprintf('/^.*\/%s.*\\.csv$/', $subject->getPrefix());

        // log a debug message
        $systemLogger->debug(
            sprintf('Now checking directory %s for files with regex %s to import', $sourceDir, $regex)
        );

        // iterate through all CSV files and start import process
        foreach (new \RegexIterator($fileIterator, $regex) as $filename) {
            try {
                // prepare the flag filenames
                $inProgressFilename = sprintf('%s.inProgress', $filename);
                $importedFilename = sprintf('%s.imported', $filename);
                $failedFilename = sprintf('%s.failed', $filename);

                // query whether or not the file has already been imported
                if (is_file($failedFilename) ||
                    is_file($importedFilename) ||
                    is_file($inProgressFilename)
                ) {
                    // log a debug message
                    $systemLogger->debug(
                        sprintf('Import running, found inProgress file %s', $inProgressFilename)
                    );

                    // ignore the file
                    continue;
                }

                // log the filename we'll process now
                $systemLogger->debug(sprintf('Now start importing file %s!', $filename));

                // flag file as in progress
                touch($inProgressFilename);

                // process the subject
                $this->subjectFactory($subject)->import($this->getSerial(), $filename->getPathname());

                // rename flag file, because import has been successfull
                rename($inProgressFilename, $importedFilename);

            } catch (\Exception $e) {
                // rename the flag file, because import failed
                rename($inProgressFilename, $failedFilename);

                // log a message that the file import failed
                $this->getSystemLogger()->error($e->__toString());

                // re-throw the exception
                throw $e;
            }
        }

        // and and log a message that the subject has been processed
        $systemLogger->info(sprintf('Successfully processed subject %s!', $subject->getClassName()));
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

        // initialize a new handler with the passed class name
        $instance = new $className();

        // $instance the handler instance
        $instance->setConfiguration($subject);
        $instance->setSystemLogger($this->getSystemLogger());
        $instance->setRegistryProcessor($this->getRegistryProcessor());

        // instanciate and set the product processor
        $processorFactory = $subject->getProcessorFactory();
        $productProcessor = $processorFactory::factory($this->getImportProcessor()->getConnection(), $subject);
        $instance->setProductProcessor($productProcessor);

        // return the subject instance
        return $instance;
    }

    /**
     * Lifecycle callback that will be inovked after the
     * import process has been finished.
     *
     * @return void
     */
    public function tearDown()
    {
        // clean up here
    }

    /**
     * This method finishes the import process and cleans the registry.
     *
     * @return void
     */
    public function finish()
    {
        $this->getRegistryProcessor()->removeAttribute($this->getSerial());
    }
}
