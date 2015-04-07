<?php

namespace PhpZone\PhpZone;

use PhpZone\PhpZone\Exception\Command\InvalidCommandException;
use PhpZone\PhpZone\Exception\Config\ConfigNotFoundException;
use PhpZone\PhpZone\Exception\Extension\InvalidExtensionException;
use PhpZone\PhpZone\Extension\Extension;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
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
        $this->setContainer();

        $this->loadConfigurationFile($input);

        $this->loadExtensions();

        $this->registerCommands();

        parent::doRun($input, $output);
    }

    private function setContainer()
    {
        $this->container = new ContainerBuilder();
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

            if ($parameterName === 'extensions' && is_array($parameterValue)) {
                $this->setParametersForExtensions($parameterValue);
            }
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

    private function setParametersForExtensions(array $extensions)
    {
        foreach ($extensions as $extensionName => $extensionConfig) {
            $this->container->setParameter($extensionName, $extensionConfig);
        }
    }

    /**
     * @throws InvalidExtensionException
     */
    private function loadExtensions()
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

            if (!$extension instanceof Extension) {
                throw new InvalidExtensionException(sprintf(
                    'Defined extension "%s" is not an instance of "%s"',
                    $extension,
                    'PhpZone\PhpZone\Extension\Extension'
                ));
            }

            $extension->load($this->container);
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
