@2.2 @2.3 @ce @ee @category @add-update
Feature: Add/Update Categories
  To import categories into my Magento 2 instance
  As an e-commerce manager
  I simply want to put CSV files with categories into a directory on the server and they should be imported

  Rules:
    - Filename starts with "category-import_*"
    - The files are available in folder "var/importexport"

Scenario: Add/Update Categories
  Given files with categories to be updated are available
    And the category import process has been started
   When the category import process has been finished
   Then a success message has to be rendered