<?php

namespace Danhunsaker\Calends\Tests\Calendar;

use Danhunsaker\BC;
use Danhunsaker\Calends\Calendar\TAI64;

/**
 * @coversDefaultClass Danhunsaker\Calends\Calendar\TAI64
 */
class TAI64Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::toInternal
     */
    public function testToInternal()
    {
        $this->assertEquals(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 0], TAI64::toInternal('40000000000000000000000000000000'));
        $this->assertEquals(['seconds' => BC::sub(BC::pow(2, 63), 1, 0), 'nano' => 999999999, 'atto' => 999999999], TAI64::toInternal('80000000000000000000000000000000'));
    }

    /**
     * @covers ::fromInternal
     */
    public function testFromInternal()
    {
        $this->assertEquals('40000000000000000000000000000000', TAI64::fromInternal(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 0]));
    }

    /**
     * @covers ::offset
     */
    public function testOffset()
    {
        $this->assertEquals(['seconds' => BC::add(BC::pow(2, 62), 86400), 'nano' => 0, 'atto' => 0], TAI64::offset(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 0], '15180'));
    }
}
