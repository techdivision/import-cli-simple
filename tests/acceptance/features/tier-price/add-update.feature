@2.2 @2.3 @ce @ee @tier-price @add-update
Feature: Add/Update Tier Prices
  To import tier prices into my Magento 2 instance
  As an e-commerce manager
  I simply want to put CSV files with tier prices into a directory on the server and they should be imported

  Rules:
    - Filename starts with "product-import-tier-price_*"
    - The files are available in folder "var/importexport"

Scenario: Add/Update Tier Prices
  Given attribute sets have been imported
    And attributes have been imported
    And categories have been imported
    And products have been replaced
    And files with tier prices to be updated are available
    And the tier price import process has been started
   When the import process has been finished
   Then a success message has to be rendered
    And the magento index has been updated

@product-check
Scenario: Check Tier Price for Joust Duffle Bag
  Given I am on the homepage
   When I go to "/joust-duffle-bag.html"
   Then the response status code should be 200
    And I should see "Buy 4 for 29,00 € each and save 12%"

@product-check
Scenario: Check Tier Price for Drive Shoulder Pack
  Given I am on the homepage
   When I go to "/strive-shoulder-pack.html"
   Then the response status code should be 200
    And I should see "Buy 3 for 23,04 € each and save 28%"