Feature: Dispatch and listen on custom events
  As a developer
  I want to be able to dispatch and listen on application events
  So I can make an application driven by events

  Scenario: Dispatch and listen on custom events
    Given there is a config file with:
      """
      extensions:
          PhpZone\PhpZone\Example\ExampleExtension: ~
      """
    And there is a class in the "src/Example/ExampleExtension" with:
      """
      <?php

      namespace PhpZone\PhpZone\Example;

      use PhpZone\PhpZone\Extension\AbstractExtension;
      use Symfony\Component\DependencyInjection\ContainerBuilder;
      use Symfony\Component\DependencyInjection\Definition;
      use Symfony\Component\DependencyInjection\Reference;

      class ExampleExtension extends AbstractExtension
      {
          public function load(array $config, ContainerBuilder $container)
          {
              $definition = new Definition('PhpZone\PhpZone\Example\ExampleCommand');
              $definition->setArguments(array('example:command', new Reference('event_dispatcher')));
              $definition->addTag('command');
              $container->setDefinition('example.command', $definition);

              $definition = new Definition('PhpZone\PhpZone\Example\ExampleListener');
              $definition->addTag(
                  'event_listener',
                  array(
                      'event' => 'phpzone.example',
                      'method' => 'onPhpzoneExample',
                  )
              );
              $container->setDefinition('example.listener', $definition);
          }
      }

      """
    And there is a class in the "src/Example/ExampleCommand" with:
      """
      <?php

      namespace PhpZone\PhpZone\Example;

      use Symfony\Component\Console\Command\Command;
      use Symfony\Component\Console\Input\InputInterface;
      use Symfony\Component\Console\Output\OutputInterface;
      use Symfony\Component\EventDispatcher\EventDispatcherInterface;
      use Symfony\Component\EventDispatcher\GenericEvent;

      class ExampleCommand extends Command
      {
          private $eventDispatcher;

          public function __construct($name, EventDispatcherInterface $eventDispatcher)
          {
              $this->eventDispatcher = $eventDispatcher;

              parent::__construct($name);
          }

          protected function execute(InputInterface $input, OutputInterface $output)
          {
              $event = new GenericEvent($output);
              $this->eventDispatcher->dispatch('phpzone.example', $event);
          }
      }

      """
    And there is a class in the "src/Example/ExampleListener" with:
      """
      <?php

      namespace PhpZone\PhpZone\Example;

      use Symfony\Component\EventDispatcher\Event;

      class ExampleListener
      {
          public function onPhpzoneExample(Event $event)
          {
              $output = $event->getSubject();
              $output->write('Example text');
          }
      }

      """
    When I run phpzone with "example:command"
    Then I should see:
      """
      Example text
      """