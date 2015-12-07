<?php

namespace spec\Danhunsaker\Calends;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CalendsSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Danhunsaker\Calends\Calends');
    }

    public function it_should_get_internal_time()
    {
        $this->getInternalTime()->shouldHaveKey('seconds');
    }

    public function it_should_get_date()
    {
        $this->getDate()->shouldBeString();
    }
}
