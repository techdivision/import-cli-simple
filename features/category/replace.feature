@category @replace
Feature: Replace Categories
  To replace categories into my Magento 2 instance
  As an e-commerce manager
  I simply want to put CSV files with categories into a directory on the server and they should be replaced

  Rules:
    - Filename starts with "category-import_*"
    - The files are available in folder "var/importexport"

Scenario: Replace Categories
  Given files with categories to be replaced are available
    And the category replacement process has been started
   When the category replacement process has been finished
   Then a success message has to be rendered