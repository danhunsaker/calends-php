<?php

namespace Danhunsaker\Calends\Tests\Converter;

use Danhunsaker\Calends\Calends;
use Danhunsaker\Calends\Converter\DateTime as Converter;
use DateTime;

/**
 * @coversDefaultClass Danhunsaker\Calends\Converter\DateTime
 */
class DateTimeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::import
     */
    public function testImport()
    {
        $test = Converter::import(date_create('@0'));

        $this->assertEquals(Calends::create(0, 'unix'), $test);
    }

    /**
     * @covers ::convert
     */
    public function testConvert()
    {
        $test = Converter::convert(Calends::create(0, 'unix'));

        $this->assertEquals(['start' => date_create('@0'), 'duration' => new \DateInterval('PT0S'), 'end' => date_create('@0')], $test);
    }
}
