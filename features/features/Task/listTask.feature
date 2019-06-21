@listTask

Feature: after authentication, i need to be able to get list task.

  Background:
    Given I am on "/login"
    And I load the following user
      | username | password | email            | roles      |
      | user4    | 12345678 | admin@gmail.com  | ROLE_ADMIN |
    And I load the following task
      | title   | content |
      | tache 3 | content |
      | tache 4 | content |

  Scenario: [success] list task done is empty
    And I fill in "Nom d'utilisateur :" with "user4"
    And I fill in "Mot de passe :" with "12345678"
    And I press "Se connecter"
    And I am on "/"
    And I should see "Consulter la liste des tâches terminées"
    And I am on "/tasks?search=done"
   And I should see "Créer une tâche"

  Scenario: [success] with admin credentials, i should see button toggle for all tasks. And i can update all tasks by their title link.
    And I fill in "Nom d'utilisateur :" with "user4"
    And I fill in "Mot de passe :" with "12345678"
    And I press "Se connecter"
    And I am on "/"
    And I am on "/tasks?search=waiting"
    And I should see "tache 3"
    And I should see "tache 4"
    And I should see "Créer une tâche"
    And I should see "Marquer comme terminée"

  Scenario: [success] with user credentials, i should only see button to delete and toggle for my tasks. And i can only update my tasks by title link.
    And I fill in "Nom d'utilisateur :" with "user2"
    And I fill in "Mot de passe :" with "12345678"
    And I press "Se connecter"
    And I am on "/tasks"
    Then I should be on "/tasks?search=waiting"
    And I should see "tache 3"
    And I should see "tache 4"
    And I should see "Créer une tâche"
    And I should see "Supprimer"
    And I should see "Marquer comme terminée"
