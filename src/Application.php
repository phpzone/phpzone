<?php

namespace PhpZone\PhpZone;

use PhpZone\PhpZone\Exception\Command\InvalidCommandException;
use PhpZone\PhpZone\Exception\Config\ConfigNotFoundException;
use PhpZone\PhpZone\Exception\Config\InvalidFileTypeException;
use PhpZone\PhpZone\Exception\Config\InvalidFormatException;
use PhpZone\PhpZone\Exception\Extension\InvalidExtensionException;
use PhpZone\PhpZone\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Debug\Debug;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

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
        Debug::enable();

        $this->container = new ContainerBuilder();

        $this->loadConfigurationFile($input);

        $this->registerExtensions();

        $this->container->compile();

        $this->registerCommands();

        return parent::doRun($input, $output);
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
     * @throws InvalidFormatException
     */
    private function loadConfigurationFile(InputInterface $input)
    {
        $config = $this->parseConfigurationFile($input);

        if (empty($config)) {
            throw new InvalidFormatException('Configuration file is empty', 1);
        } elseif (!is_array($config)) {
            throw new InvalidFormatException('Configuration file doesn\'t contain correct data', 1);
        }

        foreach ($config as $parameterName => $parameterValue) {
            $this->container->setParameter($parameterName, $parameterValue);
        }
    }

    /**
     * @param InputInterface $input
     *
     * @return array|null
     *
     * @throws ConfigNotFoundException
     * @throws InvalidFileTypeException
     */
    private function parseConfigurationFile(InputInterface $input)
    {
        $path = 'phpzone.yml';

        if ($input->hasParameterOption(array('--config', '-c'))) {
            $path = $input->getParameterOption(array('--config', '-c'));
        }

        $yamlFileLoader = new YamlFileLoader(new FileLocator(getcwd()));

        $config = $yamlFileLoader->load($path);

        return $config;
    }

    /**
     * @throws InvalidExtensionException
     */
    private function registerExtensions()
    {
        if (!$this->container->hasParameter('extensions')) {
            throw new InvalidFormatException('Configuration file has to contain the "extensions" option', 1);
        }

        $extensions = $this->container->getParameter('extensions');

        if (empty($extensions)) {
            throw new InvalidFormatException(
                'Configuration file has to contain some data in the "extensions" option',
                1
            );
        } elseif (!is_array($extensions)) {
            throw new InvalidFormatException(
                'Configuration file doesn`t contain correct format of data in the "extensions" option',
                1
            );
        }

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
