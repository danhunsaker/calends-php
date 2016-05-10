<?php

namespace Danhunsaker\Calends\Tests\Converter;

use Danhunsaker\Calends\Calends;
use Danhunsaker\Calends\Converter\IntlCalendar as Converter;
use IntlCalendar;
use IntlTimeZone;

/**
 * @coversDefaultClass Danhunsaker\Calends\Converter\IntlCalendar
 */
class IntlCalendarTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::import
     */
    public function testImport()
    {
        if ( ! class_exists('\IntlCalendar')) {
            return;
        }

        $date = IntlCalendar::createInstance(IntlTimeZone::getGMT(), 'en_US@calendar=persian');
        $date->setTime(0);

        $test = Converter::import($date);

        $this->assertEquals(Calends::create(0, 'unix'), $test);
    }

    /**
     * @covers ::convert
     */
    public function testConvert()
    {
        if ( ! class_exists('\IntlCalendar')) {
            return;
        }

        $date1 = IntlCalendar::createInstance(IntlTimeZone::getGMT(), 'en_US@calendar=persian');
        $date1->setTime(0);
        $date2 = IntlCalendar::createInstance(IntlTimeZone::getGMT(), 'en_US@calendar=persian');
        $date2->setTime(86400);

        Converter::$locale = 'en_US@calendar=persian';
        $test1             = Converter::convert(Calends::create(0, 'unix'));
        $this->assertEquals(['start' => $date1, 'duration' => new \DateInterval('PT0S'), 'end' => $date1], $test1);

        $test2             = Converter::convert(Calends::create(['start' => 0, 'end' => 86400], 'unix'));
        $this->assertEquals(['start' => $date1, 'duration' => new \DateInterval('PT86400S'), 'end' => $date2], $test2);
    }
}
