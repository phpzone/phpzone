<?php

namespace PhpZone\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use PhpZone\PhpZone\Application;
use Symfony\Component\Console\Tester\ApplicationTester;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Defines application features from the specific context.
 */
class ApplicationContext implements Context, SnippetAcceptingContext
{
    /** @var Application */
    private $application;

    /** @var ApplicationTester */
    private $tester;

    /** @var int */
    private $exitCode;

    /**
     * @beforeScenario
     */
    public function setUpEnvironment()
    {
        $this->setApplicationTest();
    }

    private function setApplicationTest()
    {
        $container = new ContainerBuilder();
        $application = new Application('0.1.0', $container);
        $application->setAutoExit(false);
        $this->application = $application;

        $this->tester = new ApplicationTester($this->application);
    }

    /**
     * @When I run phpzone
     * @When I run phpzone with the :option option
     */
    public function iRunPhpzoneWithTheOption($option = null)
    {
        $arguments = array ();

        $this->addOptionToArguments($option, $arguments);

        $this->exitCode = $this->tester->run($arguments);
    }

    /**
     * @param string $option
     * @param array $arguments
     */
    private function addOptionToArguments($option, array &$arguments)
    {
        if ($option) {
            if (preg_match('/(?P<option>[a-z-]+)=(?P<value>[a-z.\/]+)/', $option, $matches)) {
                $arguments[$matches['option']] = $matches['value'];
            } else {
                $arguments['--' . trim($option, '"')] = true;
            }
        }
    }

    /**
     * @When I run phpzone with :command
     */
    public function iRunPhpzoneWith($command = null)
    {
        $arguments = array ($command);

        $this->exitCode = $this->tester->run($arguments);
    }

    /**
     * @Then I should have :commandName command
     */
    public function iShouldHaveCommand($commandName)
    {
        expect($this->application->has($commandName))->toBe(true);
    }

    /**
     * @Then I should not see any error
     */
    public function iShouldNotSeeAnyError()
    {
        expect($this->exitCode)->shouldBeLike(0);
    }

    /**
     * @Then I should see an error
     */
    public function iShouldSeeAnError()
    {
        expect($this->exitCode > 0)->toBe(true);
    }

    /**
     * @Then I should see:
     */
    public function iShouldSee(PyStringNode $content)
    {
        expect($this->tester->getDisplay())->shouldBeLike($content->getRaw());
    }

    /**
     * @Then I should have an exit code :exitCode
     */
    public function iShouldHaveAnExitCode($exitCode)
    {
        expect($this->exitCode)->shouldBeLike($exitCode);
    }
}
