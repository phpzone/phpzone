<?php

namespace spec\PhpZone\PhpZone;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ApplicationSpec extends ObjectBehavior
{
    public function let()
    {
        $version = 'x.y.z';

        $this->beConstructedWith($version);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('PhpZone\PhpZone\Application');
    }

    public function it_should_extend_symfony_console_application()
    {
        $this->shouldHaveType('Symfony\Component\Console\Application');
    }

    public function it_should_have_name_phpzone()
    {
        $this->getName()->shouldBeLike('PhpZone');
    }

    public function it_should_have_version()
    {
        $this->getVersion()->shouldBeLike('x.y.z');
    }
}
