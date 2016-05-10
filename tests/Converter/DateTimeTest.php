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
        $test = Converter::import(new DateTime('@0'));

        $this->assertEquals(Calends::create(0, 'unix'), $test);
    }

    /**
     * @covers ::convert
     */
    public function testConvert()
    {
        $test1 = Converter::convert(Calends::create(0, 'unix'));
        $this->assertEquals(['start' => new DateTime('@0'), 'duration' => new \DateInterval('PT0S'), 'end' => new DateTime('@0')], $test1);

        $test2 = Converter::convert(Calends::create(['start' => 0, 'end' => 86400], 'unix'));
        $this->assertEquals(['start' => new DateTime('@0'), 'duration' => new \DateInterval('PT86400S'), 'end' => new DateTime('@86400')], $test2);
    }
}
