<?php

namespace Danhunsaker\Calends\Tests\Eloquent;

use Danhunsaker\BC;
use Danhunsaker\Calends\Eloquent\FragmentFormat;
use Danhunsaker\Calends\Tests\TestHelpers;

/**
 * @coversDefaultClass Danhunsaker\Calends\Eloquent\FragmentFormat
 */
class FragmentFormatTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        TestHelpers::ensureEloquentSampleCalendar();
    }

    /**
     * @covers ::formatFragment
     */
    public function testFormatFragment()
    {
        $this->assertEquals('01', FragmentFormat::find(1)->formatFragment(['second' => 0, 'minute' => 0, 'hour' => 0, 'day' => 1, 'month' => 2, 'year' => 1970]));
        $this->assertEquals('February', FragmentFormat::find(4)->formatFragment(['second' => 0, 'minute' => 0, 'hour' => 0, 'day' => 1, 'month' => 2, 'year' => 1970]));
    }

    /**
     * @covers ::getParseString
     */
    public function testGetParseString()
    {
        $this->assertEquals('%02d', FragmentFormat::find(1)->getParseString());
        $this->assertEquals('%s', FragmentFormat::find(4)->getParseString());
    }

    /**
     * @covers ::parseValue
     */
    public function testParseValue()
    {
        $this->assertEquals([1 => ['unit.day.value', 1]], FragmentFormat::find(1)->parseValue('01'));
        $this->assertEquals([1 => ['unit.month.value', 2]], FragmentFormat::find(4)->parseValue('February'));
    }
}
