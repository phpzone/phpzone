<?php

namespace PhpZone\PhpZone;

use PhpZone\PhpZone\Config\Loader\YamlLoader;
use PhpZone\PhpZone\Console\Shell;
use PhpZone\PhpZone\DependencyInjection\RegisterListenersPass;
use PhpZone\PhpZone\Exception\Command\InvalidCommandException;
use PhpZone\PhpZone\Exception\Config\ConfigNotFoundException;
use PhpZone\PhpZone\Exception\Config\InvalidFileTypeException;
use PhpZone\PhpZone\Exception\Config\InvalidFormatException;
use PhpZone\PhpZone\Exception\Extension\InvalidExtensionException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Debug\Debug;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader as ContainerYamlFileLoader;

class Application extends BaseApplication
{
    /** @var ContainerBuilder */
    private $container;

    /**
     * @param string $version
     */
    public function __construct($version)
    {
        parent::__construct('PhpZone', $version);
    }

    /**
     * @return InputDefinition An InputDefinition instance
     */
    protected function getDefaultInputDefinition()
    {
        $definition = parent::getDefaultInputDefinition();
        $options = $definition->getOptions();

        $options['init'] = new InputOption(
            'init',
            null,
            InputOption::VALUE_NONE,
            'Initialize the configuration file <comment>(phpzone.yml)</comment>'
        );

        $options['config'] = new InputOption(
            'config',
            'c',
            InputOption::VALUE_REQUIRED,
            'Specify a custom location for the configuration file'
        );

        $options['shell'] = new InputOption(
            'shell',
            's',
            InputOption::VALUE_NONE,
            'Launch the shell environment'
        );

        $definition->setOptions($options);

        return $definition;
    }

    public function doRun(InputInterface $input, OutputInterface $output)
    {
        Debug::enable();

        $this->container = new ContainerBuilder();

        $this->setDefaultContainerConfiguration();

        $this->loadConfigurationFile($input);

        $this->registerExtensions();

        $this->container->compile();

        $this->setDispatcher($this->container->get('event_dispatcher'));

        $this->registerCommands();

        if (true === $input->hasParameterOption(array('--shell', '-s'))) {
            $shell = new Shell($this);
            $shell->run();

            return 0;
        }

        if (true === $input->hasParameterOption(array('--init'))) {
            $this->add($this->container->get('phpzone.phpzone.console.command.init'));
            $input = new ArrayInput(array('command' => 'phpzone:init'));
        }

        return parent::doRun($input, $output);
    }

    private function setDefaultContainerConfiguration()
    {
        $loader = new ContainerYamlFileLoader($this->container, new FileLocator(__DIR__ . '/../config'));
        $loader->load('services.yml');

        $listenerPass = new RegisterListenersPass();
        $this->container->addCompilerPass($listenerPass);
    }

    /**
     * @param InputInterface $input
     *
     * @throws ConfigNotFoundException
     * @throws InvalidFileTypeException
     * @throws InvalidFormatException
     */
    private function loadConfigurationFile(InputInterface $input)
    {
        $path = 'phpzone.yml';

        if ($input->hasParameterOption(array('--config', '-c'))) {
            $path = $input->getParameterOption(array('--config', '-c'));
        }

        $yamlLoader = new YamlLoader(new FileLocator(getcwd()));

        try {
            $config = $yamlLoader->load($path);
        } catch (ConfigNotFoundException $e) {
            if ($input->hasParameterOption(array('--config', '-c'))) {
                throw $e;
            } else {
                $config = array();
            }
        }

        foreach ($config as $parameterName => $parameterValue) {
            $this->container->setParameter($parameterName, $parameterValue);
        }
    }

    /**
     * @throws InvalidExtensionException
     */
    private function registerExtensions()
    {
        if ($this->container->hasParameter('extensions')) {
            $extensions = $this->container->getParameter('extensions');

            foreach ($extensions as $extensionClassName => $extensionOptions) {
                if (!class_exists($extensionClassName)) {
                    throw new InvalidExtensionException(
                        sprintf('Defined extension "%s" doesn`t exist', $extensionClassName),
                        1
                    );
                }

                $extension = new $extensionClassName;

                if (!$extension instanceof ExtensionInterface) {
                    throw new InvalidExtensionException(
                        sprintf(
                            'Defined extension "%s" isn`t an instance of "%s"',
                            get_class($extension),
                            'Symfony\Component\DependencyInjection\Extension\ExtensionInterface'
                        ),
                        1
                    );
                }

                $this->container->registerExtension($extension);

                $this->container->loadFromExtension($extensionClassName, $extensionOptions);
            }
        }
    }

    /**
     * @throws InvalidCommandException
     */
    private function registerCommands()
    {
        $taggedServices = $this->container->findTaggedServiceIds('command');

        foreach ($taggedServices as $serviceId => $tags) {
            $command = $this->container->get($serviceId);

            if (!$command instanceof Command) {
                throw new InvalidCommandException(
                    sprintf(
                        'Defined service "%s" of class "%s" isn`t an instance of "%s"',
                        $serviceId,
                        get_class($command),
                        'Symfony\Component\Console\Command\Command'
                    ),
                    1
                );
            }

            $this->add($command);
        }
    }
}
