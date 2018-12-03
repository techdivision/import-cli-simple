<?php

/**
 * TechDivision\Import\Cli\Product\AddUpdateOperationTest
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

namespace TechDivision\Import\Cli\Product;

use TechDivision\Import\Utils\OperationKeys;
use TechDivision\Import\Product\Utils\ColumnKeys;
use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Cli\AbstractIntegrationTest;

/**
 * Test class for the product functionality.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product
 * @link      http://www.techdivision.com
 */
class AddUpdateOperationTest extends AbstractIntegrationTest
{

    /**
     * The operation that has to be tested.
     *
     * @return string The operation name
     */
    protected function getOperationName()
    {
        return OperationKeys::ADD_UPDATE;
    }

    /**
     * The configuration used for the test case.
     *
     * @return string The absolute path to the configuration file
     * @see \TechDivision\Import\Cli\AbstractIntegrationTest::getConfigurationFile()
     */
    protected function getConfigurationFile()
    {
        return sprintf('%s/ce/2.3.x/conf/product/add-update-operation/techdivision-import.json', $this->getFilesDir());
    }

    /**
     * The additional DI configuration used for the test case.
     *
     * @return string The absolute path to the DI configuration file
     * @see \TechDivision\Import\Cli\AbstractIntegrationTest::getDiConfigurationFile()
     */
    protected function getDiConfigurationFile()
    {
        return sprintf('%s/ce/2.3.x/conf/services.xml', $this->getFilesDir());
    }

    /**
     * Test's the add-update operation with a simple product without category relation.
     *
     * @return void
     */
    public function testAddUpdateWithSimpleProduct()
    {

        // prepare the file we want to import
        $filename = $this->prepareFileWithSingleRow();

        // process the import operation
        $this->processImport();

        // make sure, the flag file for a successfull import exists
        $this->assertCount(1, glob(sprintf('%s/*/%s.imported', $this->getTmpDir(), basename($filename))));

        // initialize the product bunch processor instance
        $productBunchProcessor = $this->getProductBunchProcessor();

        // try to load the imported product by its SKU
        $product = $productBunchProcessor->loadProduct($sku = 'TEST-SKU-000001');

        // assert the expected product entity data
        $this->assertArrayHasKey('sku', $product);
        $this->assertSame($sku, $product[MemberNames::SKU]);
        $this->assertSame(4, (integer) $product[MemberNames::ATTRIBUTE_SET_ID]);
        $this->assertSame('simple', $product[MemberNames::TYPE_ID]);
        $this->assertSame(0, (integer) $product[MemberNames::HAS_OPTIONS]);
        $this->assertSame(0, (integer) $product[MemberNames::REQUIRED_OPTIONS]);
        $this->assertSame('2016-10-24 12:36:00', $product[MemberNames::CREATED_AT]);
        $this->assertSame('2016-10-24 12:36:00', $product[MemberNames::UPDATED_AT]);
    }

    /**
     * Test's the add-update operation with a simple product with one category relation.
     *
     * @return void
     */
    public function testAddUpdateWithSimpleProductAndCategoryRelation()
    {

        // create a new category
        $categoryId = (integer) $this->createCategory('Testcategory');

        // prepare the file we want to import
        $filename = $this->prepareFileWithSingleRow(array(ColumnKeys::CATEGORIES => 'Default Category/Testcategory'));

        // process the import operation
        $this->processImport();

        // make sure, the flag file for a successfull import exists
        $this->assertCount(1, glob(sprintf('%s/*/%s.imported', $this->getTmpDir(), basename($filename))));

        // initialize the product bunch processor instance
        $productBunchProcessor = $this->getProductBunchProcessor();

        // try to load the imported product by its SKU
        $product = $productBunchProcessor->loadProduct($sku = 'TEST-SKU-000001');

        // try to load the category product relations by their SKU and count them
        $categoryProducts = $productBunchProcessor->getCategoryProductsBySku($sku);
        $this->assertCount(1, $categoryProducts);

        // load the second found category product relation
        $categoryProduct = array_shift($categoryProducts);

        // assert the values
        $this->assertSame($product[MemberNames::ENTITY_ID], $categoryProduct[MemberNames::PRODUCT_ID]);
        $this->assertSame($categoryId, (integer) $categoryProduct[MemberNames::CATEGORY_ID]);
    }

    /**
     * Test's the add-update operation with a simple product with a changed category relation.
     *
     * @return void
     */
    public function testAddUpdateWithSimpleProductAndChangedCategoryRelation()
    {

        // create two new categories
        $this->createCategory('Testcategory 1');
        $categoryId = (integer) $this->createCategory('Testcategory 2');

        // initialize the array for for the names of the import files
        $filenames = array(
            $this->prepareFileWithSingleRow(array(ColumnKeys::CATEGORIES => 'Default Category/Testcategory 1')),
            $this->prepareFileWithSingleRow(array(ColumnKeys::CATEGORIES => 'Default Category/Testcategory 2'))
        );

        // invoke the import operation twice to import both files
        $this->processImport(sizeof($filenames));

        // make sure, the flag file for a successfull import exists
        foreach ($filenames as $filename) {
            $this->assertCount(1, glob(sprintf('%s/*/%s.imported', $this->getTmpDir(), basename($filename))));
        }

        // initialize the product bunch processor instance
        $productBunchProcessor = $this->getProductBunchProcessor();

        // try to load the imported product by its SKU
        $product = $productBunchProcessor->loadProduct($sku = 'TEST-SKU-000001');

        // try to load the category product relations by their SKU and count them
        $categoryProducts = $productBunchProcessor->getCategoryProductsBySku($sku);
        $this->assertCount(1, $categoryProducts);

        // load the second found category product relation
        $categoryProduct = array_shift($categoryProducts);

        // assert the values
        $this->assertSame($product[MemberNames::ENTITY_ID], $categoryProduct[MemberNames::PRODUCT_ID]);
        $this->assertSame($categoryId, (integer) $categoryProduct[MemberNames::CATEGORY_ID]);
    }
}
