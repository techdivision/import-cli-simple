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

use TechDivision\Import\Cli\Application;
use TechDivision\Import\Cli\Command\ImportProductsCommand;
use TechDivision\Import\Cli\Command\ImportCategoriesCommand;
use TechDivision\Import\Cli\Command\ImportClearPidFileCommand;
use TechDivision\Import\Cli\Command\ImportCreateOkFileCommand;

$application = new Application();
$application->add(new ImportProductsCommand());
$application->add(new ImportCategoriesCommand());
$application->add(new ImportClearPidFileCommand());
$application->add(new ImportCreateOkFileCommand());

// execute the command
$statusCode = $application->run();

// stop and render the status code
exit($statusCode);

__HALT_COMPILER(); ?>