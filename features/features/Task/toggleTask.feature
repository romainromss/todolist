@toggleTask

Feature: after authentication, i need to be able to toggle task.

  Background:
    Given I am on "/login"
    And I load the following user
      | username | password | email             | roles      |
      | user4    | user4     | admin@gmail.com  | ROLE_ADMIN |
    And I have saved one task
    And task with title "tache 2" should have following id "85d05a8b-c8c8-46b8-80bd-a7b103c9ac02"

  Scenario: [fail] trying to toggle no exist task
    And I fill in "Nom d'utilisateur :" with "user4"
    And I fill in "Mot de passe :" with "user4"
    And I press "Se connecter"
    And I am on "/tasks/85d05a8b-c8c8-46b8-80bd-a7b103a9ac02/toggle"
    And I should see "La tâche demandée n'existe pas."
    And I am on "/tasks"

  Scenario: [success] with user1 creadential, i should be able to toggle the task create by this user
    And I fill in "Nom d'utilisateur :" with "user2"
    And I fill in "Mot de passe :" with "12345678"
    And I press "Se connecter"
    And I am on "/tasks?search=waiting"
    And I am on "/tasks/85d05a8b-c8c8-46b8-80bd-a7b103c9ac02/toggle"
    And I should see "La tâche tache 2 a bien été marquée comme faite"
    Then I should be on "/tasks"
    And I am on "/tasks?search=done"
    And I am on "/tasks/85d05a8b-c8c8-46b8-80bd-a7b103c9ac02/toggle"
    And I should see "La tâche tache 2 a bien été marquée comme non faite"
    And I should see "tache 2"
    And I should see "Marquer comme terminée"

  Scenario: [success] with admin creadential, i should be able to toggle any task without must be owner
    And I save an other task
    And task with title "tache 3" should have following id "85d05a8b-c8c8-46b8-80bd-a7b103c9ac03"
    And I fill in "Nom d'utilisateur :" with "user2"
    And I fill in "Mot de passe :" with "12345678"
    And I press "Se connecter"
    And I am on "/tasks?search=waiting"
    And I am on "/tasks/85d05a8b-c8c8-46b8-80bd-a7b103c9ac03/toggle"
    And I should see "La tâche tache 3 a bien été marquée comme faite"
    Then I should be on "/tasks"
    And I am on "/tasks?search=done"
    And I am on "/tasks/85d05a8b-c8c8-46b8-80bd-a7b103c9ac03/toggle"
    And I should see "La tâche tache 3 a bien été marquée comme non faite"
    And I should see "tache 3"
    And I should see "Marquer comme terminée"
