 @product @add-update
Feature: Add/Update Products
  To import products into my Magento 2 instance
  As an E-Commerce manager
  I simply want to put a CSV file with the products into a directory on the server and they should be imported

  Rules:
    - Filename starts with product-import_*
    - The file is available in folder "var/importexport"

Background:
  Given a third party system has copied the file "vendor/techdivision/import-sample-data/generic/data/products/add-update/product-import_20161021-161909_01.csv" into the import folder "var/importexport"
    And a third party system has copied the file "vendor/techdivision/import-sample-data/generic/data/products/add-update/product-import_20161021-161909_02.csv" into the import folder "var/importexport"
    And a third party system has copied the file "vendor/techdivision/import-sample-data/generic/data/products/add-update/product-import_20161021-161909_03.csv" into the import folder "var/importexport"
    And a third party system has copied the file "vendor/techdivision/import-sample-data/generic/data/products/add-update/product-import_20161021-161909_04.csv" into the import folder "var/importexport"
    
Scenario: Add/Update Attributes
  Given that a new file "var/importexport/product-import_20161021-161909_01.csv" containing data is available
    And that a new file "var/importexport/product-import_20161021-161909_02.csv" containing data is available
    And that a new file "var/importexport/product-import_20161021-161909_03.csv" containing data is available
    And that a new file "var/importexport/product-import_20161021-161909_04.csv" containing data is available
    And the command "bin/import-simple import:create:ok-file" has been executed
    And the command "bin/import-simple import:products" has been executed
    And the Magento command "bin/magento indexer:reindex" has been executed
   Then the page "/fusion-backpack.html" should be available
    And the page "/fusion-backpack.html" should contain the price "$59.00"