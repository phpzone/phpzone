<?php

namespace spec\PhpZone\PhpZone\Exception\Extension;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class InvalidExtensionExceptionSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('PhpZone\PhpZone\Exception\Extension\InvalidExtensionException');
    }

    public function it_should_extend__base_exception()
    {
        $this->shouldHaveType('\Exception');
    }
}
