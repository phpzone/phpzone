<?php

namespace PhpZone\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Defines application features from the specific context.
 */
class FilesystemContext implements Context, SnippetAcceptingContext
{
    /** @var Filesystem */
    private $filesystem;

    /** @var string */
    private $workingDirectory;

    public function __construct()
    {
        $this->filesystem = new Filesystem();
    }

    /**
     * @beforeScenario
     */
    public function setUpEnvironment()
    {
        $this->setWorkingDirectory();
    }

    private function setWorkingDirectory()
    {
        $this->workingDirectory = tempnam(sys_get_temp_dir(), 'phpzone-test');
        $this->filesystem->remove($this->workingDirectory);
        $this->filesystem->mkdir($this->workingDirectory);
        chdir($this->workingDirectory);
    }

    /**
     * @afterScenario
     */
    public function tearDownEnvironment()
    {
        $this->filesystem->remove($this->workingDirectory);
    }

    /**
     * @Given there is a config file with:
     */
    public function thereIsAConfigFileWith(PyStringNode $content)
    {
        $this->createFile('phpzone.yml', $content);
    }

    /**
     * @Given there is a file :file with:
     */
    public function thereIsAFileWith($file, PyStringNode $content)
    {
        $this->createFile($file, $content);
    }

    /**
     * @Given there is no config file
     */
    public function thereIsNoConfigFile()
    {
    }

    /**
     * @Given there is a class in the :file with:
     */
    public function thereIsAClassInTheWith($file, PyStringNode $content)
    {
        $this->createFile($file, $content);
        $this->loadClassFile($file);
    }

    /**
     * @param string $file
     * @param PyStringNode $content
     */
    private function createFile($file, PyStringNode $content)
    {
        $this->filesystem->dumpFile($file, $content);
    }

    /**
     * @param string $file
     */
    private function loadClassFile($file)
    {
        require_once $file;
    }
}
