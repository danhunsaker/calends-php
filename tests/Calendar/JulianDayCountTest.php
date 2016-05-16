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
        $this->assertEquals(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 0], JulianDayCount::toInternal('2440587.5'));
        $this->assertEquals(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 0], JulianDayCount::toInternal('2440587.5', 'geo-centric'));
        $this->assertEquals(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 0], JulianDayCount::toInternal('40587.5', 'reduced'));
        $this->assertEquals(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 0], JulianDayCount::toInternal('40587', 'modified'));
        $this->assertEquals(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 0], JulianDayCount::toInternal('0587', 'truncated'));
        $this->assertEquals(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 0], JulianDayCount::toInternal('25567.5', 'dublin'));
        $this->assertEquals(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 0], JulianDayCount::toInternal('-10957.5', 'j2000'));
        $this->assertEquals(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 0], JulianDayCount::toInternal('141428', 'lilian'));
        $this->assertEquals(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 0], JulianDayCount::toInternal('719163', 'rata-die'));
        $this->assertEquals(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 86400], JulianDayCount::toInternal('34127.339438826655247253', 'mars-sol'));
        $this->assertEquals(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 0], JulianDayCount::toInternal('2440587.5', 'invalid'));
    }

    /**
     * @covers ::fromInternal
     */
    public function testFromInternal()
    {
        $this->assertEquals('2440587.5', JulianDayCount::fromInternal(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 0]));
        $this->assertEquals('2440587.5', JulianDayCount::fromInternal(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 0], 'geo-centric'));
        $this->assertEquals('40587.5', JulianDayCount::fromInternal(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 0], 'reduced'));
        $this->assertEquals('40587', JulianDayCount::fromInternal(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 0], 'modified'));
        $this->assertEquals('0587', JulianDayCount::fromInternal(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 0], 'truncated'));
        $this->assertEquals('25567.5', JulianDayCount::fromInternal(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 0], 'dublin'));
        $this->assertEquals('-10957.5', JulianDayCount::fromInternal(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 0], 'j2000'));
        $this->assertEquals('141428', JulianDayCount::fromInternal(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 0], 'lilian'));
        $this->assertEquals('719163', JulianDayCount::fromInternal(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 0], 'rata-die'));
        $this->assertEquals('34127.339438826655247253', JulianDayCount::fromInternal(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 86400], 'mars-sol'));
        $this->assertEquals('2440587.5', JulianDayCount::fromInternal(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 0], 'invalid'));
    }

    /**
     * @covers ::offset
     */
    public function testOffset()
    {
        $this->assertEquals(['seconds' => BC::add(BC::pow(2, 62), 86400), 'nano' => 0, 'atto' => 0], JulianDayCount::offset(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 0], 1));
    }
}
