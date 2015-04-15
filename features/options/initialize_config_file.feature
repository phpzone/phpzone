Feature: Initialize a sample of the configuration file
  As a developer
  I want to be able to auto-generate a sample of the configuration file
  So I don't need to create the configuration file manually and I can see examples

  Scenario: Run the initialization with not existing current configuration file
    Given there is no config file
    And there is a file "src/Config/templates/phpzone.example.yml" with:
      """
      #imports:
      #    - { resource: relative\path\to\another\config\file.yml }

      #extensions:
      #    Namespace\FooExtension: ~
      #    Namespace\BarExtension:
      #        some_key: some_value

      """
    When I run phpzone with the "init" option
    Then I should have a file "phpzone.yml" with:
      """
      #imports:
      #    - { resource: relative\path\to\another\config\file.yml }

      #extensions:
      #    Namespace\FooExtension: ~
      #    Namespace\BarExtension:
      #        some_key: some_value

      """

  Scenario: Run the initialization with existing current configuration file
    Given there is a config file with:
      """
      """
    And there is a file "src/Config/templates/phpzone.example.yml" with:
      """
      """
    When I run phpzone with the "init" option
    Then I should see an error
