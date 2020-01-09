@msi @add-update
Feature: Add/Update MSI
  To import MSI into my Magento 2 instance
  As an e-commerce manager
  I simply want to put CSV files with MSI into a directory on the server and they should be imported

  Rules:
    - Filename starts with "product-import-inventory-msi_*"
    - The files are available in folder "var/importexport"

Scenario: Add/Update MSI
  Given attribute sets have been imported
    And attributes have been imported
    And categories have been imported
    And products have been imported
    And files with MSI to be updated are available
    And the MSI import process has been started
   When the import process has been finished
   Then a success message has to be rendered
    And the magento index has been updated

@product-check
Scenario Outline: Check Products
  Given I am on the homepage
   When I go to <page>
   Then the response status code should be <code>
    And title, price and status are <title>, <price>, <status>

    Examples:
      | page                         | title                  | status         | price     | code |
      | "/joust-duffle-bag.html"     | "404 Not Found"        |                |           | 404  |
      | "/fusion-backpack.html"      | "Fusion Backpack"      | "IN STOCK"     | "59,00 €" | 200  |
      | "/strive-shoulder-pack.html" | "Strive Shoulder Pack" | "OUT OF STOCK" | "32,00 €" | 200  |