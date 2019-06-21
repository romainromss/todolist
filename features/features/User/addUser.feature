@addUser

Feature: Check if I can add user

Background:
  Given I am on "/login"
  And I load the following user
    | username | password | email             | roles      |
    | admin    | pass     | admin@gmail.com   | ROLE_ADMIN |

  And I fill in "Nom d'utilisateur :" with "admin"
  And I fill in "Mot de passe :" with "pass"
  And I press "Se connecter"
  And I should be on "/"
  And I follow "Utilisateurs"
  And I follow "Créer un utilisateur"
  And I should be on "/users/create"

  Scenario: [fail] submit form with already exist username
    When I fill in "Nom d'utilisateur" with "admin"
    And I fill in "Mot de passe" with "pass"
    And I fill in "Tapez le mot de passe à nouveau" with "pass"
    And I fill in "Adresse email" with "user@gmail.com"
    And I select "Utilisateur" from "Roles"
    And I press "Ajouter"

  Scenario: [fail] submit form with already exist email
    When I fill in "Nom d'utilisateur" with "johndoe"
    And I fill in "Mot de passe" with "pass"
    And I fill in "Tapez le mot de passe à nouveau" with "pass"
    And I fill in "Adresse email" with "admin@gmail.com"
    And I select "Utilisateur" from "Roles"
    And I press "Ajouter"

  Scenario: [fail] submit form with invalid confirm password
    When I fill in "Nom d'utilisateur" with "johndoe"
    And I fill in "Mot de passe" with "pass"
    And I fill in "Tapez le mot de passe à nouveau" with "hello"
    And I fill in "Adresse email" with "johndoe@gmail.com"
    And I select "Utilisateur" from "Roles"
    And I press "Ajouter"

  Scenario: [fail] submit form with empty data
    When I press "Ajouter"

  Scenario: [fail] submit form with invalid email
    When I fill in "Nom d'utilisateur" with "johndoe"
    And I fill in "Mot de passe" with "pass"
    And I fill in "Tapez le mot de passe à nouveau" with "pass"
    And I fill in "Adresse email" with "user.com"
    And I select "Utilisateur" from "Roles"
    And I press "Ajouter"

  Scenario: [success] submit form with valid data
    When I fill in "Nom d'utilisateur" with "johndoe"
    And I fill in "Mot de passe" with "passpass"
    And I fill in "Tapez le mot de passe à nouveau" with "passpass"
    And I fill in "Adresse email" with "johndoe@gmail.com"
    And I select "Utilisateur" from "Roles"
    And I press "Ajouter"
    And I should see "L'utilisateur a bien été ajouté."
    And I am on "/users"
    And user with username "johndoe" should exist in database and have the following role "ROLE_USER"
