<?php

namespace spec\PhpZone\PhpZone\Console;

use PhpSpec\ObjectBehavior;
use PhpZone\PhpZone\Application;
use Prophecy\Argument;

class ShellSpec extends ObjectBehavior
{
    public function let(Application $application)
    {
        $this->beConstructedWith($application);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('PhpZone\PhpZone\Console\Shell');
    }

    public function it_should_extend_symfony_console_shell()
    {
        $this->shouldHaveType('Symfony\Component\Console\Shell');
    }
}
