<?php

namespace PhpZone\PhpZone\Loader;

use PhpZone\PhpZone\Exception\Config\ConfigNotFoundException;
use PhpZone\PhpZone\Exception\Config\InvalidFileTypeException;
use PhpZone\PhpZone\Exception\Config\InvalidFormatException;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Yaml\Yaml;

class YamlFileLoader
{
    /** @var FileLocatorInterface */
    private $locator;

    public function __construct(FileLocatorInterface $locator)
    {
        $this->locator = $locator;
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

        if (!is_array($content)) {
            return null;
        }

        $this->parseImports($content, $path);

        return $content;
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
     * @param array $content
     * @param string $path
     *
     * @throws ConfigNotFoundException
     * @throws InvalidFileTypeException
     * @throws InvalidFormatException
     */
    private function parseImports(&$content, $path)
    {
        if (!empty($content['imports'])) {
            $imports = $content['imports'];

            if (!is_array($imports)) {
                throw new InvalidFormatException(
                    sprintf('Configuration file has to contain an array of resources in the "imports" option'),
                    1
                );
            }

            $baseDir = dirname($path);

            foreach ($imports as $import) {
                if (empty($import['resource'])) {
                    throw new InvalidFormatException(
                        sprintf('Configuration file has to contain the "resource" option in the "imports" resources'),
                        1
                    );
                }

                $resource = $baseDir . '/' . ltrim($import['resource'], '/');

                $importedContent = $this->load($resource);

                $content = array_merge_recursive($content, $importedContent);
            }

            unset($content['imports']);
        }
    }
}
