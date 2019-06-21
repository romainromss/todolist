@login

Feature: Only register user can login

Background:
    Given I am on "/login"
    And I load the following user
        | username | password   | email           | roles      |
        | admin    | pass       | admin@gmail.com | ROLE_ADMIN |
        | user     | pass       | user@gmail.com  | ROLE_USER  |
    Scenario: [fail] submit form without data
        When I press "Se connecter"
        Then I should see "Mauvais identifiant."

    Scenario: [fail] submit form with invalid password
        When I fill in "Nom d'utilisateur :" with "admin"
        And I fill in "Mot de passe :" with "hello"
        And I press "Se connecter"
        Then I should see "Mot de passe incorrect."

    Scenario: [success] submit form with user credentials
        When I fill in "Nom d'utilisateur :" with "user"
        And I fill in "Mot de passe :" with "pass"
        And I press "Se connecter"
        Then I should be on "/"
        And I should see "Se déconnecter"
        And I should see "Tâches"
        And user with username "user" should exist in database and have the following role "ROLE_USER"

    Scenario: [success] submit form with admin credentials
        When I fill in "Nom d'utilisateur :" with "admin"
        And I fill in "Mot de passe :" with "pass"
        And I press "Se connecter"
        Then I should be on "/"
        And I should see "Se déconnecter"
        And I should see "Utilisateurs"
        And I should see "Tâches"
        And user with username "admin" should exist in database and have the following role "ROLE_ADMIN"
