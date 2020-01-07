@attribute @add-update
Feature: Add/Update Attributes
  To import attributes into my Magento 2 instance
  As an e-commerce manager
  I simply want to put CSV files with attributes into a directory on the server and they should be imported

  Rules:
    - Filename starts with "attribute-import_*"
    - The files are available in folder "var/importexport"
    
Scenario: Add/Update Attributes
  Given attribute sets have been imported
    And files with attributes to be updated are available
    And the attribute import process has been started
   When the process has been finished
   Then a success message has to be rendered