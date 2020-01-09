@2.2 @2.3 @ce @ee @attribute-set @delete
Feature: Delete Attribute-Sets
  To delete attribute from into my Magento 2 instance
  As an e-commerce manager
  I simply want to put CSV files with attribute sets into a directory on the server and they should be deleted

  Rules:
    - Filename starts with "attribute-set-import_*"
    - The files are available in folder "var/importexport"

Scenario: Delete Attribute-Sets
  Given files with attribute sets to be deleted are available
    And the attribute set deletion process has been started
   When the attribute set deletion process has been finished
   Then a success message has to be rendered