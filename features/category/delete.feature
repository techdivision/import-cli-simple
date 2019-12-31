@delete
Feature: Delete Categories
  To import categories into my Magento 2 instance
  As an E-Commerce manager
  I simply want to put a CSV file with the categories into a directory on the server and they should be imported

  Rules:
    - Filename starts with category-import_*
    - The file is available in folder "var/importexport"

Background:
  Given a third party system has copied the file "vendor/techdivision/import-sample-data/generic/data/categories/add-update/category-import_20161024-194026_01.csv" into the import folder "var/importexport"

Scenario: Add/Update Categories
  Given that a new file "var/importexport/category-import_20161024-194026_01.csv" containing data is available
    And the command "bin/import-simple import:create:ok-file" has been executed
   Then the command "bin/import-simple import:categories delete" has been executed