@editTask

Feature: after authentication, i need to be able to edit task.

  Background:
    Given I am on "/login"
    And I have saved one task
    And task with title "tache 2" should have following id "85d05a8b-c8c8-46b8-80bd-a7b103c9ac02"
    And I load the following user
      | username | password | email           | roles     |
      | user4    | 12345678 | admin@gmail.com | ROLE_USER |

  Scenario: [fail] trying to edit no exist task
    And I fill in "Nom d'utilisateur :" with "user2"
    And I fill in "Mot de passe :" with "12345678"
    And I press "Se connecter"
    And I am on "/tasks/85d05a8b-c8c8-46b8-80bd-a7b103c9ac01/edit"
    And I should see "La tâche demandée n'existe pas."

  Scenario: [fail] submit form with invalid data
    And I fill in "Nom d'utilisateur :" with "user2"
    And I fill in "Mot de passe :" with "12345678"
    And I press "Se connecter"
    Then I am on "/tasks/85d05a8b-c8c8-46b8-80bd-a7b103c9ac02/edit"
    When I fill in "task_title" with ""
    And I fill in "task_content" with ""
    And I press "Modifier"
    Then I should see "Le titre de la tâche doit être renseigné."
    Then I should see "Le contenu de la tâche doit être renseigné."

  Scenario: [success] submit form with valid data
    And I fill in "Nom d'utilisateur :" with "user2"
    And I fill in "Mot de passe :" with "12345678"
    And I press "Se connecter"
    Then I am on "/tasks/85d05a8b-c8c8-46b8-80bd-a7b103c9ac02/edit"
    When I fill in "task_title" with "ma super tache"
    And I fill in "task_content" with "La description de la tache 2 mis à jour"
    And I press "Modifier"
    And I should see "La tâche a bien été modifiée."
    Then I should be on "/tasks"
