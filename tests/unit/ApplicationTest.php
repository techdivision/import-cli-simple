<?php

/**
 * TechDivision\Import\Cli\ApplicationTest
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

namespace TechDivision\Import;

use TechDivision\Import\Cli\Application;

/**
 * Test class for the symfony application implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The application that has to be tested.
     *
     * @var \TechDivision\Import\Cli\Application
     */
    protected $application;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {

        // mock the container instance
        $mockContainer = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
                              ->setMethods(get_class_methods('Symfony\Component\DependencyInjection\ContainerInterface'))
                              ->getMock();

        // create an instance of the application
        $this->application = new Application($mockContainer);
    }

    /**
     * Test the getContainer() method.
     *
     * @return void
     */
    public function testGetContainer()
    {
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\ContainerInterface', $this->application->getContainer());
    }
}
