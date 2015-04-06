<?php

namespace PhpZone\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use PhpZone\PhpZone\Application;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Tester\ApplicationTester;

/**
 * Defines application features from the specific context.
 */
class ApplicationContext implements Context, SnippetAcceptingContext
{
    /** @var Application */
    private $application;

    /** @var StreamOutput */
    private $output;

    /** @var ApplicationTester */
    private $tester;

    /**
     * @beforeScenario
     */
    public function setUpEnvironment()
    {
        $this->setApplicationTest();
    }

    private function setApplicationTest()
    {
        $application = new Application('0.1.0');
        $application->setAutoExit(false);
        $this->application = $application;

        $this->tester = new ApplicationTester($this->application);
    }

    /**
     * @When I run phpzone
     */
    public function iRunPhpzone()
    {
        $this->tester->run(array());
    }

    /**
     * @Then I should have :commandName command
     */
    public function iShouldHaveCommand($commandName)
    {
        expect($this->application->has($commandName))->toBe(true);
    }
}
