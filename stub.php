#!/usr/bin/env php
<?php
Phar::mapPhar();

/**
 * stub.php
 *
 * PHP version 57
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */

// if we're running from phar load the phar autoload, else let the
// script 'robo' search for the autoloader
if (strpos(basename(__FILE__), 'phar')) {
    require_once $autoloadFile = 'phar://import-cli-simple.phar/vendor/autoload.php';
} else {
    if (file_exists(__DIR__.'/vendor/autoload.php')) {
        require_once $autoloadFile = __DIR__.'/vendor/autoload.php';
    } elseif (file_exists(__DIR__.'/../../autoload.php')) {
        require_once $autoloadFile = __DIR__ . '/../../autoload.php';
    } else {
        require_once $autoloadFile = 'phar://import-cli-simple.phar/vendor/autoload.php';
    }
}

// initialize the vendor directory
$vendorDir = dirname($autoloadFile);

// bootstrap and run the application
require dirname($vendorDir) . '/bootstrap.php';

// stop the compiler
__HALT_COMPILER(); ?>