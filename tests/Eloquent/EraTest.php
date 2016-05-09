<?php

namespace Danhunsaker\Calends\Tests\Eloquent;

use Danhunsaker\BC;
use Danhunsaker\Calends\Eloquent\Era;
use Danhunsaker\Calends\Tests\TestHelpers;

/**
 * @coversDefaultClass Danhunsaker\Calends\Eloquent\Era
 */
class EraTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        TestHelpers::ensureEloquentSampleCalendar();
    }

    /**
     * @covers ::getFormatArgs
     */
    public function testGetFormatArgs()
    {
        $this->assertEquals(['length' => 12, 'value' => 1, 'code' => 'bc'], Era::find(1)->getFormatArgs([]));
        $this->assertEquals(['length' => 60, 'value' => 3, 'code' => 'pm'], Era::find(2)->getFormatArgs(['hour' => 15]));
        $this->assertEquals(['length' => 12, 'value' => 1, 'code' => 'ad'], Era::find(1)->getFormatArgs(['year' => 1]));
    }
}
