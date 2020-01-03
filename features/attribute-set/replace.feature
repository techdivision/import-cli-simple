@attribute-set @replace
Feature: Replace Attribute-Sets
  To replace attribute sets into my Magento 2 instance
  As an e-commerce manager
  I simply want to put CSV files with attribute sets into a directory on the server and they should be replaced

  Rules:
    - Filename starts with "attribute-set-import_*"
    - The files are available in folder "var/importexport"

Scenario: Replace Attribute-Sets
  Given files with attribute sets to be replaced are available
    And the attribute set replacement process has been started
   When the attribute set replacement process has been finished
   Then a success message has to be rendered