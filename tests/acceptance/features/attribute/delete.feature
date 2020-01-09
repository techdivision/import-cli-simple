@2.2 @2.3 @ce @ee @attribute @delete
Feature: Delete Attributes
  To delete attributes from my Magento 2 instance
  As an e-commerce manager
  I simply want to put CSV files with attributes into a directory on the server and they should be deleted

  Rules:
    - Filename starts with "attribute-import_*"
    - The files are available in folder "var/importexport"

Scenario: Delete Attributes
  Given files with attributes to be deleted are available
    And the attribute deletion process has been started
   When the process has been finished
   Then a success message has to be rendered