<?php

/**
 * pacemaker.php
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
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */

// initialize the actual directory
$actualDirectory = dirname(__DIR__);

// initialize the possible vendor directories
$possibleVendorDirs = array(
    array($actualDirectory, sprintf('%s/techdivision/import-cli-simple/bootstrap.php', $actualDirectory)), // when installed as library in vendor/bin/pacemaker (QUICKFIX: this should never happen, because files has been copied and not symlinked)
    array($actualDirectory . '/vendor', sprintf('%s/bootstrap.php', $actualDirectory)),                    // when called directly from the root directory of this library
    array(dirname(dirname($actualDirectory)), sprintf('%s/bootstrap.php', $actualDirectory))               // when installed as library in directory vendor/techdivision/import-cli-pacemaker/bin/pacemaker
);

// try to locate the actual vendor directory
$loaded = false;
foreach ($possibleVendorDirs as $possibleVendorDir) {
    // list vendor directory and related boostrap file
    list ($vendorDir, $boostrapFile) = $possibleVendorDir;
    // if the autoload.php exsists, we've found the vendor directory
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
require $boostrapFile;
