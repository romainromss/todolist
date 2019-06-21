@listUser

Feature: after authentication with admin credentials, i need to be able to got list user.

  Background:
    Given I am on "/login"
    And I load the following user
      | username | password | email             | roles      |
      | user1    | user1    | user1@gmail.com   | ROLE_ADMIN |
      | user2    | user2    | user2@gmail.com   | ROLE_USER  |
      | admin    | admin    | admin@gmail.com   | ROLE_ADMIN |

  Scenario: [fail] as a non-admin user, i can't have access to list user
    When I fill in "Nom d'utilisateur :" with "user2"
    And I fill in "Mot de passe :" with "user2"
    And I press "Se connecter"
    Then I am on "/users"
    And the response status code should be 403

  Scenario: [success] as admin user, i can have access to list user
    When I fill in "Nom d'utilisateur :" with "admin"
    And I fill in "Mot de passe :" with "admin"
    And I press "Se connecter"
    And I am on "/users"
    And I should see "user1"
    And I should see "user1@gmail.com"
    And I should see "user2"
    And I should see "user2@gmail.com"
    And I should see "admin"
    And I should see "admin@gmail.com"
    And I should see 3 "a.btn-success" elements
    And I should see 3 "table.table tbody tr" elements
    And the response status code should be 200
