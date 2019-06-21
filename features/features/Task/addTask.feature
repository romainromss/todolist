@addTask

Feature: after authentication, i need to be able to add task.

  Background:
    Given I am on "/login"
    And I load the following user
      | username | password | email           | roles      |
      | admin    | admin    | admin@gmail.com | ROLE_ADMIN |
    And I fill in "Nom d'utilisateur :" with "admin"
    And I fill in "Mot de passe :" with "admin"
    And I press "Se connecter"
    And I should be on "/"
    And I am on "/tasks/create"

  Scenario: [fail] submit form with no data
    When I press "Ajouter"
    Then I should see "Le titre de la tâche doit être renseigné."
    And I should see "Le contenu de la tâche doit être renseigné."

  Scenario: [fail] submit form with title over 25 characters
    When I fill in "task_title" with "myverylongtitlemyverylongtitlemyverylongtitle"
    And I fill in "task_content" with "Ceci est la description de la tâche"
    And I press "Ajouter"
    Then I should see "Le titre de la tâche ne doit pas comporter plus de 25 caractères."

  Scenario: [success] submit form with valid data
    When I fill in "task_title" with "Mon titre"
    And I fill in "task_content" with "Ceci est la description de la tâche"
    And I press "Ajouter"
    Then I should see "La tâche a été bien été ajoutée."
    And I should be on "/tasks"
