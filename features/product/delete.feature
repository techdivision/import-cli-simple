@product @delete
Feature: Delete Products
  To import products into my Magento 2 instance
  As an E-Commerce manager
  I simply want to put a CSV file with the products into a directory on the server and they should be deleted

  Rules:
    - Filename starts with product-import_*
    - The file is available in folder "var/importexport"
    
Scenario: Add/Update Products
  Given a third party system has copied the file "vendor/techdivision/import-sample-data/generic/data/products/add-update/product-import_20161021-161909_01.csv" into the import folder "var/importexport"
    And a third party system has copied the file "vendor/techdivision/import-sample-data/generic/data/products/add-update/product-import_20161021-161909_02.csv" into the import folder "var/importexport"
    And a third party system has copied the file "vendor/techdivision/import-sample-data/generic/data/products/add-update/product-import_20161021-161909_03.csv" into the import folder "var/importexport"
    And a third party system has copied the file "vendor/techdivision/import-sample-data/generic/data/products/add-update/product-import_20161021-161909_04.csv" into the import folder "var/importexport"
    And that a new file "var/importexport/product-import_20161021-161909_01.csv" containing data is available
    And that a new file "var/importexport/product-import_20161021-161909_02.csv" containing data is available
    And that a new file "var/importexport/product-import_20161021-161909_03.csv" containing data is available
    And that a new file "var/importexport/product-import_20161021-161909_04.csv" containing data is available
   When the command "bin/import-simple import:create:ok-file" has been executed
    And the command "bin/import-simple import:products delete" has been executed
    And the Magento command "bin/magento indexer:reindex" has been executed

Scenario Outline: Check Products
   Given is on our page
    When he is on <url> 
    Then the page should return status <status>

    Examples:
      | url                     | status |
      | "/fusion-backpack.html" | 404    |
      | "/driven-backpack.html" | 404    |