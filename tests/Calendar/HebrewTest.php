<?php

namespace Danhunsaker\Calends\Tests\Calendar;

use Danhunsaker\Calends\Calendar\Hebrew;

/**
 * @coversDefaultClass Danhunsaker\Calends\Calendar\Hebrew
 */
class HebrewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::toInternal
     */
    public function testToInternal()
    {
        $this->assertEquals(['seconds' => bcpow(2, 62), 'nano' => 0, 'atto' => 0], Hebrew::toInternal('22-Tebeth-5730 00:00:00.000000 +00:00'));
    }

    /**
     * @covers ::fromInternal
     */
    public function testFromInternal()
    {
        $this->assertEquals('22 Tebeth 5730 00:00:00.000000 +00:00', Hebrew::fromInternal(['seconds' => bcpow(2, 62), 'nano' => 0, 'atto' => 0]));
    }

    /**
     * @covers ::offset
     */
    public function testOffset()
    {
        $this->assertEquals(['seconds' => bcadd(bcpow(2, 62), 86400), 'nano' => 0, 'atto' => 0], Hebrew::offset(['seconds' => bcpow(2, 62), 'nano' => 0, 'atto' => 0], '1 day'));
    }
}
