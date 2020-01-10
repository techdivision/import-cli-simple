@2.2 @2.3 @ce @ee @customer-address @add-update
Feature: Add/Update Customer Addresses
  To import customer addresses into my Magento 2 instance
  As an e-commerce manager
  I simply want to put CSV files with customer addresses into a directory on the server and they should be imported

  Rules:
    - Filename starts with "customer-address-import_*"
    - The files are available in folder "var/importexport"

Scenario: Add/Update Customer Addresses
  Given files with customer addresses to be updated are available
    And the customer address import process has been started
   When the customer address import process has been finished
   Then a success message has to be rendered
    And the magento customer grid index has been updated