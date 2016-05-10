<?php

namespace Danhunsaker\Calends\Tests\Converter;

use Danhunsaker\Calends\Calends;
use Danhunsaker\Calends\Converter\IntlGregorianCalendar as Converter;
use IntlGregorianCalendar;

/**
 * @coversDefaultClass Danhunsaker\Calends\Converter\IntlGregorianCalendar
 */
class IntlGregorianCalendarTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::import
     */
    public function testImport()
    {
        if ( ! class_exists('\IntlCalendar')) {
            return;
        }

        $date = IntlGregorianCalendar::createInstance();
        $date->clear();

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

        $date1 = IntlGregorianCalendar::createInstance();
        $date1->clear();
        $date2 = IntlGregorianCalendar::createInstance();
        $date2->setTime(86400);

        $test1 = Converter::convert(Calends::create(0, 'unix'));
        $this->assertEquals(['start' => $date1, 'duration' => new \DateInterval('PT0S'), 'end' => $date1], $test1);

        $test2 = Converter::convert(Calends::create(['start' => 0, 'end' => 86400], 'unix'));
        $this->assertEquals(['start' => $date1, 'duration' => new \DateInterval('PT86400S'), 'end' => $date2], $test2);
    }
}
