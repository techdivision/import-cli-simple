@2.2 @2.3 @ce @ee @product @add-update
Feature: Add/Update Products
  To import products into my Magento 2 instance
  As an e-commerce manager
  I simply want to put CSV files with products into a directory on the server and they should be imported

  Rules:
    - Filename starts with "product-import_*"
    - The files are available in folder "var/importexport"

Scenario: Add/Update Products
  Given attribute sets have been imported
    And attributes have been imported
    And categories have been imported
    And files with products to be updated are available
    And the product import process has been started
   When the import process has been finished
   Then a success message has to be rendered
    And the magento index has been updated

@product-check
Scenario Outline: Check Products
  Given I am on the homepage
   When I go to <page>
   Then the response status code should be <code>
    And title and price are <title>, <price>

    Examples:
      | page                     | title             | price     | code |
      | "/joust-duffle-bag.html" | "404 Not Found"   |           | 404  |
      | "/fusion-backpack.html"  | "Fusion Backpack" | "59,00 €" | 200  |
      | "/driven-backpack.html"  | "Driven Backpack" | "36,00 €" | 200  |