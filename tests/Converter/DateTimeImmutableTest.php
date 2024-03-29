<?php

namespace Danhunsaker\Calends\Tests\Converter;

use Danhunsaker\Calends\Calends;
use Danhunsaker\Calends\Converter\DateTimeImmutable as Converter;
use DateInterval;
use DateTimeImmutable;

/**
 * @coversDefaultClass Danhunsaker\Calends\Converter\DateTimeImmutable
 */
class DateTimeImmutableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::import
     */
    public function testImport()
    {
        if ( ! class_exists('\DateTimeImmutable')) {
            return;
        }

        $test = Converter::import(new DateTimeImmutable('@0'));

        $this->assertEquals(Calends::create(0, 'unix'), $test);
    }

    /**
     * @covers ::convert
     */
    public function testConvert()
    {
        if ( ! class_exists('\DateTimeImmutable')) {
            return;
        }

        $test1 = Converter::convert(Calends::create(0, 'unix'));
        $this->assertEquals(['start' => new DateTimeImmutable('@0'), 'duration' => new DateInterval('PT0S'), 'end' => new DateTimeImmutable('@0')], $test1);

        $test2 = Converter::convert(Calends::create(['start' => 0, 'end' => 86400], 'unix'));
        $this->assertEquals(['start' => new DateTimeImmutable('@0'), 'duration' => new DateInterval('PT86400S'), 'end' => new DateTimeImmutable('@86400')], $test2);
    }
}
