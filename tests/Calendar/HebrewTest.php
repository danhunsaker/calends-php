<?php

namespace Danhunsaker\Calends\Tests\Calendar;

use Danhunsaker\BC;
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
        $this->assertEquals(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 0], Hebrew::toInternal('22-Tebeth-5730 00:00:00.000000 +00:00'));
    }

    /**
     * @covers ::fromInternal
     */
    public function testFromInternal()
    {
        $this->assertEquals('22 Tebeth 5730 00:00:00.000000 +00:00', Hebrew::fromInternal(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 0]));
        $this->assertEquals('22 Tebeth 5731 00:00:00.000000 +00:00', Hebrew::fromInternal(['seconds' => BC::add(BC::pow(2, 62), BC::mul(86400, 384)), 'nano' => 0, 'atto' => 0]));
    }

    /**
     * @covers ::offset
     */
    public function testOffset()
    {
        $this->assertEquals(['seconds' => BC::add(BC::pow(2, 62), 86400), 'nano' => 0, 'atto' => 0], Hebrew::offset(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 0], '1 day'));
    }
}
