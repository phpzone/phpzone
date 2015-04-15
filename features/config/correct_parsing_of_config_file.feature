Feature: Correct parsing of config file
  As a developer
  I want to be able to parse a config file correctly
  So I can be sure that a configuration is done correctly

  Scenario: Running with correct config file
    Given there is a config file with:
      """
      extensions:
          PhpZone\PhpZone\Example\Example3Extension: ~

      """
    And there is a class in the "src/Example/Example3Extension.php" with:
      """
      <?php

      namespace PhpZone\PhpZone\Example;

      use PhpZone\PhpZone\Extension\AbstractExtension;
      use Symfony\Component\DependencyInjection\ContainerBuilder;
      use Symfony\Component\DependencyInjection\Definition;

      class Example3Extension extends AbstractExtension
      {
          public function load(array $config, ContainerBuilder $container)
          {
          }
      }

      """
    When I run phpzone
    Then I should not see any error

  Scenario: Running with a not existing config file and without a custom location of the file
    Given there is no config file
    When I run phpzone
    Then I should not see any error

  Scenario: Running with empty config file
    Given there is a config file with:
      """
      """
    When I run phpzone
    Then I should not see any error

  Scenario: Running with a config file in wrong format
    Given there is a config file with:
      """
      wrong_format
      """
    When I run phpzone
    Then I should see an error

  Scenario: Running with a config file which contains the "extensions" option in wrong format
    Given there is a config file with:
      """
      extensions: value
      """
    When I run phpzone
    Then I should see an error
