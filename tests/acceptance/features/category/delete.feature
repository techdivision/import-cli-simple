@2.2 @2.3 @ce @ee @category @delete
Feature: Delete Categories
  To delete categories from my Magento 2 instance
  As an e-commerce manager
  I simply want to put CSV files with categories into a directory on the server and they should be deleted

  Rules:
    - Filename starts with "category-import_*"
    - The files are available in folder "var/importexport"

Scenario: Delete Categories
  Given files with categories to be deleted are available
    And the category deletion process has been started
   When the category deletion process has been finished
   Then a success message has to be rendered