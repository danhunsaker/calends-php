<?php

namespace Danhunsaker\Calends\Tests\Calendar;

use Danhunsaker\BC;
use Danhunsaker\Calends\Calendar\Unix;

/**
 * @coversDefaultClass Danhunsaker\Calends\Calendar\Unix
 */
class UnixTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::toInternal
     */
    public function testToInternal()
    {
        $this->assertEquals(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 0], Unix::toInternal(0));
    }

    /**
     * @covers ::fromInternal
     */
    public function testFromInternal()
    {
        $this->assertEquals(0, Unix::fromInternal(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 0]));
    }

    /**
     * @covers ::offset
     */
    public function testOffset()
    {
        $this->assertEquals(['seconds' => BC::add(BC::pow(2, 62), 86400), 'nano' => 0, 'atto' => 0], Unix::offset(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 0], 86400));
    }
}
