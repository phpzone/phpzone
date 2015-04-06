<?php

namespace PhpZone\PhpZone;

use PhpZone\PhpZone\Extension\Extension;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
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

        $this->loadConfigurationFile();

        $this->loadExtensions();

        $this->registerCommands();

        parent::doRun($input, $output);
    }

    private function setContainer()
    {
        $this->container = new ContainerBuilder();
    }

    /**
     * @throws \RuntimeException
     */
    private function loadConfigurationFile()
    {
        $path = 'phpzone.yml';

        if (!file_exists($path)) {
            throw new \RuntimeException(sprintf('Configuration file "%s%" not found', $path));
        }

        $config = Yaml::parse(file_get_contents($path));

        foreach ($config as $parameterName => $parameterValue) {
            $this->container->setParameter($parameterName, $parameterValue);
        }
    }

    /**
     * @throws \RuntimeException
     */
    private function loadExtensions()
    {
        $extensions = $this->container->getParameter('extensions');

        foreach ($extensions as $extensionClassName) {
            $extension = new $extensionClassName;

            if (!$extension instanceof Extension) {
                throw new \RuntimeException(sprintf(
                    'Defined extension "%s" is not instance of "%s"',
                    $extension,
                    'PhpZone\PhpZone\Extension\Extension'
                ));
            }

            $extension->load($this->container);
        }
    }

    private function registerCommands()
    {
        $taggedServices = $this->container->findTaggedServiceIds('command');

        foreach ($taggedServices as $serviceId => $tags) {
            $this->add($this->container->get($serviceId));
        }
    }
}
