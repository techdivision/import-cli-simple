<?php

/**
 * import-simple.php
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

// initialize the possible vendor directories
$possibleVendorDirs = array(
    dirname(__DIR__),
    dirname(__DIR__) . '/vendor',
    dirname(dirname(dirname(__DIR__)))
);

// try to locate the actual vendor directory
$loaded = false;
foreach ($possibleVendorDirs as $vendorDir) {
    if (file_exists($file = sprintf('%s/autoload.php', $vendorDir))) {
        require $file;
        $loaded = true;
        break;
    }
}

// stop processing, if NO vendor directory has been detected
if (!$loaded) {
    die(
        'You need to set up the project dependencies using the following commands:' . PHP_EOL .
        'wget http://getcomposer.org/composer.phar' . PHP_EOL .
        'php composer.phar install' . PHP_EOL
    );
}

// bootstrap and run the application
require dirname(__DIR__) . '/bootstrap.php';
