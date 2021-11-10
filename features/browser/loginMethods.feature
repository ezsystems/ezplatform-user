@IbexaOSS @IbexaContent @IbexaExperience @IbexaCommerce
Feature: As an user I can log in using email address

  @APIUser:admin
  Scenario: User can log in on frontend using email
    Given I create a user "testadmin" with last name "User" in group "Administrator users" with email "testadmin@example.com"
    And I am viewing the pages on siteaccess "site" as "testadmin@example.com"
    When I go to "users"
    Then the url should match "/Users"
    And I should see "Users"

  @APIUser:admin @javascript
  Scenario: User can log in to backoffice using email
    Given I create a user "testadmin2" with last name "User" in group "Administrator users" with email "testadmin2@example.com"
    And I open Login page in admin SiteAccess
    When I log in as "testadmin2@example.com"
    Then I should be on Dashboard page
