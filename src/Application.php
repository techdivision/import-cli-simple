<?php

/**
 * TechDivision\Import\Cli\Application
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

use Symfony\Component\DependencyInjection\TaggedContainerInterface;

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
class Application extends \Symfony\Component\Console\Application
{

    /**
     * The application name.
     *
     * @var string
     */
    protected $name = 'M2IF - Simple Console Tool';

    /**
     * The application version.
     *
     * @var string
     */
    protected $version = '1.0.0-alpha';

    /**
     * The DI container builder instance.
     *
     * @var \Symfony\Component\DependencyInjection\TaggedContainerInterface
     */
    protected $container;

    /**
     * The constructor to initialize the instance.
     *
     * @param \Symfony\Component\DependencyInjection\TaggedContainerInterface The container instance
     */
    public function __construct(TaggedContainerInterface $container)
    {

        // invoke the parent constructor
        parent::__construct($this->name, $this->version);

        // set the container instance
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
}
