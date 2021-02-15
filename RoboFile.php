<?php

/**
 * RoboFile.php
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

use Lurker\Event\FilesystemEvent;
use Symfony\Component\Finder\Finder;

/**
 * Defines the available build tasks.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class RoboFile extends \Robo\Tasks
{

    /**
     * The build properties.
     *
     * @var array
     */
    protected $properties = array(
        'base.dir' => __DIR__,
        'etc.dir' => __DIR__ . '/etc',
        'dist.dir' => __DIR__ . '/dist',
        'vendor.dir' => __DIR__ . '/vendor',
        'target.dir' => __DIR__ . '/target',
        'symfony.dir' => __DIR__ . '/symfony',
        'webapp.name' => 'import-cli-simple',
        'webapp.version' => '3.8.42'
    );

    /**
     * Run's the composer install command.
     *
     * @return \Robo\Result The result
     */
    public function composerInstall()
    {
        // optimize autoloader with custom path
        return $this->taskComposerInstall()
             ->preferDist()
             ->optimizeAutoloader()
             ->run();
    }

    /**
     * Run's the composer update command.
     *
     * @return \Robo\Result The result
     */
    public function composerUpdate()
    {
        // optimize autoloader with custom path
        return $this->taskComposerUpdate()
             ->preferDist()
             ->optimizeAutoloader()
             ->run();
    }

    /**
     * Clean up the environment for a new build.
     *
     * @return \Robo\Result The result
     */
    public function clean()
    {
        return $this->taskDeleteDir($this->properties['target.dir'])->run();
    }

    /**
     * Prepare's the environment for a new build.
     *
     * @return \Robo\Result The result
     */
    public function prepare()
    {

        // prepare the directories
        return $this->taskFileSystemStack()
             ->mkdir($this->properties['dist.dir'])
             ->mkdir($this->properties['target.dir'])
             ->mkdir(sprintf('%s/reports', $this->properties['target.dir']))
             ->run();
    }

    /**
     * Prepare's the Docker environment for a new build.
     *
     * @param string $domainName    The domain name used to invoke the Magento 2 instance inside the Docker container
     * @param string $containerName The Docker container name
     *
     * @return void
     */
    public function prepareDocker($domainName, $containerName)
    {

        // stop the build on first failure of a task
        $this->stopOnFail(true);

        // prepare the filesystem
        $this->prepare();

        // initialize the variables to query whether or not the docker container has been started successfully
        $counter = 0;
        $magentoNotAvailable = true;

        do {
            // reset the result of the CURL request
            $res = null;

            // query whether or not the image already has been loaded
            exec(
                str_replace(
                    array('{domain-name}'),
                    array($domainName),
                    'curl --resolve {domain-name}:80:127.0.0.1 http://{domain-name}/magento_version'
                ),
                $res
            );

            // query whether or not the Docker has been started
            foreach ($res as $val) {
                if (strstr($val, 'Magento/')) {
                    $magentoNotAvailable = false;
                }
            }

            // raise the counter
            $counter++;

            // sleep while the docker container is not available
            if ($magentoNotAvailable === true) {
                sleep(1);
            }

        } while ($magentoNotAvailable && $counter < 30);

        // activate batch commit behaviour to improve performance
        $this->taskDockerExec($containerName)
            ->interactive()
            ->exec('mysql -uroot -proot -e \'SET GLOBAL innodb_flush_log_at_trx_commit = 2\'')
            ->run();

        // grant the privilieges to connection from outsite the container
        $this->taskDockerExec($containerName)
            ->interactive()
            ->exec('mysql -uroot -proot -e \'GRANT ALL ON *.* TO "magento"@"%" IDENTIFIED BY "magento"\'')
            ->run();

        // flush the privileges
        $this->taskDockerExec($containerName)
            ->interactive()
            ->exec('mysql -uroot -proot -e "FLUSH PRIVILEGES"')
            ->run();
    }

    /**
     * Creates the a PHAR archive from the sources.
     *
     * @return void
     */
    public function createPhar()
    {

        // stop the build on first failure of a task
        $this->stopOnFail(true);

        // run the build process
        $this->build();

        // prepare the PHAR archive name
        $archiveName = sprintf(
            '%s/%s.phar',
            $this->properties['target.dir'],
            $this->properties['webapp.name']
        );

        // prepare the target directory
        $targetDir = $this->properties['target.dir'] . DIRECTORY_SEPARATOR . $this->properties['webapp.version'];

        // copy the composer.json file
        $this->taskFilesystemStack()
             ->copy(
                  __DIR__ . DIRECTORY_SEPARATOR . 'composer.json',
                  $targetDir. DIRECTORY_SEPARATOR. 'composer.json'
             )->run();

          // copy the .semver file
          $this->taskFilesystemStack()
               ->copy(
                   __DIR__ . DIRECTORY_SEPARATOR . '.semver',
                   $targetDir. DIRECTORY_SEPARATOR. '.semver'
               )->run();

          // copy the bootstrap.php file
          $this->taskFilesystemStack()
               ->copy(
                  __DIR__ . DIRECTORY_SEPARATOR . 'bootstrap.php',
                  $targetDir. DIRECTORY_SEPARATOR. 'bootstrap.php'
               )->run();

        // install the composer dependencies
        $this->taskComposerInstall()
            ->dir($targetDir)
            ->noDev()
            ->optimizeAutoloader()
            ->run();

        // prepare the task
        $pharTask = $this->taskPackPhar($archiveName)
            ->compress()
            ->stub('stub.php');

        // load a list with all the source files from the vendor directory
        $finder = Finder::create()->files()
            ->name('*.php')
            ->name('*.json')
            ->name('.semver')
            ->name('services.xml')
            ->name('services-1.0.xsd')
            ->in($targetDir)
            ->ignoreDotFiles(false);

        // iterate over the source files of the vendor directory and add them to the PHAR archive
        foreach ($finder as $file) {
            $pharTask->addFile($file->getRelativePathname(), $file->getRealPath());
        }

        // create the PHAR archive
        $pharTask->run();

        // verify PHAR archive is packed correctly
        $this->_exec(sprintf('php %s', $archiveName));

        // prepare the PHAR archive distribution name
        $distArchiveName = sprintf('%s/%s.phar', $this->properties['dist.dir'], $this->properties['webapp.name']);

        // clean up the dist directory
        $this->taskCleanDir($this->properties['dist.dir'])->run();

        // copy the latest PHAR archive to the dist directory
        $this->taskFilesystemStack()->copy($archiveName, $distArchiveName)->run();
    }

    /**
     * Load the repository source directories that matches the passed pattern.
     *
     * @param string $glue    The glue used the the directory are concatenated to a string
     * @param string $pattern The pattern used to load the source directories
     *
     * @return string The concatenated relative source directories
     */
    protected function loadLibrarySourceDirs(string $glue = ',', string $pattern = 'techdivision/*/src') : string
    {

        // load the source directories
        $sourceDirs = glob(sprintf('%s/%s', $this->properties['vendor.dir'], $pattern), GLOB_ONLYDIR);

        // cut-off the actual path
        array_walk($sourceDirs, function (&$value) {
            $value = ltrim(str_replace(__DIR__, '', $value), '/');
        });

        // implode and return the source directories
        return implode($glue, $sourceDirs);
    }

    /**
     * Run's the PHPMD.
     *
     * @return \Robo\Result The result
     */
    public function runMd()
    {

        // run the mess detector
        return $this->_exec(
            sprintf(
                '%s/bin/phpmd %s xml phpmd.xml --reportfile %s/reports/pmd.xml --ignore-violations-on-exit',
                $this->properties['vendor.dir'],
                $this->loadLibrarySourceDirs(),
                $this->properties['target.dir']
            )
        );
    }

    /**
     * Run's the PHPCodeSniffer.
     *
     * @return \Robo\Result The result
     */
    public function runCs()
    {

        // load the repositories that matches the pattern the vendor/techdivision/*/src directories
        $dirs = glob(sprintf('%s/techdivision/*/src', $this->properties['vendor.dir']), GLOB_ONLYDIR);

        // run the code sniffer
        return $this->_exec(
            sprintf(
                '%s/bin/phpcs -n --report-full --extensions=php --standard=phpcs.xml --report-checkstyle=%s/reports/phpcs.xml %s',
                $this->properties['vendor.dir'],
                $this->properties['target.dir'],
                $this->loadLibrarySourceDirs(' ')
            )
        );
    }

    /**
     * Run's the PHPCPD.
     *
     * @return \Robo\Result The result
     */
    public function runCpd()
    {

        // prepare the patterns for the files that has to be ignored
        $ignore = array(
            'Utils/MemberNames',
            'Loaders/RawEntityLoader',
            'Subjects/BunchSubject',
            'Subjects/EeBunchSubject',
            'Subjects/OptionSubject',
            'Subjects/ConverterSubject',
            'Services/CategoryBunchProcessor',
            'Plugins/MissingOptionValuesPlugin',
            'Observers/EeBundleOptionObserver',
            'Observers/EeMediaGalleryValueObserver',
            'Observers/EeBundleSelectionUpdateObserver',
            'Observers/CustomerAddressAttributeObserver',
            'Observers/ProductToAttributeOptionValueConverterObserver'
        );

        // run the copy past detector
        return $this->_exec(
            sprintf(
                '%s/bin/phpcpd --regexps-exclude %s %s/techdivision/*/src --log-pmd %s/reports/pmd-cpd.xml',
                $this->properties['vendor.dir'],
                implode(',', $ignore),
                $this->properties['vendor.dir'],
                $this->properties['target.dir']
            )
        );
    }

    /**
     * Run's the PHPUnit testsuite.
     *
     * @return \Robo\Result The result
     */
    public function runTestsUnit()
    {

        // run PHPUnit
        return $this->taskPHPUnit(
                sprintf(
                    '%s/bin/phpunit --testsuite "techdivision/import-cli-simple PHPUnit testsuite"',
                    $this->properties['vendor.dir']
                )
            )
            ->configFile('phpunit.xml')
            ->run();
    }

    /**
     * Run's the integration testsuite.
     *
     * This task uses the Magento 2 docker image generator from https://github.com/techdivision/magento2-docker-imgen. To execute
     * this task, it is necessary that you've setup a running container with the domain name, passed as argument.
     *
     * @return \Robo\Result The result
     */
    public function runTestsIntegration()
    {

        // run the integration testsuite
        return $this->taskPHPUnit(
                sprintf(
                    '%s/bin/phpunit --testsuite "techdivision/import-cli-simple PHPUnit integration testsuite"',
                    $this->properties['vendor.dir']
                )
             )
             ->configFile('phpunit.xml')
             ->run();
    }

    /**
     * Run's the acceptance testsuite.
     *
     * This task uses the Magento 2 docker image generator from https://github.com/techdivision/magento2-docker-imgen. To execute
     * this task, it is necessary that you've setup a running container with the domain name, passed as argument.
     *
     * @return \Robo\Result The result
     */
    public function runTestsAcceptance($magentoEdition = 'ce', $magentoVersion = '2.3.3')
    {

        // initialize the default tags
        $tags = sprintf('@%s&&@%s&&~@customer&&~@customer-address', strtolower($magentoEdition), implode('.', sscanf($magentoVersion, "%d.%d")));

        // query whether or not the version is lower than 2.3.3, because then we've to ignore the MSI tests
        if (version_compare($magentoVersion, '2.3.2') < 1) {
            $tags = sprintf('%s&&~msi', $tags);
        }

        // finally, invoke the acceptance tests
        return $this->taskBehat()
            ->format('pretty')
            ->option('tags', $tags)
            ->noInteraction()
            ->run();
        }

    /**
     * Raising the semver version number.
     *
     * @return \Robo\Result The result
     */
    public function semver()
    {
        return $this->taskSemVer('.semver')
             ->prerelease('beta')
             ->run();
    }

    /**
     * The complete build process.
     *
     * @return void
     */
    public function build()
    {

        // stop the build on first failure of a task
        $this->stopOnFail(true);

        // process the build
        $this->clean();
        $this->prepare();
        $this->runCpd();
        $this->runCs();
        $this->runMd();
        $this->runTestsUnit();
    }
}
