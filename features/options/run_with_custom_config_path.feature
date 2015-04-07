Feature: Running with custom config path
  As a developer
  I want to be able to specify custom path to the config file
  So I can have the config file elsewhere than default path

  Scenario: Running with custom config path
    Given there is a file "test/phpzone.yml" with:
      """
      extensions:
          PhpZone\PhpZone\Example\ExampleExtension2: ~

      """
    And there is a class in the "src/Example/ExampleExtension2.php" with:
      """
      <?php

      namespace PhpZone\PhpZone\Example;

      use PhpZone\PhpZone\Extension\Extension;
      use Symfony\Component\DependencyInjection\ContainerBuilder;
      use Symfony\Component\DependencyInjection\Definition;

      class ExampleExtension2 implements Extension
      {
          public function load(ContainerBuilder $container)
          {
              $definition = new Definition('Symfony\Component\Console\Command\Command');
              $definition->setArguments(array('example:command:2'));
              $definition->addTag('command');
              $container->setDefinition('example.command_2', $definition);
          }
      }

      """
    When I run phpzone with the "--config=test/phpzone.yml" option
    Then I should have "example:command:2" command
