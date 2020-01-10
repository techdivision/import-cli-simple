@2.2 @2.3 @ce @ee @customer @delete
Feature: Delete Customer
  To delete customers from into my Magento 2 instance
  As an e-commerce manager
  I simply want to put CSV files with customers into a directory on the server and they should be deleted

  Rules:
    - Filename starts with "customer-import_*"
    - The files are available in folder "var/importexport"

Scenario: Delete Customers
  Given files with customers to be deleted are available
    And the customer deletion process has been started
   When the customer deletion process has been finished
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
    And the url should match "/customer/account/login/referer"
    And I should see "Sign In"

    Examples:
      | email                 | password      | code |
      | "jondoe@example.com" | "appserver.i0" | 200  |