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
}
