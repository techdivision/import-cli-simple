<?php

/**
 * TechDivision\Import\Cli\SimpleTest
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

/**
 * Test class for the simple, single-threaded, importer implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class SimpleTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The instance to be tested.
     *
     * @var TechDivision\Import\Cli\Simple
     */
    protected $instance;

    /**
     * Initializes the instance we want to test.
     *
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->instance = new Simple();
    }

    /**
     * Test's the getter/setter for the import processor.
     *
     * @return void
     */
    public function testSetGetImportProcessor()
    {

        // create a import processor mock instance
        $mockImportProcessor = $this->getMockBuilder($processorInterface = 'TechDivision\Import\Services\ImportProcessorInterface')
                                    ->setMethods(get_class_methods($processorInterface))
                                    ->getMock();

        // test the setter/getter for the import processor
        $this->instance->setImportProcessor($mockImportProcessor);
        $this->assertSame($mockImportProcessor, $this->instance->getImportProcessor());
    }

    /**
     * Test's if the passed file is NOT part of a bunch.
     *
     * @return void
     */
    public function testIsPartOfBunchWithNoBunch()
    {

        // initialize the prefix and the actual date
        $prefix = 'magento-import';
        $actualDate = date('Ymd');

        // prepare some files which are NOT part of a bunch
        $data = array(
            array(sprintf('import/add-update/%s_%s-172_01.csv', $prefix, $actualDate), true),
            array(sprintf('import/add-update/%s_%s-173_01.csv', $prefix, $actualDate), false),
            array(sprintf('import/add-update/%s_%s-174_01.csv', $prefix, $actualDate), false),
        );

        // make sure, that only the FIRST file is part of the bunch
        foreach ($data as $row) {
            list ($filename, $result) = $row;
            $this->assertSame($result, $this->instance->isPartOfBunch($prefix, $filename));
        }
    }

    /**
     * Test's if the passed file IS part of a bunch.
     *
     * @return void
     */
    public function testIsPartOfBunchWithBunch()
    {

        // initialize the prefix and the actual date
        $prefix = 'magento-import';
        $actualDate = date('Ymd');

        // prepare some files which are NOT part of a bunch
        $data = array(
            array(sprintf('import/add-update/%s_%s-172_01.csv', $prefix, $actualDate), true),
            array(sprintf('import/add-update/%s_%s-172_02.csv', $prefix, $actualDate), true),
            array(sprintf('import/add-update/%s_%s-172_03.csv', $prefix, $actualDate), true),
        );

        // make sure, that the file IS part of the bunch
        foreach ($data as $row) {
            list ($filename, $result) = $row;
            $this->assertSame($result, $this->instance->isPartOfBunch($prefix, $filename));
        }
    }
}
