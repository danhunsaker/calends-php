<?php

namespace Danhunsaker\Calends\Tests\Converter;

use Carbon\Carbon;
use Danhunsaker\Calends\Calends;
use Danhunsaker\Calends\Converter\Carbon as Converter;

/**
 * @coversDefaultClass Danhunsaker\Calends\Converter\Carbon
 */
class CarbonTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::import
     */
    public function testImport()
    {
        $test = Converter::import(Carbon::createFromTimestamp(0));

        $this->assertEquals(Calends::create(0, 'unix'), $test);
    }

    /**
     * @covers ::convert
     */
    public function testConvert()
    {
        $test1 = Converter::convert(Calends::create(0, 'unix'));
        $this->assertEquals(['start' => Carbon::createFromTimestamp(0), 'duration' => \Carbon\CarbonInterval::seconds(0), 'end' => Carbon::createFromTimestamp(0)], $test1);

        $test2 = Converter::convert(Calends::create(['start' => 0, 'end' => 86400], 'unix'));
        $this->assertEquals(['start' => Carbon::createFromTimestamp(0), 'duration' => \Carbon\CarbonInterval::seconds(86400), 'end' => Carbon::createFromTimestamp(86400)], $test2);
    }
}
