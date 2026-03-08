Feature: Enroll in a training
  In order to participate in a course
  As an authenticated student
  I want to enroll in an available training session

  Background:
    Given I am logged in as a "student"

  Scenario: Successfully enrolling in a new training
    Given a training titled "Domain Driven Design 101" exists
    When I send a "POST" request to "/platinum/v1/trainings/enroll" with:
      | training_id | 101 |
    Then the response status should be 201
    And the response should contain "enrolled"
    And the training "Domain Driven Design 101" should appear in my portal