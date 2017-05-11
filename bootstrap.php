<?php

/**
 * bootstrap.php
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

// import the used classes
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use TechDivision\Import\Cli\Utils\DependencyInjectionKeys;

// initialize the DI container and set the vendor directory
$container = new ContainerBuilder();
$container->setParameter(DependencyInjectionKeys::CONFIGURATION_VENDOR_DIR, $vendorDir);

// initialize the default loader and load the DI configuration for the this library
$defaultLoader = new XmlFileLoader($container, new FileLocator($vendorDir));
$defaultLoader->load(__DIR__ . '/symfony/Resources/config/services.xml');

// initialize and run the application
$statusCode = $container->get(DependencyInjectionKeys::APPLICATION)->run($container->get(DependencyInjectionKeys::INPUT));

// stop and render the status code
exit($statusCode);
