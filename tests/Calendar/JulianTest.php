<?php

namespace Danhunsaker\Calends\Tests\Calendar;

use Danhunsaker\Calends\Calendar\Julian;

/**
 * @coversDefaultClass Danhunsaker\Calends\Calendar\Julian
 */
class JulianTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::toInternal
     */
    public function testToInternal()
    {
        $this->assertEquals(['seconds' => bcpow(2, 62), 'nano' => 0, 'atto' => 0], Julian::toInternal('Thu, 18 Dec 1969 00:00:00.000000 +00:00'));
    }

    /**
     * @covers ::fromInternal
     */
    public function testFromInternal()
    {
        $this->assertEquals('Thu, 18 Dec 1969 00:00:00.000000 +00:00', Julian::fromInternal(['seconds' => bcpow(2, 62), 'nano' => 0, 'atto' => 0]));
    }

    /**
     * @covers ::offset
     */
    public function testOffset()
    {
        $this->assertEquals(['seconds' => bcadd(bcpow(2, 62), 86400), 'nano' => 0, 'atto' => 0], Julian::offset(['seconds' => bcpow(2, 62), 'nano' => 0, 'atto' => 0], '1 day'));
    }
}
