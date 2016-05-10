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
        $dtClass = class_exists('\\DateTimeImmutable') ? '\\DateTimeImmutable' : '\\DateTime';

        $test = Converter::import(new Period($dtClass::createFromFormat('U.u', '0.000000'), $dtClass::createFromFormat('U.u', '0.000000')));

        $this->assertEquals(Calends::create(0, 'unix'), $test);
    }

    /**
     * @covers ::convert
     */
    public function testConvert()
    {
        $dtClass = class_exists('\\DateTimeImmutable') ? '\\DateTimeImmutable' : '\\DateTime';

        $test1 = Converter::convert(Calends::create(0, 'unix'));
        $this->assertEquals(new Period($dtClass::createFromFormat('U.u', '0.000000'), $dtClass::createFromFormat('U.u', '0.000000')), $test1);

        $test2 = Converter::convert(Calends::create(['start' => 0, 'end' => 86400], 'unix'));
        $this->assertEquals(new Period($dtClass::createFromFormat('U.u', '0.000000'), $dtClass::createFromFormat('U.u', '86400.000000')), $test2);
    }
}
