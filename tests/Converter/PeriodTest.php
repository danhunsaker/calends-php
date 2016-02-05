<?php

namespace Danhunsaker\Calends\Tests\Converter;

use Danhunsaker\Calends\Calends;
use Danhunsaker\Calends\Converter\Period as Converter;
use League\Period\Period;

/**
 * @coversDefaultClass Danhunsaker\Calends\Converter\Period
 */
class PeriodTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::import
     */
    public function testImport()
    {
        if (version_compare(phpversion(), '5.5') < 0) return;

        $test = Converter::import(Period::createFromDuration('@0', 0));

        $this->assertEquals(Calends::create(0, 'unix'), $test);
    }

    /**
     * @covers ::convert
     */
    public function testConvert()
    {
        if (version_compare(phpversion(), '5.5') < 0) return;

        $test = Converter::convert(Calends::create(0, 'unix'));

        $this->assertEquals(Period::createFromDuration('@0', 0), $test);
    }
}
