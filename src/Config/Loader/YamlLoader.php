<?php

namespace PhpZone\PhpZone\Config\Loader;

use PhpZone\PhpZone\Config\Definition\Configuration;
use PhpZone\PhpZone\Exception\Config\ConfigNotFoundException;
use PhpZone\PhpZone\Exception\Config\InvalidFileTypeException;
use PhpZone\PhpZone\Exception\Config\InvalidFormatException;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Yaml\Yaml;

class YamlLoader
{
    /** @var FileLocatorInterface */
    private $locator;

    /** @var Configuration */
    private $configuration;

    /** @var Processor */
    private $processor;

    public function __construct(FileLocatorInterface $locator)
    {
        $this->locator = $locator;

        $this->configuration = new Configuration();
        $this->processor = new Processor();
    }

    /**
     * @param string $file
     *
     * @return array|null
     *
     * @throws ConfigNotFoundException
     * @throws InvalidFileTypeException
     * @throws InvalidFormatException
     */
    public function load($file)
    {
        try {
            $path = $this->locator->locate($file);
        } catch (\InvalidArgumentException $e) {
            throw new ConfigNotFoundException($e->getMessage(), 1);
        }

        if (!is_string($path)) {
            return null;
        }

        if (!$this->supports($path)) {
            throw new InvalidFileTypeException(sprintf('File "%s" is not in YAML format', $file), 1);
        }

        $content = $this->loadFile($path);

        try {
            $config = $this->processor->processConfiguration($this->configuration, array($content));
        } catch (InvalidTypeException $e) {
            throw new InvalidFormatException($e->getMessage(), 1);
        }

        $this->parseImports($config, $path);

        return $config;
    }

    /**
     * @param string $path
     *
     * @return array|null
     */
    private function loadFile($path)
    {
        return Yaml::parse(file_get_contents($path));
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    private function supports($path)
    {
        return is_string($path) && 'yml' === pathinfo($path, PATHINFO_EXTENSION);
    }

    /**
     * @param array $config
     * @param string $path
     *
     * @throws ConfigNotFoundException
     * @throws InvalidFileTypeException
     * @throws InvalidFormatException
     */
    private function parseImports(array &$config, $path)
    {
        $imports = $config['imports'];

        $baseDir = dirname($path);

        foreach ($imports as $import) {
            $resource = $baseDir . '/' . ltrim($import['resource'], '/');

            $importedConfig = $this->load($resource);

            $config = array_merge_recursive($config, $importedConfig);
        }

        unset($config['imports']);
    }
}
