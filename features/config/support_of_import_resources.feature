Feature: Support of importing resources in the config file
  As a developer
  I want to be able to specify other resource to import
  So I can have a separated configuration within more files

  Scenario: Running with a config file containing resource to import
    Given there is a config file with:
      """
      imports:
          - { resource: relative_path/phpzone-separate-7b.yml }
      extensions:
          PhpZone\PhpZone\Example\Example7aExtension: ~

      """
    And there is a file "relative_path/phpzone-separate-7b.yml" with:
      """
      extensions:
          PhpZone\PhpZone\Example\Example7bExtension: ~

      """
    And there is a class in the "src/Example/Example7aExtension.php" with:
      """
      <?php

      namespace PhpZone\PhpZone\Example;

      use PhpZone\PhpZone\Extension\AbstractExtension;
      use Symfony\Component\DependencyInjection\ContainerBuilder;
      use Symfony\Component\DependencyInjection\Definition;

      class Example7aExtension extends AbstractExtension
      {
          public function load(array $config, ContainerBuilder $container)
          {
              $definition = new Definition('Symfony\Component\Console\Command\Command');
              $definition->setArguments(array('example:command:7a'));
              $definition->addTag('command');
              $container->setDefinition('example.command_7a', $definition);
          }
      }

      """
    And there is a class in the "src/Example/Example7bExtension.php" with:
      """
      <?php

      namespace PhpZone\PhpZone\Example;

      use PhpZone\PhpZone\Extension\AbstractExtension;
      use Symfony\Component\DependencyInjection\ContainerBuilder;
      use Symfony\Component\DependencyInjection\Definition;

      class Example7bExtension extends AbstractExtension
      {
          public function load(array $config, ContainerBuilder $container)
          {
              $definition = new Definition('Symfony\Component\Console\Command\Command');
              $definition->setArguments(array('example:command:7b'));
              $definition->addTag('command');
              $container->setDefinition('example.command_7b', $definition);
          }
      }

      """
    When I run phpzone
    Then I should have "example:command:7a" command
    And I should have "example:command:7b" command

