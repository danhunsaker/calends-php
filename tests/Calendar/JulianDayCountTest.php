<?php

namespace Danhunsaker\Calends\Tests\Calendar;

use Danhunsaker\BC;
use Danhunsaker\Calends\Calendar\JulianDayCount;

/**
 * @coversDefaultClass Danhunsaker\Calends\Calendar\JulianDayCount
 */
class JulianDayCountTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::toInternal
     */
    public function testToInternal()
    {
        $this->assertEquals(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 0], JulianDayCount::toInternal(2440587.5));
    }

    /**
     * @covers ::fromInternal
     */
    public function testFromInternal()
    {
        $this->assertEquals(2440587.5, JulianDayCount::fromInternal(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 0]));
    }

    /**
     * @covers ::offset
     */
    public function testOffset()
    {
        $this->assertEquals(['seconds' => BC::add(BC::pow(2, 62), 86400), 'nano' => 0, 'atto' => 0], JulianDayCount::offset(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 0], 1));
    }
}