<?php

namespace PhpZone\PhpZone\Integration\Config\Loader;

use PhpZone\PhpZone\Config\Loader\YamlLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Filesystem\Filesystem;

class YamlLoaderTest extends \PHPUnit_Framework_TestCase
{
    /** @var Filesystem */
    private $filesystem;

    /** @var string */
    private $workingDirectory;

    /** @var YamlLoader */
    private $yamlLoader;

    public function setUp()
    {
        $this->filesystem = new Filesystem();
        $this->workingDirectory = tempnam(sys_get_temp_dir(), 'phpzone-test');
        $this->filesystem->remove($this->workingDirectory);
        $this->filesystem->mkdir($this->workingDirectory);
        chdir($this->workingDirectory);

        $this->yamlLoader = new YamlLoader(new FileLocator($this->workingDirectory));
    }

    public function tearDown()
    {
        $this->filesystem->remove($this->workingDirectory);
    }

    /**
     * @expectedException \PhpZone\PhpZone\Exception\Config\ConfigNotFoundException
     * @expectedExceptionCode 1
     */
    public function test_it_should_fail_when_given_file_does_not_exist()
    {
        $testFile = 'yamlFile.yml';

        $this->yamlLoader->load($testFile);
    }

    public function test_it_should_parse_content_when_yaml_file_given()
    {
        $testFile = 'yamlFile.yml';
        $testFileContent = <<<EOF
extensions:
    PhpZone\Exmaple: ~
EOF;
        $this->filesystem->dumpFile($testFile, $testFileContent);

        expect($this->yamlLoader->load($testFile))->toBe(array(
            'extensions' => array(
                'PhpZone\Exmaple' => array(),
            ),
        ));
    }

    /**
     * @expectedException \PhpZone\PhpZone\Exception\Config\InvalidFileTypeException
     * @expectedExceptionCode 1
     */
    public function test_it_should_fail_when_xml_file_given()
    {
        $testFile = 'xmlFile.xml';
        $this->filesystem->dumpFile($testFile, '');

        $this->yamlLoader->load($testFile);
    }

    public function test_it_should_include_imported_resources_when_resources_for_import_given()
    {
        $testFile = 'dir_1/dir_2/yamlFile.yml';
        $testFileContent = <<<EOF
imports:
    - { resource: ../yamlImportedFile.yml }
extensions:
    PhpZone\Exmaple1: ~
EOF;
        $this->filesystem->dumpFile($testFile, $testFileContent);

        $testImportedFile = 'dir_1/yamlImportedFile.yml';
        $testImportedFileContent = <<<EOF
extensions:
    PhpZone\Exmaple2: ~
EOF;
        $this->filesystem->dumpFile($testImportedFile, $testImportedFileContent);

        expect($this->yamlLoader->load($testFile))->toBe(array(
            'extensions' => array(
                'PhpZone\Exmaple1' => array(),
                'PhpZone\Exmaple2' => array(),
            ),
        ));
    }

    /**
     * @expectedException \PhpZone\PhpZone\Exception\Config\InvalidFormatException
     * @expectedExceptionCode 1
     */
    public function test_it_should_fail_when_config_has_invalid_format()
    {
        $testFile = 'yamlFile.yml';
        $testFileContent = <<<EOF
imports: string
EOF;
        $this->filesystem->dumpFile($testFile, $testFileContent);

        $this->yamlLoader->load($testFile);
    }
}
