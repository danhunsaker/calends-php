<?php

namespace Danhunsaker\Calends\Tests\Converter;

use Danhunsaker\Calends\Calends;
use Danhunsaker\Calends\Converter\Carbon as Converter;
use Carbon\Carbon;

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
        $test = Converter::convert(Calends::create(0, 'unix'));

        $this->assertEquals(['start' => Carbon::createFromTimestamp(0), 'duration' => \Carbon\CarbonInterval::seconds(0), 'end' => Carbon::createFromTimestamp(0)], $test);
    }
}
