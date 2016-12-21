#!/usr/bin/env php
<?php
Phar::mapPhar();


/**
 * if we're running from phar load the phar autoload,
 * else let the script 'robo' search for the autoloader
 */
if (strpos(basename(__FILE__), 'phar')) {
    require_once 'phar://import-cli-simple.phar/vendor/autoload.php';
} else {
    if (file_exists(__DIR__.'/vendor/autoload.php')) {
        require_once __DIR__.'/vendor/autoload.php';
    } elseif (file_exists(__DIR__.'/../../autoload.php')) {
        require_once __DIR__ . '/../../autoload.php';
    } else {
        require_once 'phar://import-cli-simple.phar/vendor/autoload.php';
    }
}

use Symfony\Component\Console\Application;
use TechDivision\Import\Cli\Command\ImportProductsCommand;

// initialize the application
$application = new Application();
$application->add(new ImportProductsCommand());

// execute the command
$statusCode = $application->run();
exit($statusCode);

__HALT_COMPILER(); ?>