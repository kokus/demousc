@javascript
@api
Feature: Admin dashboard

  Scenario: An admin can access to the dashboard
    Given I am logged in as a "administrator"
    When I go to "admin/dashboard"
    Then I should see the text "subsite1"

  Scenario: An admin can add new site
    Given I am logged in as a "administrator"
    When I go to "admin/dashboard"
    Then I should see the link "Add Site"
    When I click "Add Site"
    And I fill in "Site ID" with "New test site"
    And I press "Create"
    Then I should see the error message containing "The machine-readable name must contain only lowercase letters, numbers, and hyphens."
    When I fill in "Site ID" with "new-test-site"
    And I press "Create"
    Then I should see the success message containing "new-test-site was created successfully."
    And I should see the text "new-test-site" in the "content" region
