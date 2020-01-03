@product @delete
Feature: Delete Products
  To delete products from my Magento 2 instance
  As an e-commerce manager
  I simply want to put CSV files with products into a directory on the server and they should be deleted

  Rules:
    - Filename starts with "product-import_*"
    - The files are available in folder "var/importexport"
    
Scenario: Add/Update Products
  Given files with products to be deleted are available
    And the product deletion process has been started
   When the product deletion process has been finished
   Then a success message has to be rendered

Scenario Outline: Check Products
  Given the magento index has been updated
   When I go to <page>
   Then the response status code should be <code>
    And title and price are <title>, <price>

    Examples:
      | page                     | title           | price | code |
      | "/fusion-backpack.html"  | "404 Not Found" |       | 404  |
      | "/driven-backpack.html"  | "404 Not Found" |       | 404  |