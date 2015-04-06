Feature: Registering a command by an extension
  As a developer
  I want to register a command by an extension
  So I can run custom commands

  Scenario: Register a command by extension
    Given there is a config file with:
      """
      extensions:
          PhpZone\PhpZone\Example\ExampleExtension1: ~

      """
    And there is a class in the "src/Example/ExampleExtension1.php" with:
      """
      <?php

      namespace PhpZone\PhpZone\Example;

      use PhpZone\PhpZone\Extension\Extension;
      use Symfony\Component\DependencyInjection\ContainerBuilder;
      use Symfony\Component\DependencyInjection\Definition;

      class ExampleExtension1 implements Extension
      {
          public function load(ContainerBuilder $container)
          {
              $definition = new Definition('\PhpZone\PhpZone\Example\ExampleCommand1');
              $definition->addTag('command');
              $container->setDefinition('example.command_1', $definition);
          }
      }

      """
    And there is a class in the "src/Example/ExampleCommand1.php" with:
      """
      <?php

      namespace PhpZone\PhpZone\Example;

      use Symfony\Component\Console\Command\Command;

      class ExampleCommand1 extends Command
      {
          public function __construct()
          {
              parent::__construct('example:command:1');
          }
      }

      """
    When I run phpzone
    Then I should have "example:command:1" command
