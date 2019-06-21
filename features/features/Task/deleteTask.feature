@deleteTask

Feature: after authentication, i need to be able to delete task.

  Background:
    Given I am on "/login"
    And I load the following user
      | username | password | email             | roles      |
      | user4    | user4     | admin@gmail.com  | ROLE_ADMIN |
    And I have saved one task
    And task with title "tache 2" should have following id "85d05a8b-c8c8-46b8-80bd-a7b103c9ac02"

  Scenario: [fail] trying to delete no exist task
    And I fill in "Nom d'utilisateur :" with "user4"
    And I fill in "Mot de passe :" with "user4"
    And I press "Se connecter"
    And I am on "/tasks/85d05a8b-c8c8-46b8-80bd-a7b103a9ac02/delete"
    And I should see "La tâche demandée n'existe pas."
    And I am on "/tasks"

  Scenario: [fail] trying to delete task without permission
    And I fill in "Nom d'utilisateur :" with "user7"
    And I fill in "Mot de passe :" with "12345678"
    And I press "Se connecter"
    And I am on "tasks/85d05a8b-c8c8-46b8-80bd-a7b103c9ac02/delete"
    And I should see "Vous ne pouvez pas supprimer cette tâche."
    And I am on "/tasks"

  Scenario: [success] with user4 credential, i should be able to delete the task create by this user
   And I fill in "Nom d'utilisateur :" with "user2"
   And I fill in "Mot de passe :" with "12345678"
   And I press "Se connecter"
   And I am on "/tasks"
   And I should see "tache 2"
   And I press "Supprimer"
   And I should see "La tâche a bien été supprimée."
   And I am on "/tasks"
   And task with title "tache 2" should not exist in database

