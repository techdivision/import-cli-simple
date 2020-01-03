@product @replace
Feature: Replace Products
  To replace products into my Magento 2 instance
  As an e-commerce manager
  I simply want to put CSV files with product into a directory on the server and they should be replaced

  Rules:
    - Filename starts with "product-import_*"
    - The files are available in folder "var/importexport"

Scenario: Replace Products
  Given attribute sets have been imported
    And attributes have been imported
    And categories have been imported
    And files with products to be replaced are available
    And the product replacement process has been started
   When the replacement process has been finished
   Then a success message has to be rendered

Scenario Outline: Check Products
  Given the magento index has been updated
   When I go to <page>
   Then the response status code should be <code>
    And title and price are <title>, <price>

    Examples:
      | page                     | title              | price    | code |
      | "/joust-duffle-bag.html" | "Joust Duffle Bag" | "$34.00" | 200  |
      | "/fusion-backpack.html"  | "Fusion Backpack"  | "$59.00" | 200  |
      | "/driven-backpack.html"  | "Driven Backpack"  | "$36.00" | 200  |