<?php

namespace PhpZone\PhpZone;

use PhpZone\PhpZone\Exception\Command\InvalidCommandException;
use PhpZone\PhpZone\Exception\Config\ConfigNotFoundException;
use PhpZone\PhpZone\Exception\Extension\InvalidExtensionException;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\Yaml\Yaml;

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

    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->container = new ContainerBuilder();

        $this->loadConfigurationFile($input);

        $this->registerExtensions();

        $this->container->compile();

        $this->registerCommands();

        parent::doRun($input, $output);
    }

    /**
     * @return InputDefinition An InputDefinition instance
     */
    protected function getDefaultInputDefinition()
    {
        $definition = parent::getDefaultInputDefinition();
        $options = $definition->getOptions();

        $options['config'] = new InputOption(
            'config',
            'c',
            InputOption::VALUE_REQUIRED,
            'Specify a custom location for the configuration file'
        );

        $definition->setOptions($options);

        return $definition;
    }

    /**
     * @param InputInterface $input
     *
     * @throws ConfigNotFoundException
     */
    private function loadConfigurationFile(InputInterface $input)
    {
        $config = $this->parseConfigurationFile($input);

        foreach ($config as $parameterName => $parameterValue) {
            $this->container->setParameter($parameterName, $parameterValue);
        }
    }

    /**
     * @param InputInterface $input
     *
     * @return array
     *
     * @throws ConfigNotFoundException
     */
    private function parseConfigurationFile(InputInterface $input)
    {
        $path = 'phpzone.yml';

        if ($input->hasParameterOption(array('--config', '-c'))) {
            $path = $input->getParameterOption(array('--config', '-c'));
        }

        if (!file_exists($path)) {
            throw new ConfigNotFoundException(sprintf('Configuration file "%s" not found', $path));
        }

        $config = Yaml::parse(file_get_contents($path));

        return $config;
    }

    /**
     * @throws InvalidExtensionException
     */
    private function registerExtensions()
    {
        $extensions = $this->container->getParameter('extensions');

        foreach ($extensions as $extensionClassName => $extensionOptions) {
            if (!class_exists($extensionClassName)) {
                throw new InvalidExtensionException(sprintf(
                    'Defined extension "%s" does not exist',
                    $extensionClassName
                ));
            }

            $extension = new $extensionClassName;

            if (!$extension instanceof ExtensionInterface) {
                throw new InvalidExtensionException(sprintf(
                    'Defined extension "%s" is not an instance of "%s"',
                    get_class($extension),
                    'Symfony\Component\DependencyInjection\Extension\ExtensionInterface'
                ));
            }

            $this->container->registerExtension($extension);

            if (is_array($extensionOptions)) {
                $config = $extensionOptions;
            } else {
                $config = array($extensionOptions);
            }

            $this->container->loadFromExtension($extensionClassName, $config);
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
                throw new InvalidCommandException(sprintf(
                    'Defined service "%s% of class "%s" is not an instance of "%s"',
                    $serviceId,
                    get_class($command),
                    'Symfony\Component\Console\Command\Command'
                ));
            }

            $this->add($command);
        }
    }
}
