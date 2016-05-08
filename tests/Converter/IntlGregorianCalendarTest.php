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

        $date = IntlGregorianCalendar::createInstance();
        $date->clear();

        $test = Converter::convert(Calends::create(0, 'unix'));

        $this->assertEquals(['start' => $date, 'duration' => new \DateInterval('PT0S'), 'end' => $date], $test);
    }
}
