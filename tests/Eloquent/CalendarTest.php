<?php

namespace Danhunsaker\Calends\Tests\Eloquent;

use Danhunsaker\BC;
use Danhunsaker\Calends\Tests\TestHelpers;

/**
 * @coversDefaultClass Danhunsaker\Calends\Eloquent\Calendar
 */
class CalendarTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        TestHelpers::ensureEloquentSampleCalendar();
    }

    /**
     * @covers ::toInternal
     */
    public function testToInternal()
    {
        $this->assertEquals(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 0], Calendar::find(1)->toInternal('01 Jan 1970 00:00:00'));
    }

    /**
     * @covers ::fromInternal
     */
    public function testFromInternal()
    {
        $this->assertEquals('01 Jan 1970 00:00:00', Calendar::find(1)->fromInternal(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 0]));
    }

    /**
     * @covers ::offset
     */
    public function testOffset()
    {
        $this->assertEquals(['seconds' => BC::add(BC::pow(2, 62), 86400), 'nano' => 0, 'atto' => 0], Calendar::find(1)->offset(['seconds' => BC::pow(2, 62), 'nano' => 0, 'atto' => 0], '1 day'));
    }

    /**
     * @covers ::parseDate
     * @covers ::getEpochUnitArray
     */
    public function testParseDate()
    {
        $this->assertEquals(['second' => 0, 'minute' => 0, 'hour' => 0, 'day' => 1, 'month' => 1, 'year' => 1970], Calendar::find(1)->parseDate('1970-01-01 00:00:00'));
    }

    /**
     * @covers ::formatDate
     */
    public function testFormatDate()
    {
        $this->assertEquals('01 Jan 1970 00:00:00', Calendar::find(1)->formatDate(['second' => 0, 'minute' => 0, 'hour' => 0, 'day' => 1, 'month' => 1, 'year' => 1970], null));
        $this->assertEquals('', Calendar::find(2)->formatDate([], null));
        $this->assertEquals('1970-01-01_00-00-00', Calendar::find(1)->formatDate(['second' => 0, 'minute' => 0, 'hour' => 0, 'day' => 1, 'month' => 1, 'year' => 1970], 'filestr'));
        $this->assertEquals('year: 1970', Calendar::find(1)->formatDate(['second' => 0, 'minute' => 0, 'hour' => 0, 'day' => 1, 'month' => 1, 'year' => 1970], '\\y\\e\\a\\r: Y'));
    }

    /**
     * @covers ::parseOffset
     */
    public function testParseOffset()
    {
        $this->assertEquals(['day' => 17, 'minute' => 7, 'second' => 6, 'hour' => 1], Calendar::find(1)->parseOffset('3 day 7 minutes 6 second 1 hour 2 week'));
    }

    /**
     * @covers ::unitsToTS
     */
    public function testUnitsToTS()
    {
        $this->assertEquals(0, Calendar::find(1)->unitsToTS(['second' => 0, 'minute' => 0, 'hour' => 0, 'day' => 1, 'month' => 1, 'year' => 1970]));
        $this->assertEquals(0, Calendar::find(3)->unitsToTS([]));
    }

    /**
     * @covers ::tsToUnits
     */
    public function testTSToUnits()
    {
        $this->assertEquals([
            'second'      => 0,   'minute'     => 0,  'hour'       => 0, 'day'         => 1,   'month'      => 1,  'year'        => 1970,
            'decade'      => 198, 'century'    => 20, 'millenium'  => 2, 'quarter'     => 1,   'week'       => 0,  'millisecond' => 0,
            'microsecond' => 0,   'nanosecond' => 0,  'picosecond' => 0, 'femtosecond' => 0,   'attosecond' => 0,
        ], Calendar::find(1)->tsToUnits(0));
    }

    /**
     * @covers ::addUnits
     */
    public function testAddUnits()
    {
        $this->assertEquals([
            'day'         => 18,  'minute'       => 7,  'second'     => 6, 'hour'        => 1, 'month'      => 1, 'year'        => 1970,
            'decade'      => 198, 'century'      => 20, 'millenium'  => 2, 'quarter'     => 1, 'week'       => 2, 'millisecond' => 0,
            'microsecond' => 0,   'nanosecond'   => 0,  'picosecond' => 0, 'femtosecond' => 0, 'attosecond' => 0,
        ], Calendar::find(1)->addUnits(['day'    => 17, 'minute'     => 7, 'second'      => 6, 'hour'       => 1],
                                       ['second' => 0,  'minute'     => 0, 'hour'        => 0, 'day'        => 1, 'month'       => 1, 'year' => 1970]));
    }
}
