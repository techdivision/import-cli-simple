@2.2 @2.3 @ce @ee @attribute @replace
Feature: Replace Attributes
  To replace attributes into my Magento 2 instance
  As an e-commerce manager
  I simply want to put CSV files with attributes into a directory on the server and they should be replaced

  Rules:
    - Filename starts with "attribute-import_*"
    - The files are available in folder "var/importexport"

Scenario: Replace Attributes
  Given attribute sets have been imported
    And files with attributes to be replaced are available
    And the attribute replacement process has been started
   When the process has been finished
   Then a success message has to be rendered