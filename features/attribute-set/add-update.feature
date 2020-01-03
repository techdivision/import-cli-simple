@attribute-set @add-update
Feature: Add/Update Attribute-Sets
  To import attribute sets into my Magento 2 instance
  As an e-commerce manager
  I simply want to put CSV files with attribute sets into a directory on the server and they should be imported

  Rules:
    - Filename starts with "attribute-set-import_*"
    - The files are available in folder "var/importexport"
    
Scenario: Add/Update Attribute Sets
  Given files with attribute sets to be updated are available
    And the attribute set import process has been started
   When the attribute set import process has been finished
   Then a success message has to be rendered