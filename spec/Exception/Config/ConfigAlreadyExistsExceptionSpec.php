<?php

namespace spec\PhpZone\PhpZone\Exception\Config;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ConfigAlreadyExistsExceptionSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('PhpZone\PhpZone\Exception\Config\ConfigAlreadyExistsException');
    }

    public function it_should_extent_base_exception()
    {
        $this->shouldHaveType('\Exception');
    }
}
