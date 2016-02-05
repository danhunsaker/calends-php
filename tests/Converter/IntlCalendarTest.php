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
        if ( ! class_exists('\IntlCalendar')) return;

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
        if ( ! class_exists('\IntlCalendar')) return;

        $date = IntlCalendar::createInstance(IntlTimeZone::getGMT(), 'en_US@calendar=persian');
        $date->setTime(0);

        Converter::$locale = 'en_US@calendar=persian';
        $test = Converter::convert(Calends::create(0, 'unix'));

        $this->assertEquals(['start' => $date, 'duration' => new \DateInterval('PT0S'), 'end' => $date], $test);
    }
}
