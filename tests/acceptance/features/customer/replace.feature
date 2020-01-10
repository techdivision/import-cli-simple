@2.2 @2.3 @ce @ee @customer @replace
Feature: Replace Customers
  To replace customers into my Magento 2 instance
  As an e-commerce manager
  I simply want to put CSV files with customers into a directory on the server and they should be replaced

  Rules:
    - Filename starts with "customer-import_*"
    - The files are available in folder "var/importexport"

Scenario: Replace Customers
  Given customers have been imported
    And files with customers to be replaced are available
    And the customer replacement process has been started
   When the customer replacement process has been finished
   Then a success message has to be rendered
    And the magento customer grid index has been updated

@customer-check
Scenario Outline: Check Customer Login
  Given I am on the homepage
    And I go to "/customer/account"
   When I fill in <email> for "email"
    And I fill in <password> for "pass"
    And I press "send2"
   Then the response status code should be <code>
    And I should be on "/customer/account/index/"
    And I should see "Jon Doe"

    Examples:
      | email                 | password      | code |
      | "jondoe@example.com" | "appserver.i0" | 200  |