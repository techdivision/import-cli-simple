<?php

/**
 * TechDivision\Import\Cli\AbstractIntegrationTest
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
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Cli;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use TechDivision\Import\Utils\EntityStatus;
use TechDivision\Import\Utils\StoreViewCodes;
use TechDivision\Import\Cli\Utils\DependencyInjectionKeys;

/**
 * Test class for the product URL rewrite observer implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
abstract class AbstractIntegrationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The container instance.
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected static $container;

    /**
     * The counter for the CSV files.
     *
     * @var integer
     */
    protected $counter = 0;

    /**
     * The templates for CSV file rows.
     *
     * @var array
     */
    protected $templates = array(
        'product-import' => array(
            'sku' => 'TEST-SKU-000001',
            'store_view_code' => null,
            'attribute_set_code' => 'Default',
            'product_type' => 'simple',
            'categories' => null,
            'product_websites' => 'base',
            'name' => 'Testproduct',
            'description' => 'Long description of Testproduct',
            'short_description' => 'Short description of Testproduct',
            'weight' => null,
            'product_online' => null,
            'tax_class_name' => 'Taxable Goods',
            'visibility' => 'Catalog, Search',
            'price' => 36,
            'special_price' => 33,
            'special_price_from_date' => null,
            'special_price_to_date' => null,
            'url_key' => null,
            'meta_title' => null,
            'meta_keywords' => null,
            'meta_description' => null,
            'base_image' => null,
            'base_image_label' => null,
            'small_image' => null,
            'small_image_label' => null,
            'thumbnail_image' => null,
            'thumbnail_image_label' => null,
            'swatch_image' => null,
            'swatch_image_label' => null,
            'created_at' => '10/24/16, 12:36 PM',
            'updated_at' => '10/24/16, 12:36 PM',
            'new_from_date' => null,
            'new_to_date' => null,
            'display_product_options_in' => null,
            'map_price' => null,
            'msrp_price' => null,
            'map_enabled' => null,
            'gift_message_available' => null,
            'custom_design' => null,
            'custom_design_from' => null,
            'custom_design_to' => null,
            'custom_layout_update' => null,
            'page_layout' => null,
            'product_options_container' => null,
            'msrp_display_actual_price_type' => null,
            'country_of_manufacture' => null,
            'additional_attributes' => null,
            'qty' => 100,
            'out_of_stock_qty' => 0,
            'use_config_min_qty' => 1,
            'is_qty_decimal' => 0,
            'allow_backorders' => 0,
            'use_config_backorders' => 1,
            'min_cart_qty' => 1,
            'use_config_min_sale_qty' => 1,
            'max_cart_qty' => 0,
            'use_config_max_sale_qty' => 1,
            'is_in_stock' => 1,
            'notify_on_stock_below' => null,
            'use_config_notify_stock_qty' => 1,
            'manage_stock' => 0,
            'use_config_manage_stock' => 1,
            'use_config_qty_increments' => 1,
            'qty_increments' => 0,
            'use_config_enable_qty_inc' => 1,
            'enable_qty_increments' => 0,
            'is_decimal_divided' => 0,
            'website_id' => 0,
            'related_skus' => null,
            'related_position' => null,
            'crosssell_skus' => null,
            'crosssell_position' => null,
            'upsell_skus' => null,
            'upsell_position' => null,
            'additional_images' => null,
            'additional_image_labels' => null,
            'hide_from_product_page' => null,
            'bundle_price_type' => null,
            'bundle_sku_type' => null,
            'bundle_price_view' => null,
            'bundle_weight_type' => null,
            'bundle_values,bundle_shipment_type' => null,
            'configurable_variations' => null,
            'configurable_variation_labels' => null,
            'associated_skus' => null
        ),
        'url-rewrite' => array(
            'original_data' => null,
            'sku' => 'TEST-SKU-000001',
            'store_view_code' => null,
            'categories' => null,
            'product_websites' => 'base',
            'name' => 'Testproduct',
            'visibility' => 'Catalog, Search',
            'url_key' => null
        )
    );

    /**
     * This method is called before the first test of this test class is run.
     *
     * @since Method available since Release 3.4.0
     */
    public static function setUpBeforeClass()
    {

        // initialize the vendor directory
        $vendorDir = sprintf('%s/vendor', getcwd());

        // the path of the JMS serializer directory, relative to the vendor directory
        $jmsDir = DIRECTORY_SEPARATOR . 'jms' . DIRECTORY_SEPARATOR . 'serializer' . DIRECTORY_SEPARATOR . 'src';

        // try to find the path to the JMS Serializer annotations
        if (!file_exists($annotationDir = $vendorDir . DIRECTORY_SEPARATOR . $jmsDir)) {
            // stop processing, if the JMS annotations can't be found
            throw new \Exception(
                sprintf(
                    'The jms/serializer libarary can not be found in one of "%s"',
                    implode(', ', $vendorDir)
                )
            );
        }

        // register the autoloader for the JMS serializer annotations
        \Doctrine\Common\Annotations\AnnotationRegistry::registerAutoloadNamespace(
            'JMS\Serializer\Annotation',
            $annotationDir
        );

        // initialize the DI container and set the vendor directory
        self::$container = new ContainerBuilder();
        self::$container->setParameter(DependencyInjectionKeys::CONFIGURATION_VENDOR_DIR, $vendorDir);

        // initialize the default loader and load the DI configuration for the this library
        $defaultLoader = new XmlFileLoader(self::$container, new FileLocator($vendorDir));
        $defaultLoader->load(dirname($vendorDir) . '/symfony/Resources/config/services.xml');
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {

        // load the configuration factory and create the configuration instance
        /** @var \TechDivision\Import\Configuration\Jms\ConfigurationFactory $configurationFactory */
        $configurationFactory = self::$container->get(DependencyInjectionKeys::CONFIGURATION_FACTORY);
        $configuration = $configurationFactory->factory($this->getConfigurationFile());

        // extend the plugins with the main configuration instance
        /** @var \TechDivision\Import\Cli\Configuration\Subject $subject */
        foreach ($configuration->getPlugins() as $plugin) {
            // set the configuration instance on the plugin
            $plugin->setConfiguration($configuration);

            // query whether or not the plugin has subjects configured
            if ($subjects = $plugin->getSubjects()) {
                // extend the plugin's subjects with the main configuration instance
                /** @var \TechDivision\Import\Cli\Configuration\Subject $subject */
                foreach ($subjects as $subject) {
                    // set the configuration instance on the subject
                    $subject->setConfiguration($configuration);
                }
            }
        }

        // initialize the configuration with the test specific data
        $configuration->setSourceDir($this->getSourceDir());
        $configuration->setTargetDir($this->getTargetDir());
        $configuration->setPidFilename($this->getPidFilename());
        $configuration->setOperationName($this->getOperationName());

        // load the library loader and autoload the libraries
        /** @var \TechDivision\Import\Cli\Configuration\LibraryLoader $libraryLoader */
        $libraryLoader = self::$container->get(DependencyInjectionKeys::LIBRARY_LOADER);
        $libraryLoader->load($configuration);

        // initialize the default loader and load the DI configuration for the this library
        $defaultLoader = new XmlFileLoader(self::$container, new FileLocator());

        // load the DI configuration for all the extension libraries
        $defaultLoader->load($this->getDiConfigurationFile());

        // add the configuration to the DI container
        self::$container->set(DependencyInjectionKeys::CONFIGURATION, $configuration);

        // start the transaction
        self::$container->get(DependencyInjectionKeys::CONNECTION)->getConnection()->beginTransaction();

        // create the temporary directory, if not extists
        if (!is_dir($tmpDir = $this->getTmpDir())) {
            mkdir($tmpDir);
        }
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     * @see \PHPUnit_Framework_TestCase::tearDown()
     */
    protected function tearDown()
    {

        // stop the transaction and rollback
        self::$container->get(DependencyInjectionKeys::CONNECTION)->getConnection()->rollback();

        // clean-up the temporary directory
        $this->removeDir($this->getTmpDir());
    }

    /**
     * Removes a directory recursively.
     *
     * @param string $src The source directory to be removed
     *
     * @return void
     */
    protected static function removeDir($src)
    {

        // open the directory
        $dir = opendir($src);

        // remove files/folders recursively
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                $full = $src . '/' . $file;
                if (is_dir($full)) {
                    AbstractIntegrationTest::removeDir($full);
                } else {
                    unlink($full);
                }
            }
        }

        // close handle and remove directory itself
        closedir($dir);
        rmdir($src);
    }

    /**
     * Process the import for the passed times.
     *
     * @param integer $times The number the import has to be processed
     *
     * @return void
     */
    protected function processImport($times = 1)
    {
        for ($i = 0; $i < $times; $i++) {
            self::$container->get(DependencyInjectionKeys::SIMPLE)->process();
        }
    }

    /**
     * The absolute path to the directory containing testfiles (CSV, configuration, etc.).
     *
     * @return string The absolute path to the testfiles
     */
    protected function getFilesDir()
    {
        return sprintf('%s/_files', __DIR__);
    }

    /**
     * The absolute path the the tempoary directory to use.
     *
     * @return string The absolute path to the temporary directory
     */
    protected function getTmpDir()
    {
        return sprintf('%s/tmp', $this->getFilesDir());
    }

    /**
     * The source directory with the CSV files to test.
     *
     * @return string The absolute path to the source directory
     */
    protected function getSourceDir()
    {
        return $this->getTmpDir();
    }

    /**
     * The target directory for the import artefacts.
     *
     * @return string The absolute path to the target directory
     */
    protected function getTargetDir()
    {
        return $this->getTmpDir();
    }

    /**
     * The absolute path to the PID file.
     *
     * @return string The absolute path to the PID file
     */
    protected function getPidFilename()
    {
        return sprintf('%s/%s.pid', $this->getTmpDir(), uniqid());
    }

    /**
     * Copy the .csv + .ok files from the working to the temporary directory.
     *
     * @param string $workingDir The working directory with the testfiles
     *
     * @return void
     */
    protected function prepareFiles($workingDir)
    {

        // copy the CSV files from the working to the source directory
        foreach (glob(sprintf('%s/%s/*.{csv,ok}', $this->getFilesDir(), $workingDir), GLOB_BRACE) as $filename) {
            copy($filename, sprintf('%s/%s', $this->getTmpDir(), basename($filename)));
        }
    }

    /**
     * Merges the data of the passed row into the default values and returns it.
     *
     * @param array  $row  The array with the data to merge
     * @param string $type The row type, one of product, category or attribute
     *
     * @return array The prepared row
     */
    protected function prepareRow(array $row = array(), $type = 'product-import')
    {

        // load the headers and the default values from the template
        $headers = array_flip(array_keys($this->templates[$type]));
        $defaultValues = array_values($this->templates[$type]);

        // replace the default values with the passed one
        foreach ($row as $columnName => $columnValue) {
            $defaultValues[$headers[$columnName]] = $columnValue;
        }

        // return the prepared row
        return $defaultValues;
    }

    /**
     * Creates a CSV file from the passed data.
     *
     * @param array  $rows          The rows to export
     * @param string $type          The type of the export file, one of product, category or attribute
     * @param string $bunchCounter  The bunch counter if the file should be part of a bundle
     *
     * @return string The filename of the CSV file created
     */
    protected function prepareFile(array $rows = array(), $type = 'product-import', $bunchCounter = '01')
    {

        // prepend the headers to the rows
        array_unshift($rows, array_keys($this->templates[$type]));

        // prepare the file we want to export to
        $filename = sprintf('%s/%s_%s-%s_01.csv', $this->getTmpDir(), $type, date('Ymd'), ++$this->counter, $bunchCounter);

        // export the data to the file
        self::$container->get(DependencyInjectionKeys::IMPORT_ADAPTER_EXPORT)->export($filename, $rows);

        // return the filename
        return $filename;
    }

    /**
     * Creates a CSV file from the passed data.
     *
     * @param array   $row           The row to export
     * @param string  $type          The type of the export file, one of product, category or attribute
     * @param string  $bunchCounter  The bunch counter if the file should be part of a bundle
     * @param boolean $merge         The flag that the passed data is only a subset and has to be merge with the default data
     *
     * @return string The filename of the CSV file created
     */
    protected function prepareFileWithSingleRow(array $row = array(), $storeViews = array(), $type = 'product-import', $bunchCounter = '01', $merge = true)
    {

        // initialize the array with the export data with the headers
        $preparedData = array(array_keys($this->templates[$type]));

        // append the row to the export data
        array_push($preparedData, $merge ? $this->prepareRow($row) : $row);

        // prepare the file we want to exprot
        $filename = sprintf('%s/%s_%s-%s_%s.csv', $this->getTmpDir(), $type, date('Ymd'), ++$this->counter, $bunchCounter);

        // export the data to the file
        self::$container->get(DependencyInjectionKeys::IMPORT_ADAPTER_EXPORT)->export($filename, $preparedData);

        // return the filename
        return $filename;
    }

    /**
     * Return's the import processor instance.
     *
     * @return \TechDivision\Import\Services\ImportProcessorInterface The import processor instance
     */
    protected function getImportProcessor()
    {
        return self::$container->get(DependencyInjectionKeys::IMPORT_PROCESSOR_IMPORT);
    }

    /**
     * Return's the product bunch processor instance.
     *
     * @return \TechDivision\Import\Product\Services\ProductBunchProcessorInterface The product bunch processor instance
     */
    protected function getProductBunchProcessor()
    {
        return self::$container->get(\TechDivision\Import\Product\Utils\DependencyInjectionKeys::PROCESSOR_PRODUCT_BUNCH);
    }

    /**
     * Return's the product URL rewrite processor instance.
     *
     * @return \TechDivision\Import\Product\UrlRewrite\Services\ProductUrlRewriteProcessorInterface The product URL rewrite processor instance
     */
    protected function getProductUrlRewriteProcessor()
    {
        return self::$container->get(\TechDivision\Import\Product\UrlRewrite\Utils\DependencyInjectionKeys::PROCESSOR_PRODUCT_URL_REWRITE);
    }

    /**
     * Return's the category bunch processor instance.
     *
     * @return \TechDivision\Import\Category\Services\CategoryBunchProcessorInterface The catgory bunch processor instance
     */
    protected function getCategoryBunchProcessor()
    {
        return self::$container->get(\TechDivision\Import\Category\Utils\DependencyInjectionKeys::PROCESSOR_CATEGORY_BUNCH);
    }

    /**
     * Return's the root category.
     *
     * @param string $storeViewCode The store view code to return the root category for
     *
     * @return array The root category for the passed store view code
     */
    protected function getRootCategory($storeViewCode)
    {

        // load the available root categories
        $rootCategories = $this->getCategoryBunchProcessor()->getRootCategories();

        // query whether or not a root category for the passed store view code exists
        if (isset($rootCategories[$storeViewCode])) {
            return $rootCategories[$storeViewCode];
        }

        // throw an exception, if not
        throw \Exception(sprintf('Can\'t find root category for store view code "%s"', $storeViewCode));
    }

    /**
     * Creates a new category with the passed data.
     *
     * @param string  $name     The category name
     * @param integer $parentId The ID of the parent category
     * @param boolean $isAnchor TRUE if the category has the anchor flag set, else FALSE
     *
     * @return integer The ID of the new category
     */
    protected function createCategory($name, $isAnchor = false)
    {

        // load the root category for the default store view code
        $rootCategory = $this->getRootCategory(StoreViewCodes::DEF);
        $parentId = $rootCategory[\TechDivision\Import\Category\Utils\MemberNames::ENTITY_ID];

        // create an addtional store view
        $categoryId = $this->getCategoryBunchProcessor()->persistCategory(
            array(
                \TechDivision\Import\Category\Utils\MemberNames::ATTRIBUTE_SET_ID => 3,
                \TechDivision\Import\Category\Utils\MemberNames::PARENT_ID        => $parentId,
                \TechDivision\Import\Category\Utils\MemberNames::CREATED_AT       => date('Y-m-d H:i:s'),
                \TechDivision\Import\Category\Utils\MemberNames::UPDATED_AT       => date('Y-m-d H:i:s'),
                \TechDivision\Import\Category\Utils\MemberNames::PATH             => '',
                \TechDivision\Import\Category\Utils\MemberNames::POSITION         => 0,
                \TechDivision\Import\Category\Utils\MemberNames::LEVEL            => 2,
                \TechDivision\Import\Category\Utils\MemberNames::CHILDREN_COUNT   => 0,
                EntityStatus::MEMBER_NAME                                         => EntityStatus::STATUS_CREATE
            )
        );

        // update the category with the correct path
        $this->getCategoryBunchProcessor()->persistCategory(
            array(
                \TechDivision\Import\Category\Utils\MemberNames::ENTITY_ID        => $categoryId,
                \TechDivision\Import\Category\Utils\MemberNames::ATTRIBUTE_SET_ID => 3,
                \TechDivision\Import\Category\Utils\MemberNames::PARENT_ID        => $parentId,
                \TechDivision\Import\Category\Utils\MemberNames::CREATED_AT       => date('Y-m-d H:i:s'),
                \TechDivision\Import\Category\Utils\MemberNames::UPDATED_AT       => date('Y-m-d H:i:s'),
                \TechDivision\Import\Category\Utils\MemberNames::PATH             => sprintf('1/2/%s', $categoryId),
                \TechDivision\Import\Category\Utils\MemberNames::POSITION         => 0,
                \TechDivision\Import\Category\Utils\MemberNames::LEVEL            => 2,
                \TechDivision\Import\Category\Utils\MemberNames::CHILDREN_COUNT   => 0,
                EntityStatus::MEMBER_NAME                                         => EntityStatus::STATUS_UPDATE
            )
        );

        // create the "name" attribute
        $this->getCategoryBunchProcessor()->persistCategoryVarcharAttribute(
            array(
                \TechDivision\Import\Category\Utils\MemberNames::ATTRIBUTE_ID => 45,
                \TechDivision\Import\Category\Utils\MemberNames::STORE_ID     => 0,
                \TechDivision\Import\Category\Utils\MemberNames::ENTITY_ID    => $categoryId,
                \TechDivision\Import\Category\Utils\MemberNames::VALUE        => $name,
                EntityStatus::MEMBER_NAME                                     => EntityStatus::STATUS_CREATE
            )
        );

        // create the "url_key" attribute
        $this->getCategoryBunchProcessor()->persistCategoryVarcharAttribute(
            array(
                \TechDivision\Import\Category\Utils\MemberNames::ATTRIBUTE_ID => 124,
                \TechDivision\Import\Category\Utils\MemberNames::STORE_ID     => 0,
                \TechDivision\Import\Category\Utils\MemberNames::ENTITY_ID    => $categoryId,
                \TechDivision\Import\Category\Utils\MemberNames::VALUE        => 'testcategory',
                EntityStatus::MEMBER_NAME                                     => EntityStatus::STATUS_CREATE
            )
        );

        // create the "url_path" attribute
        $this->getCategoryBunchProcessor()->persistCategoryVarcharAttribute(
            array(
                \TechDivision\Import\Category\Utils\MemberNames::ATTRIBUTE_ID => 125,
                \TechDivision\Import\Category\Utils\MemberNames::STORE_ID     => 0,
                \TechDivision\Import\Category\Utils\MemberNames::ENTITY_ID    => $categoryId,
                \TechDivision\Import\Category\Utils\MemberNames::VALUE        => 'testcategory',
                EntityStatus::MEMBER_NAME                                     => EntityStatus::STATUS_CREATE
            )
        );

        // create the "is_anchor" attribute
        $this->getCategoryBunchProcessor()->persistCategoryIntAttribute(
            array(
                \TechDivision\Import\Category\Utils\MemberNames::ATTRIBUTE_ID => 54,
                \TechDivision\Import\Category\Utils\MemberNames::STORE_ID     => 0,
                \TechDivision\Import\Category\Utils\MemberNames::ENTITY_ID    => $categoryId,
                \TechDivision\Import\Category\Utils\MemberNames::VALUE        => $isAnchor ? 1 : 0,
                EntityStatus::MEMBER_NAME                                     => EntityStatus::STATUS_CREATE
            )
        );

        // return the ID of the created category
        return $categoryId;
    }

    /**
     * The operation that has to be tested.
     *
     * @return string The operation name
     */
    abstract protected function getOperationName();

    /**
     * The absolute path the configuration file.
     *
     * @return string The absolute path to the configuration file
     */
    abstract protected function getConfigurationFile();

    /**
     * The additional DI configuration used for the test case.
     *
     * @return string The absolute path to the DI configuration file
     */
    abstract protected function getDiConfigurationFile();
}
