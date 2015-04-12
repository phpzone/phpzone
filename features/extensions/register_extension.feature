Feature: Registering an extension
  As a developer
  I want to register an extension
  So I can run custom commands

  Scenario: Register an extension
    Given there is a config file with:
      """
      extensions:
          PhpZone\PhpZone\Example\Example4Extension: ~

      """
    And there is a class in the "src/Example/Example4Extension.php" with:
      """
      <?php

      namespace PhpZone\PhpZone\Example;

      use PhpZone\PhpZone\Extension\AbstractExtension;
      use Symfony\Component\DependencyInjection\ContainerBuilder;
      use Symfony\Component\DependencyInjection\Definition;

      class Example4Extension extends AbstractExtension
      {
          public function load(array $config, ContainerBuilder $container)
          {
          }
      }

      """
    When I run phpzone
    Then I should not see any error

  Scenario: Register an extension which does not exist
    Given there is a config file with:
      """
      extensions:
          PhpZone\PhpZone\Example\NotExistsExtension: ~

      """
    When I run phpzone
    Then I should see an error

  Scenario: Register an extension which does not have valid interface
    Given there is a config file with:
      """
      extensions:
          PhpZone\PhpZone\Example\Example5Extension: ~

      """
    And there is a class in the "src/Example/Example5Extension.php" with:
      """
      <?php

      namespace PhpZone\PhpZone\Example;

      use Symfony\Component\DependencyInjection\ContainerBuilder;
      use Symfony\Component\DependencyInjection\Definition;

      class Example5Extension
      {
          public function load(array $config, ContainerBuilder $container)
          {
          }
      }

      """
    When I run phpzone
    Then I should see an error
