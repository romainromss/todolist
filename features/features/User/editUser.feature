@editUser

Feature: after authentication, i need to be able to edit user.

  Background:
    Given I am on "/login"
    And I load the following user
      | username | password | email             | roles      |
      | user1    | user1    | user1@gmail.com   | ROLE_ADMIN |
      | user3    | user3    | user3@gmail.com   | ROLE_USER  |

    And user with username "user1" should have following id "aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa"

  Scenario: [fail] As non-admin, i trying to edit user
    When I fill in "Nom d'utilisateur :" with "user3"
    And I fill in "Mot de passe :" with "user3"
    And I press "Se connecter"
    And I am on "/users/bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb/edit"
    Then the response status code should be 403
#
  Scenario: [fail] Trying to edit not exist user
    When I fill in "Nom d'utilisateur" with "user1"
    And I fill in "Mot de passe" with "user1"
    And I press "Se connecter"
    And I am on "/users/aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaab/edit"
    And I should see "L'utilisateur demandé n'existe pas."
    And I am on "/users"
#
  Scenario: [fail] submit form with an already existing username
    When I fill in "Nom d'utilisateur :" with "user1"
    And I fill in "Mot de passe :" with "user1"
    And I press "Se connecter"
    And I am on "/users/aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa/edit"
    And the "Nom d'utilisateur" field should contain "user1"
    And the "Adresse email" field should contain "user1@gmail.com"
    And the "select[id='user_roles'] option[selected='selected']" element should contain "Administrateur"
    And I fill in "Nom d'utilisateur" with "admin"
    And I press "Modifier"
#
  Scenario: [fail] submit form with already existing email
    When I fill in "Nom d'utilisateur :" with "user1"
    And I fill in "Mot de passe :" with "user1"
    And I press "Se connecter"
    And I am on "/users/aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa/edit"
    And the "Nom d'utilisateur" field should contain "user1"
    And the "Adresse email" field should contain "user1@gmail.com"
    And the "select[id='user_roles'] option[selected='selected']" element should contain "Administrateur"
    And I fill in "Adresse email" with "admin@gmail.com"
    And I press "Modifier"
#
  Scenario: [fail] submit form without fill field password
    When I fill in "Nom d'utilisateur :" with "user1"
    And I fill in "Mot de passe :" with "user1"
    And I press "Se connecter"
    And I am on "/users/aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa/edit"
    And the "Nom d'utilisateur" field should contain "user1"
    And the "Adresse email" field should contain "user1@gmail.com"
    And the "select[id='user_roles'] option[selected='selected']" element should contain "Administrateur"
    And I press "Modifier"

  Scenario: [success] submit form with valid data
    Given I load a specific user
    And user with username "user2" should have following id "bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb"
    When I fill in "Nom d'utilisateur :" with "user1"
    And I fill in "Mot de passe :" with "user1"
    And I press "Se connecter"
    And I am on "/users/bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb/edit"
    When I fill in the following:
      | user_username        | user2           |
      | user_password_first  | useruser2       |
      | user_password_second | useruser2       |
    And I select "ROLE_ADMIN" from "user_roles"
    And I fill in the following:
      | user_email           | admin@gmail.com |
    And I press "Modifier"
    And I should see "L'utilisateur a bien été modifié"
    And I am on "/users"
    And I should see "user2"
    And I should see "admin@gmail.com"
    And user with username "user2" should exist in database and have the following role "ROLE_ADMIN"
