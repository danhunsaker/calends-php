<?php

namespace spec\Danhunsaker\Calends;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CalendsSpec extends ObjectBehavior
{
    public function it_is_initializable()
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

    public function it_should_to_internal_from_unix()
    {
        $this::toInternalFromUnix(0)->shouldHaveKey('seconds');
    }

    public function it_should_from_internal_to_unix()
    {
        $this::fromInternalToUnix(['seconds' => 0, 'nano' => 0, 'atto' => 0])->shouldBeString();
    }
}
