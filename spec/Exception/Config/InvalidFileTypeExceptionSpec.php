<?php

namespace spec\PhpZone\PhpZone\Exception\Config;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class InvalidFileTypeExceptionSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('PhpZone\PhpZone\Exception\Config\InvalidFileTypeException');
    }

    public function it_should_extend_base_exception()
    {
        $this->shouldHaveType('\Exception');
    }
}
