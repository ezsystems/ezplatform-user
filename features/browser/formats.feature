@IbexaOSS @IbexaContent @IbexaExperience @IbexaCommerce
  Feature: As an user I can sign in using form

  @APIUser:admin
  Scenario: User is asked for setting a new password when his password is in an unsupported format
    Given I create a user "UnsupportedPasswordUser" with last name "User" in group "Anonymous Users"
    And a user "UnsupportedPasswordUser" has password in unsupported format
    When I am viewing the pages on siteaccess "site" as "UnsupportedPasswordUser"
    Then the url should match "/site/user/forgot-password/migration"
    And I should see "Your password has expired"
