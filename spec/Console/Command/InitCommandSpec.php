<?php

namespace spec\PhpZone\PhpZone\Console\Command;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class InitCommandSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('PhpZone\PhpZone\Console\Command\InitCommand');
    }

    public function it_should_extend_symfony_command()
    {
        $this->shouldHaveType('Symfony\Component\Console\Command\Command');
    }

    public function it_should_have_name()
    {
        $this->getName()->shouldBeLike('phpzone:init');
    }
}
