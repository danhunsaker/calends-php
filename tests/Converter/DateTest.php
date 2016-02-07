<?php

namespace Danhunsaker\Calends\Tests\Converter;

use Danhunsaker\Calends\Calends;
use Danhunsaker\Calends\Converter\Carbon as Converter;
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
        $test = Converter::convert(Calends::create(0, 'unix'));

        $this->assertEquals(['start' => Date::createFromTimestamp(0), 'duration' => \Carbon\CarbonInterval::seconds(0), 'end' => Date::createFromTimestamp(0)], $test);
    }
}
