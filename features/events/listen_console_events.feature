Feature: Listen on console events
  As a developer
  I want to be able to listen on console events
  So I can dynamically react on calling commands

  Scenario: Listen on console command and terminate events
    Given there is a config file with:
      """
      extensions:
          PhpZone\PhpZone\Example\ListenConsoleEvents\Example1Extension: ~
      """
    And there is a class in the "src/Example/ListenConsoleEvents/Example1Extension" with:
      """
      <?php

      namespace PhpZone\PhpZone\Example\ListenConsoleEvents;

      use PhpZone\PhpZone\Extension\AbstractExtension;
      use Symfony\Component\Console\ConsoleEvents;
      use Symfony\Component\DependencyInjection\ContainerBuilder;
      use Symfony\Component\DependencyInjection\Definition;

      class Example1Extension extends AbstractExtension
      {
          public function load(array $config, ContainerBuilder $container)
          {
              $definition = new Definition('PhpZone\PhpZone\Example\ListenConsoleEvents\Example1Command');
              $definition->setArguments(array('example:command:1'));
              $definition->addTag('command');
              $container->setDefinition('example.command_1', $definition);

              $definition = new Definition('PhpZone\PhpZone\Example\ListenConsoleEvents\Example1Listener');
              $definition->addTag(
                  'event_listener',
                  array(
                      'event' => ConsoleEvents::COMMAND,
                      'method' => 'onCommand',
                  )
              );
              $definition->addTag(
                  'event_listener',
                  array(
                      'event' => ConsoleEvents::TERMINATE,
                      'method' => 'onTerminate',
                  )
              );
              $container->setDefinition('example.listener_1', $definition);
          }
      }

      """
    And there is a class in the "src/Example/ListenConsoleEvents/Example1Command" with:
      """
      <?php

      namespace PhpZone\PhpZone\Example\ListenConsoleEvents;

      use Symfony\Component\Console\Command\Command;
      use Symfony\Component\Console\Input\InputInterface;
      use Symfony\Component\Console\Output\OutputInterface;

      class Example1Command extends Command
      {
          protected function execute(InputInterface $input, OutputInterface $output)
          {
          }
      }

      """
    And there is a class in the "src/Example/ListenConsoleEvents/Example1Listener" with:
      """
      <?php

      namespace PhpZone\PhpZone\Example\ListenConsoleEvents;

      use Symfony\Component\Console\Event\ConsoleCommandEvent;
      use Symfony\Component\Console\Event\ConsoleTerminateEvent;

      class Example1Listener
      {
          public function onCommand(ConsoleCommandEvent $event)
          {
              $command = $event->getCommand();
              $output = $event->getOutput();
              $output->writeln($command->getName() . ' command event');
          }

          public function onTerminate(ConsoleTerminateEvent $event)
          {
              $command = $event->getCommand();
              $output = $event->getOutput();
              $output->writeln($command->getName() . ' terminate event');
          }
      }

      """
    When I run phpzone with "example:command:1"
    Then I should see:
      """
      example:command:1 command event
      example:command:1 terminate event

      """

  Scenario: Listen on console exception event
    Given there is a config file with:
      """
      extensions:
          PhpZone\PhpZone\Example\ListenConsoleEvents\Example2Extension: ~
      """
    And there is a class in the "src/Example/Example2Extension" with:
      """
      <?php

      namespace PhpZone\PhpZone\Example\ListenConsoleEvents;

      use PhpZone\PhpZone\Extension\AbstractExtension;
      use Symfony\Component\Console\ConsoleEvents;
      use Symfony\Component\DependencyInjection\ContainerBuilder;
      use Symfony\Component\DependencyInjection\Definition;

      class Example2Extension extends AbstractExtension
      {
          public function load(array $config, ContainerBuilder $container)
          {
              $definition = new Definition('PhpZone\PhpZone\Example\ListenConsoleEvents\Example2Command');
              $definition->setArguments(array('example:command:2'));
              $definition->addTag('command');
              $container->setDefinition('example.command_2', $definition);

              $definition = new Definition('PhpZone\PhpZone\Example\ListenConsoleEvents\Example2Listener');
              $definition->addTag(
                  'event_listener',
                  array(
                      'event' => ConsoleEvents::EXCEPTION,
                      'method' => 'onException',
                  )
              );
              $container->setDefinition('example.listener_2', $definition);
          }
      }

      """
    And there is a class in the "src/Example/ListenConsoleEvents/Example2Command" with:
      """
      <?php

      namespace PhpZone\PhpZone\Example\ListenConsoleEvents;

      use Symfony\Component\Console\Command\Command;
      use Symfony\Component\Console\Input\InputInterface;
      use Symfony\Component\Console\Output\OutputInterface;

      class Example2Command extends Command
      {
          protected function execute(InputInterface $input, OutputInterface $output)
          {
              throw new \Exception('');
          }
      }

      """
    And there is a class in the "src/Example/ListenConsoleEvents/Example2Listener" with:
      """
      <?php

      namespace PhpZone\PhpZone\Example\ListenConsoleEvents;

      use Symfony\Component\Console\Event\ConsoleExceptionEvent;

      class Example2Listener
      {
          public function onException(ConsoleExceptionEvent $event)
          {
              $event->setException(new \Exception('', 128));
          }
      }

      """
    When I run phpzone with "example:command:2"
    Then I should have an exit code "128"
