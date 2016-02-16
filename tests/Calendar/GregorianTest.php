<?php

namespace Danhunsaker\Calends\Tests\Calendar;

use Danhunsaker\BC;
use Danhunsaker\Calends\Calendar\Gregorian;

/**
 * @coversDefaultClass Danhunsaker\Calends\Calendar\Gregorian
 */
class GregorianTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::toInternal
     */
    public function testToInternal()
    {
        $this->assertEquals(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 0], Gregorian::toInternal('Thu, 01 Jan 1970 00:00:00.000000 +00:00'));
    }

    /**
     * @covers ::fromInternal
     */
    public function testFromInternal()
    {
        $this->assertEquals('Thu, 01 Jan 1970 00:00:00.000000 +00:00', Gregorian::fromInternal(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 0]));
    }

    /**
     * @covers ::offset
     */
    public function testOffset()
    {
        $this->assertEquals(['seconds' => BC::add(BC::pow(2, 62), 86400), 'nano' => 0, 'atto' => 0], Gregorian::offset(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 0], '1 day'));
    }
}
