<?php

namespace Danhunsaker\Calends\Tests\Eloquent;

use Danhunsaker\BC;
use Danhunsaker\Calends\Eloquent\Unit;
use Danhunsaker\Calends\Tests\TestHelpers;

/**
 * @coversDefaultClass Danhunsaker\Calends\Eloquent\Unit
 */
class UnitTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        TestHelpers::ensureEloquentSampleCalendar();
    }

    /**
     * @covers ::toSeconds
     * @covers ::scaleReduce
     */
    public function testToSeconds()
    {
        $this->assertEquals('62128339200', Unit::find(1)->toSeconds(['second' => 0, 'minute' => 0, 'hour' => 0, 'day' => 1, 'month' => 2, 'year' => 1970]));
        $this->assertEquals(0, Unit::find(1)->toSeconds([]));
    }

    /**
     * @covers ::reduceAuxiliary
     * @covers ::scaleReduce
     */
    public function testReduceAuxiliary()
    {
        $this->assertEquals(['second', 20], Unit::find(1)->reduceAuxiliary(20));
        $this->assertEquals(['day', 140], Unit::find(12)->reduceAuxiliary(20));
        $this->assertEquals(['second', 0], Unit::find(20)->reduceAuxiliary(20));
    }

    /**
     * @covers ::carryOver
     */
    public function testCarryOver()
    {
        $this->assertEquals(['minute'      => 0,   'second'        => 0,  'hour'       => 0, 'day'         => 1,  'month'      => 1, 'year'        => 0,
                             'decade'      => 1,   'century'       => 1,  'millenium'  => 1, 'quarter'     => 1,  'week'       => 0, 'millisecond' => 0,
                             'microsecond' => 0,   'nanosecond'    => 0,  'picosecond' => 0, 'femtosecond' => 0,  'attosecond' => 0], Unit::find(1)->carryOver([]));
        $this->assertEquals(['half-second' => 0,   'broken-second' => 0], Unit::find(18)->carryOver([]));
        $this->assertEquals(['minute'      => 0,   'second'        => 0,  'hour'       => 0, 'day'         => 29, 'month'      => 1, 'year'        => 1970,
                             'decade'      => 198, 'century'       => 20, 'millenium'  => 2, 'quarter'     => 1,  'week'       => 4, 'millisecond' => 0,
                             'microsecond' => 0,   'nanosecond'    => 0,  'picosecond' => 0, 'femtosecond' => 0,  'attosecond' => 0], Unit::find(1)->carryOver(['second' => '62128339200']));
    }

    /**
     * @covers ::getFormatArgs
     */
    public function testGetFormatArgs()
    {
        $this->assertEquals(['length' => 1,  'value' => 0], Unit::find(1)->getFormatArgs([]));
        $this->assertEquals(['length' => 0,  'value' => 0], Unit::find(18)->getFormatArgs([]));
        $this->assertEquals(['length' => 31, 'value' => 1], Unit::find(5)->getFormatArgs(['month' => '1']));
        $this->assertEquals(['length' => 1,  'value' => '62128339200'], Unit::find(1)->getFormatArgs(['second' => '62128339200']));
    }
}
