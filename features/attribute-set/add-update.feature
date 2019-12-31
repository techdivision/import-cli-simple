@add-update
Feature: Add/Update Attribute-Sets
  To import attribute sets into my Magento 2 instance
  As an E-Commerce manager
  I simply want to put a CSV file with the attribute sets into a directory on the server and they should be imported

  Rules:
    - Filename starts with attribute-set-import_*
    - The file is available in folder "var/importexport"

Background:
  Given a third party system has copied the file "vendor/techdivision/import-sample-data/generic/data/attributes-set/add-update/attribute-set-import_20190104-114000_01.csv" into the import folder "var/importexport"

Scenario: Add/Update Attribute-Sets
  Given that a new file "var/importexport/attribute-set-import_20190104-114000_01.csv" containing data is available
    And the command "bin/import-simple import:create:ok-file" has been executed
    And the command "bin/import-simple import:attributes:set" has been executed