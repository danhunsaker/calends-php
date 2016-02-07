<?php

namespace Danhunsaker\Calends\Tests\Converter;

use Danhunsaker\Calends\Calends;
use Danhunsaker\Calends\Converter\Moment as Converter;
use Moment\Moment;

/**
 * @coversDefaultClass Danhunsaker\Calends\Converter\Moment
 */
class MomentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::import
     */
    public function testImport()
    {
        $test = Converter::import(new Moment('@0'));

        $this->assertEquals(Calends::create(0, 'unix'), $test);
    }

    /**
     * @covers ::convert
     */
    public function testConvert()
    {
        $test = Converter::convert(Calends::create(0, 'unix'));

        $this->assertEquals(['start' => new Moment('@0'), 'duration' => new \DateInterval('PT0S'), 'end' => new Moment('@0')], $test);
    }
}
