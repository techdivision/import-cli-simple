 @attribute @add-update
Feature: Add/Update Attributes
  To import attributes into my Magento 2 instance
  As an E-Commerce manager
  I simply want to put a CSV file with the attributes into a directory on the server and they should be imported

  Rules:
    - Filename starts with attribute-import_*
    - The file is available in folder "var/importexport"

Background:
  Given a third party system has copied the file "vendor/techdivision/import-sample-data/generic/data/attributes/add-update/attribute-import_20170428-124902_01.csv" into the import folder "var/importexport"
    
Scenario: Add/Update Attributes
  Given that a new file "var/importexport/attribute-import_20170428-124902_01.csv" containing data is available
    And the command "bin/import-simple import:create:ok-file" has been executed
    And the command "bin/import-simple import:attributes" has been executed