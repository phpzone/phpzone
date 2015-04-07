<?php

namespace spec\PhpZone\PhpZone\Exception\Command;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class InvalidCommandExceptionSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('PhpZone\PhpZone\Exception\Command\InvalidCommandException');
    }

    public function it_should_extend_base_exception()
    {
        $this->shouldHaveType('\Exception');
    }
}
