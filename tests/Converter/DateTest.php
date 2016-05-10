<?php

namespace Danhunsaker\Calends\Tests\Converter;

use Danhunsaker\Calends\Calends;
use Danhunsaker\Calends\Converter\Date as Converter;
use Jenssegers\Date\Date;

/**
 * @coversDefaultClass Danhunsaker\Calends\Converter\Date
 */
class DateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::import
     */
    public function testImport()
    {
        $test = Converter::import(Date::createFromTimestamp(0));

        $this->assertEquals(Calends::create(0, 'unix'), $test);
    }

    /**
     * @covers ::convert
     */
    public function testConvert()
    {
        $test1 = Converter::convert(Calends::create(0, 'unix'));
        $this->assertEquals(['start' => Date::createFromTimestamp(0), 'duration' => \Carbon\CarbonInterval::seconds(0), 'end' => Date::createFromTimestamp(0)], $test1);

        $test2 = Converter::convert(Calends::create(['start' => 0, 'end' => 86400], 'unix'));
        $this->assertEquals(['start' => Date::createFromTimestamp(0), 'duration' => \Carbon\CarbonInterval::seconds(86400), 'end' => Date::createFromTimestamp(86400)], $test2);
    }
}
