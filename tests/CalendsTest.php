<?php

namespace Danhunsaker\Calends\Tests;

use Danhunsaker\BC;

/**
 * @coversDefaultClass Danhunsaker\Calends\Calends
 */
class CalendsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $test1 = new Calends(0, 'unix');
        $test2 = new Calends(['start' => 0, 'end' => 0], 'unix');

        $this->assertInstanceOf('Danhunsaker\Calends\Calends', $test1);
        $this->assertAttributeEquals(['seconds' => BC::pow(2, 62), 'nano' => '0', 'atto' => '0'], 'internalTime', $test1);
        $this->assertAttributeEquals('0', 'duration', $test1);
        $this->assertAttributeEquals(['seconds' => BC::pow(2, 62), 'nano' => '0', 'atto' => '0'], 'endTime', $test1);
        $this->assertInstanceOf('Danhunsaker\Calends\Calends', $test2);
        $this->assertEquals($test1, $test2);
    }

    /**
     * @covers ::create
     */
    public function testCreate()
    {
        $test1 = Calends::create(0, 'unix');
        $test2 = Calends::create(['start' => 0, 'end' => 0], 'unix');

        $this->assertInstanceOf('Danhunsaker\Calends\Calends', $test1);
        $this->assertAttributeEquals(['seconds' => BC::pow(2, 62), 'nano' => '0', 'atto' => '0'], 'internalTime', $test1);
        $this->assertAttributeEquals('0', 'duration', $test1);
        $this->assertAttributeEquals(['seconds' => BC::pow(2, 62), 'nano' => '0', 'atto' => '0'], 'endTime', $test1);
        $this->assertInstanceOf('Danhunsaker\Calends\Calends', $test2);
        $this->assertEquals($test1, $test2);
    }

    /**
     * @covers ::getCalendar
     */
    public function testGetCalendar()
    {
        TestHelpers::ensureEloquentSampleCalendar();

        // Retrieves the canonical calendar name
        $this->assertEquals('unix', Calends::getCalendar('unix'));

        // Throws exception on unfound calendars
        $this->setExpectedException('Danhunsaker\Calends\UnknownCalendarException', "Can't find the 'invalid' calendar!");
        Calends::getCalendar('invalid');
    }

    /**
     * @covers ::registerCalendar
     */
    public function testRegisterCalendar()
    {
        // Can register a calendar
        $this->assertEquals(null, Calends::registerCalendar('unix', 'Danhunsaker\Calends\Calendar\Unix'));

        // No complaints about trying to register a calendar that is already loaded
        $this->assertEquals(null, Calends::registerCalendar('unix', 'Danhunsaker\Calends\Calendar\Unix'));

        // Invalid calendars should throw an exception
        $this->setExpectedException('Danhunsaker\Calends\InvalidCalendarException', "Not a valid calendar definition class name or instance: 'InvalidCalendarClass'");
        Calends::registerCalendar('invalid', 'InvalidCalendarClass');
    }

    /**
     * @covers ::getConverter
     */
    public function testGetConverter()
    {
        // Retrieves the canonical converter name
        $this->assertEquals('DateTime', Calends::getConverter(new \DateTime));

        // Throws exception on unfound converters
        $this->setExpectedException('Danhunsaker\Calends\UnknownConverterException', "Can't find the 'invalid' converter!");
        Calends::getConverter('invalid');
    }

    /**
     * @covers ::registerConverter
     */
    public function testRegisterConverter()
    {
        // Can register a converter
        $this->assertEquals(null, Calends::registerConverter('DateTime', 'Danhunsaker\Calends\Converter\DateTime'));

        // No complaints about trying to register a converter that is already loaded
        $this->assertEquals(null, Calends::registerConverter('DateTime', 'Danhunsaker\Calends\Converter\DateTime'));

        // Invalid converters should throw an exception
        $this->setExpectedException('Danhunsaker\Calends\InvalidConverterException', "Not a valid conversion class name or instance: 'InvalidConverterClass'");
        Calends::registerConverter('invalid', 'InvalidConverterClass');
    }

    /**
     * @covers ::toInternalFromUnix
     */
    public function testToInternalFromUnix()
    {
        $zero = Calends::toInternalFromUnix(0);
        $minWrap = Calends::toInternalFromUnix(BC::sub(-1, BC::pow(2, 62)));
        $maxWrap = Calends::toInternalFromUnix(BC::sub(BC::pow(2, 63), BC::pow(2, 62)));

        $this->assertArrayHasKey('seconds', $zero);
        $this->assertEquals(BC::pow(2, 62), $zero['seconds']);
        $this->assertArrayHasKey('nano', $zero);
        $this->assertEquals(0, $zero['nano']);
        $this->assertArrayHasKey('atto', $zero);
        $this->assertEquals(0, $zero['atto']);

        $this->assertArrayHasKey('seconds', $minWrap);
        $this->assertEquals(0, $minWrap['seconds']);
        $this->assertArrayHasKey('nano', $minWrap);
        $this->assertEquals(0, $minWrap['nano']);
        $this->assertArrayHasKey('atto', $minWrap);
        $this->assertEquals(0, $minWrap['atto']);

        $this->assertArrayHasKey('seconds', $maxWrap);
        $this->assertEquals(BC::sub(BC::pow(2, 63), 1), $maxWrap['seconds']);
        $this->assertArrayHasKey('nano', $maxWrap);
        $this->assertEquals(999999999, $maxWrap['nano']);
        $this->assertArrayHasKey('atto', $maxWrap);
        $this->assertEquals(999999999, $maxWrap['atto']);
    }

    /**
     * @covers ::fromInternalToUnix
     */
    public function testFromInternalToUnix()
    {
        $test = Calends::fromInternalToUnix(['seconds' => BC::pow(2, 62), 'nano' => '0', 'atto' => '0']);

        $this->assertEquals(0, $test);
    }

    /**
     * @covers ::import
     */
    public function testImport()
    {
        $test = Calends::import(date_create('@0'));

        $this->assertInstanceOf('Danhunsaker\Calends\Calends', $test);
        $this->assertAttributeEquals(['seconds' => BC::pow(2, 62), 'nano' => '0', 'atto' => '0'], 'internalTime', $test);
        $this->assertAttributeEquals('0', 'duration', $test);
        $this->assertAttributeEquals(['seconds' => BC::pow(2, 62), 'nano' => '0', 'atto' => '0'], 'endTime', $test);
    }

    /**
     * @covers ::convert
     */
    public function testConvert()
    {
        $test = Calends::create(0, 'unix')->convert('DateTime');

        $this->assertArrayHasKey('start', $test);
        $this->assertInstanceOf('DateTime', $test['start']);
        $this->assertArrayHasKey('duration', $test);
        $this->assertInstanceOf('DateInterval', $test['duration']);
        $this->assertArrayHasKey('end', $test);
        $this->assertInstanceOf('DateTime', $test['end']);
    }

    /**
     * @covers ::getInternalTime
     */
    public function testGetInternalTime()
    {
        $test = Calends::create(0, 'unix')->getInternalTime();

        $this->assertArrayHasKey('seconds', $test);
        $this->assertEquals(BC::pow(2, 62), $test['seconds']);
        $this->assertArrayHasKey('nano', $test);
        $this->assertEquals('0', $test['nano']);
        $this->assertArrayHasKey('atto', $test);
        $this->assertEquals('0', $test['atto']);
    }

    /**
     * @covers ::getInternalEndTime
     */
    public function testGetInternalEndTime()
    {
        $test = Calends::create(0, 'unix')->getInternalEndTime();

        $this->assertArrayHasKey('seconds', $test);
        $this->assertEquals(BC::pow(2, 62), $test['seconds']);
        $this->assertArrayHasKey('nano', $test);
        $this->assertEquals('0', $test['nano']);
        $this->assertArrayHasKey('atto', $test);
        $this->assertEquals('0', $test['atto']);
    }

    /**
     * @covers ::getInternalTimeAsString
     */
    public function testGetInternalTimeAsString()
    {
        $test = Calends::getInternalTimeAsString(Calends::create(0, 'unix')->getInternalTime());

        $this->assertEquals('4611686018427387904', $test);
    }

    /**
     * @covers ::getDate
     */
    public function testGetDate()
    {
        $test = Calends::create(0, 'unix')->getDate('unix');

        $this->assertEquals(0, $test);
    }

    /**
     * @covers ::getEndDate
     */
    public function testGetEndDate()
    {
        $test = Calends::create(0, 'unix')->getEndDate('unix');

        $this->assertEquals(0, $test);
    }

    /**
     * @covers ::getDuration
     */
    public function testGetDuration()
    {
        $test = Calends::create(0, 'unix')->getDuration();

        $this->assertEquals(0, $test);
    }

    /**
     * @covers ::getTimesByMode
     */
    public function testGetTimesByMode()
    {
        $test1 = Calends::create(0, 'unix');
        $test2 = Calends::create(86400, 'unix');

        $this->assertEquals([0, 0], Calends::getTimesByMode($test1, $test2, 'duration'));
        $this->assertEquals(['4611686018427387904', '4611686018427474304'], Calends::getTimesByMode($test1, $test2, 'start-end'));
        $this->assertEquals(['4611686018427387904', '4611686018427474304'], Calends::getTimesByMode($test1, $test2, 'end-start'));
        $this->assertEquals(['4611686018427387904', '4611686018427474304'], Calends::getTimesByMode($test1, $test2, 'end'));
        $this->assertEquals(['4611686018427387904', '4611686018427474304'], Calends::getTimesByMode($test1, $test2, 'start'));
    }

    /**
     * @covers ::difference
     */
    public function testDifference()
    {
        $test1 = Calends::create(0, 'unix');
        $test2 = Calends::create(86400, 'unix');

        $this->assertEquals(-86400, $test1->difference($test2));
    }

    /**
     * @covers ::compare
     */
    public function testCompare()
    {
        $test1 = Calends::create(0, 'unix');
        $test2 = Calends::create(86400, 'unix');

        $this->assertEquals(-1, Calends::compare($test1, $test2));
        $this->assertEquals(1, Calends::compare($test2, $test1));
        $this->assertEquals(0, Calends::compare($test1, $test1));
        $this->assertEquals(0, Calends::compare($test2, $test2));
    }

    /**
     * @covers ::isSame
     */
    public function testIsSame()
    {
        $test1 = Calends::create(0, 'unix');
        $test2 = Calends::create(86400, 'unix');

        $this->assertEquals(false, $test1->isSame($test2));
        $this->assertEquals(false, $test2->isSame($test1));
        $this->assertEquals(true, $test1->isSame($test1));
        $this->assertEquals(true, $test2->isSame($test2));
    }

    /**
     * @covers ::isDuring
     */
    public function testIsDuring()
    {
        $test1 = Calends::create(0, 'unix');
        $test2 = Calends::create(86400, 'unix');

        $this->assertEquals(false, $test1->isDuring($test2));
        $this->assertEquals(false, $test2->isDuring($test1));
        $this->assertEquals(true, $test1->isDuring($test1));
        $this->assertEquals(true, $test2->isDuring($test2));
    }

    /**
     * @covers ::startsDuring
     */
    public function testStartsDuring()
    {
        $test1 = Calends::create(0, 'unix');
        $test2 = Calends::create(86400, 'unix');

        $this->assertEquals(false, $test1->startsDuring($test2));
        $this->assertEquals(false, $test2->startsDuring($test1));
        $this->assertEquals(true, $test1->startsDuring($test1));
        $this->assertEquals(true, $test2->startsDuring($test2));
    }

    /**
     * @covers ::endsDuring
     */
    public function testEndsDuring()
    {
        $test1 = Calends::create(0, 'unix');
        $test2 = Calends::create(86400, 'unix');

        $this->assertEquals(false, $test1->endsDuring($test2));
        $this->assertEquals(false, $test2->endsDuring($test1));
        $this->assertEquals(true, $test1->endsDuring($test1));
        $this->assertEquals(true, $test2->endsDuring($test2));
    }

    /**
     * @covers ::contains
     */
    public function testContains()
    {
        $test1 = Calends::create(0, 'unix');
        $test2 = Calends::create(86400, 'unix');

        $this->assertEquals(false, $test1->contains($test2));
        $this->assertEquals(false, $test2->contains($test1));
        $this->assertEquals(true, $test1->contains($test1));
        $this->assertEquals(true, $test2->contains($test2));
    }

    /**
     * @covers ::overlaps
     */
    public function testOverlaps()
    {
        $test1 = Calends::create(0, 'unix');
        $test2 = Calends::create(86400, 'unix');

        $this->assertEquals(false, $test1->overlaps($test2));
        $this->assertEquals(false, $test2->overlaps($test1));
        $this->assertEquals(true, $test1->overlaps($test1));
        $this->assertEquals(true, $test2->overlaps($test2));
    }

    /**
     * @covers ::abuts
     */
    public function testAbuts()
    {
        $test1 = Calends::create(['start' => 0, 'end' => 86400], 'unix');
        $test2 = Calends::create(['start' => 86400, 'end' => 172800], 'unix');

        $this->assertEquals(true, $test1->abuts($test2));
        $this->assertEquals(true, $test2->abuts($test1));
        $this->assertEquals(false, $test1->abuts($test1));
        $this->assertEquals(false, $test2->abuts($test2));
    }

    /**
     * @covers ::isBefore
     */
    public function testIsBefore()
    {
        $test1 = Calends::create(0, 'unix');
        $test2 = Calends::create(86400, 'unix');

        $this->assertEquals(true, $test1->isBefore($test2));
        $this->assertEquals(false, $test2->isBefore($test1));
        $this->assertEquals(false, $test1->isBefore($test1));
        $this->assertEquals(false, $test2->isBefore($test2));
    }

    /**
     * @covers ::startsBefore
     */
    public function testStartsBefore()
    {
        $test1 = Calends::create(0, 'unix');
        $test2 = Calends::create(86400, 'unix');

        $this->assertEquals(true, $test1->startsBefore($test2));
        $this->assertEquals(false, $test2->startsBefore($test1));
        $this->assertEquals(false, $test1->startsBefore($test1));
        $this->assertEquals(false, $test2->startsBefore($test2));
    }

    /**
     * @covers ::endsBefore
     */
    public function testEndsBefore()
    {
        $test1 = Calends::create(0, 'unix');
        $test2 = Calends::create(86400, 'unix');

        $this->assertEquals(true, $test1->endsBefore($test2));
        $this->assertEquals(false, $test2->endsBefore($test1));
        $this->assertEquals(false, $test1->endsBefore($test1));
        $this->assertEquals(false, $test2->endsBefore($test2));
    }

    /**
     * @covers ::isAfter
     */
    public function testIsAfter()
    {
        $test1 = Calends::create(0, 'unix');
        $test2 = Calends::create(86400, 'unix');

        $this->assertEquals(false, $test1->isAfter($test2));
        $this->assertEquals(true, $test2->isAfter($test1));
        $this->assertEquals(false, $test1->isAfter($test1));
        $this->assertEquals(false, $test2->isAfter($test2));
    }

    /**
     * @covers ::startsAfter
     */
    public function testStartsAfter()
    {
        $test1 = Calends::create(0, 'unix');
        $test2 = Calends::create(86400, 'unix');

        $this->assertEquals(false, $test1->startsAfter($test2));
        $this->assertEquals(true, $test2->startsAfter($test1));
        $this->assertEquals(false, $test1->startsAfter($test1));
        $this->assertEquals(false, $test2->startsAfter($test2));
    }

    /**
     * @covers ::endsAfter
     */
    public function testEndsAfter()
    {
        $test1 = Calends::create(0, 'unix');
        $test2 = Calends::create(86400, 'unix');

        $this->assertEquals(false, $test1->endsAfter($test2));
        $this->assertEquals(true, $test2->endsAfter($test1));
        $this->assertEquals(false, $test1->endsAfter($test1));
        $this->assertEquals(false, $test2->endsAfter($test2));
    }

    /**
     * @covers ::isShorter
     */
    public function testIsShorter()
    {
        $test1 = Calends::create(['start' => 0, 'end' => 86400], 'unix');
        $test2 = Calends::create(86400, 'unix');

        $this->assertEquals(false, $test1->isShorter($test2));
        $this->assertEquals(true, $test2->isShorter($test1));
        $this->assertEquals(false, $test1->isShorter($test1));
        $this->assertEquals(false, $test2->isShorter($test2));
    }

    /**
     * @covers ::isSameDuration
     */
    public function testIsSameDuration()
    {
        $test1 = Calends::create(['start' => 0, 'end' => 86400], 'unix');
        $test2 = Calends::create(86400, 'unix');

        $this->assertEquals(false, $test1->isSameDuration($test2));
        $this->assertEquals(false, $test2->isSameDuration($test1));
        $this->assertEquals(true, $test1->isSameDuration($test1));
        $this->assertEquals(true, $test2->isSameDuration($test2));
    }

    /**
     * @covers ::isLonger
     */
    public function testIsLonger()
    {
        $test1 = Calends::create(['start' => 0, 'end' => 86400], 'unix');
        $test2 = Calends::create(86400, 'unix');

        $this->assertEquals(true, $test1->isLonger($test2));
        $this->assertEquals(false, $test2->isLonger($test1));
        $this->assertEquals(false, $test1->isLonger($test1));
        $this->assertEquals(false, $test2->isLonger($test2));
    }

    /**
     * @covers ::add
     */
    public function testAdd()
    {
        $test = Calends::create(0, 'unix')->add(86400, 'unix');

        $this->assertInstanceOf('Danhunsaker\Calends\Calends', $test);
        $this->assertAttributeEquals(['seconds' => BC::add(BC::pow(2, 62), 86400), 'nano' => '0', 'atto' => '0'], 'internalTime', $test);
        $this->assertAttributeEquals(-86400, 'duration', $test);
        $this->assertAttributeEquals(['seconds' => BC::pow(2, 62), 'nano' => '0', 'atto' => '0'], 'endTime', $test);
    }

    /**
     * @covers ::subtract
     */
    public function testSubtract()
    {
        $test = Calends::create(0, 'unix')->subtract(86400, 'unix');

        $this->assertInstanceOf('Danhunsaker\Calends\Calends', $test);
        $this->assertAttributeEquals(['seconds' => BC::sub(BC::pow(2, 62), 86400), 'nano' => '0', 'atto' => '0'], 'internalTime', $test);
        $this->assertAttributeEquals(86400, 'duration', $test);
        $this->assertAttributeEquals(['seconds' => BC::pow(2, 62), 'nano' => '0', 'atto' => '0'], 'endTime', $test);
    }

    /**
     * @covers ::addFromEnd
     */
    public function testAddFromEnd()
    {
        $test = Calends::create(0, 'unix')->addFromEnd(86400, 'unix');

        $this->assertInstanceOf('Danhunsaker\Calends\Calends', $test);
        $this->assertAttributeEquals(['seconds' => BC::pow(2, 62), 'nano' => '0', 'atto' => '0'], 'internalTime', $test);
        $this->assertAttributeEquals(86400, 'duration', $test);
        $this->assertAttributeEquals(['seconds' => BC::add(BC::pow(2, 62), 86400), 'nano' => '0', 'atto' => '0'], 'endTime', $test);
    }

    /**
     * @covers ::subtractFromEnd
     */
    public function testSubtractFromEnd()
    {
        $test = Calends::create(0, 'unix')->subtractFromEnd(86400, 'unix');

        $this->assertInstanceOf('Danhunsaker\Calends\Calends', $test);
        $this->assertAttributeEquals(['seconds' => BC::pow(2, 62), 'nano' => '0', 'atto' => '0'], 'internalTime', $test);
        $this->assertAttributeEquals(-86400, 'duration', $test);
        $this->assertAttributeEquals(['seconds' => BC::sub(BC::pow(2, 62), 86400), 'nano' => '0', 'atto' => '0'], 'endTime', $test);
    }

    /**
     * @covers ::next
     */
    public function testNext()
    {
        $test1 = Calends::create(0, 'unix')->next(86400, 'unix');
        $test2 = $test1->next();

        $this->assertInstanceOf('Danhunsaker\Calends\Calends', $test1);
        $this->assertAttributeEquals(['seconds' => BC::pow(2, 62), 'nano' => '0', 'atto' => '0'], 'internalTime', $test1);
        $this->assertAttributeEquals(86400, 'duration', $test1);
        $this->assertAttributeEquals(['seconds' => BC::add(BC::pow(2, 62), 86400), 'nano' => '0', 'atto' => '0'], 'endTime', $test1);

        $this->assertInstanceOf('Danhunsaker\Calends\Calends', $test2);
        $this->assertAttributeEquals(['seconds' => BC::add(BC::pow(2, 62), 86400), 'nano' => '0', 'atto' => '0'], 'internalTime', $test2);
        $this->assertAttributeEquals(86400, 'duration', $test2);
        $this->assertAttributeEquals(['seconds' => BC::add(BC::pow(2, 62), 172800), 'nano' => '0', 'atto' => '0'], 'endTime', $test2);
    }

    /**
     * @covers ::previous
     */
    public function testPrevious()
    {
        $test1 = Calends::create(0, 'unix')->previous(86400, 'unix');
        $test2 = $test1->previous();

        $this->assertInstanceOf('Danhunsaker\Calends\Calends', $test1);
        $this->assertAttributeEquals(['seconds' => BC::sub(BC::pow(2, 62), 86400), 'nano' => '0', 'atto' => '0'], 'internalTime', $test1);
        $this->assertAttributeEquals(86400, 'duration', $test1);
        $this->assertAttributeEquals(['seconds' => BC::pow(2, 62), 'nano' => '0', 'atto' => '0'], 'endTime', $test1);

        $this->assertInstanceOf('Danhunsaker\Calends\Calends', $test2);
        $this->assertAttributeEquals(['seconds' => BC::sub(BC::pow(2, 62), 172800), 'nano' => '0', 'atto' => '0'], 'internalTime', $test2);
        $this->assertAttributeEquals(86400, 'duration', $test2);
        $this->assertAttributeEquals(['seconds' => BC::sub(BC::pow(2, 62), 86400), 'nano' => '0', 'atto' => '0'], 'endTime', $test2);
    }

    /**
     * @covers ::setDate
     */
    public function testSetDate()
    {
        $test = Calends::create(0, 'unix')->setDate(86400, 'unix');

        $this->assertInstanceOf('Danhunsaker\Calends\Calends', $test);
        $this->assertAttributeEquals(['seconds' => BC::add(BC::pow(2, 62), 86400), 'nano' => '0', 'atto' => '0'], 'internalTime', $test);
        $this->assertAttributeEquals(-86400, 'duration', $test);
        $this->assertAttributeEquals(['seconds' => BC::pow(2, 62), 'nano' => '0', 'atto' => '0'], 'endTime', $test);
    }

    /**
     * @covers ::setEndDate
     */
    public function testSetEndDate()
    {
        $test = Calends::create(0, 'unix')->setEndDate(86400, 'unix');

        $this->assertInstanceOf('Danhunsaker\Calends\Calends', $test);
        $this->assertAttributeEquals(['seconds' => BC::pow(2, 62), 'nano' => '0', 'atto' => '0'], 'internalTime', $test);
        $this->assertAttributeEquals(86400, 'duration', $test);
        $this->assertAttributeEquals(['seconds' => BC::add(BC::pow(2, 62), 86400), 'nano' => '0', 'atto' => '0'], 'endTime', $test);
    }

    /**
     * @covers ::setDuration
     */
    public function testSetDuration()
    {
        $test = Calends::create(0, 'unix')->setDuration(86400, 'unix');

        $this->assertInstanceOf('Danhunsaker\Calends\Calends', $test);
        $this->assertAttributeEquals(['seconds' => BC::pow(2, 62), 'nano' => '0', 'atto' => '0'], 'internalTime', $test);
        $this->assertAttributeEquals(86400, 'duration', $test);
        $this->assertAttributeEquals(['seconds' => BC::add(BC::pow(2, 62), 86400), 'nano' => '0', 'atto' => '0'], 'endTime', $test);
    }

    /**
     * @covers ::setDurationFromEnd
     */
    public function testSetDurationFromEnd()
    {
        $test = Calends::create(0, 'unix')->setDurationFromEnd(86400, 'unix');

        $this->assertInstanceOf('Danhunsaker\Calends\Calends', $test);
        $this->assertAttributeEquals(['seconds' => BC::sub(BC::pow(2, 62), 86400), 'nano' => '0', 'atto' => '0'], 'internalTime', $test);
        $this->assertAttributeEquals(86400, 'duration', $test);
        $this->assertAttributeEquals(['seconds' => BC::pow(2, 62), 'nano' => '0', 'atto' => '0'], 'endTime', $test);
    }

    /**
     * @covers ::merge
     */
    public function testMerge()
    {
        $test = Calends::create(0, 'unix')->merge(Calends::create(86400, 'unix'));

        $this->assertInstanceOf('Danhunsaker\Calends\Calends', $test);
        $this->assertAttributeEquals(['seconds' => BC::pow(2, 62), 'nano' => '0', 'atto' => '0'], 'internalTime', $test);
        $this->assertAttributeEquals(86400, 'duration', $test);
        $this->assertAttributeEquals(['seconds' => BC::add(BC::pow(2, 62), 86400), 'nano' => '0', 'atto' => '0'], 'endTime', $test);
    }

    /**
     * @covers ::intersect
     */
    public function testIntersect()
    {
        $test = Calends::create(['start' => -86400, 'end' => 86400], 'unix')->intersect(Calends::create(['start' => 0, 'end' => 172800], 'unix'));

        $this->assertInstanceOf('Danhunsaker\Calends\Calends', $test);
        $this->assertAttributeEquals(['seconds' => BC::pow(2, 62), 'nano' => '0', 'atto' => '0'], 'internalTime', $test);
        $this->assertAttributeEquals(86400, 'duration', $test);
        $this->assertAttributeEquals(['seconds' => BC::add(BC::pow(2, 62), 86400), 'nano' => '0', 'atto' => '0'], 'endTime', $test);

        $this->setExpectedException('Danhunsaker\Calends\InvalidCompositeRangeException', "The ranges given do not overlap - they have no intersection.");
        $test->intersect(Calends::create(172800, 'unix'));
    }

    /**
     * @covers ::gap
     */
    public function testGap()
    {
        $test = Calends::create(['start' => -86400, 'end' => 0], 'unix')->gap(Calends::create(['start' => 86400, 'end' => 172800], 'unix'));

        $this->assertInstanceOf('Danhunsaker\Calends\Calends', $test);
        $this->assertAttributeEquals(['seconds' => BC::pow(2, 62), 'nano' => '0', 'atto' => '0'], 'internalTime', $test);
        $this->assertAttributeEquals(86400, 'duration', $test);
        $this->assertAttributeEquals(['seconds' => BC::add(BC::pow(2, 62), 86400), 'nano' => '0', 'atto' => '0'], 'endTime', $test);

        $this->setExpectedException('Danhunsaker\Calends\InvalidCompositeRangeException', "The ranges given overlap - they have no gap.");
        $test->gap(Calends::create(43200, 'unix'));
    }

    /**
     * @covers ::__invoke
     */
    public function testInvoke()
    {
        $test1 = Calends::create(0, 'unix');
        $test2 = Calends::create(['start' => 0, 'end' => 86400], 'unix');

        $this->assertEquals(0, $test1('unix'));
        $this->assertEquals(['start' => 0, 'end' => 86400], $test2('unix'));
    }

    /**
     * @covers ::__toString
     */
    public function testToString()
    {
        $test = Calends::create(0, 'unix');

        $this->assertEquals('40000000000000000000000000000000', (string) $test);
    }

    /**
     * @covers ::serialize
     */
    public function testSerialize()
    {
        $test1 = Calends::create(0, 'unix');
        $test2 = Calends::create(['start' => 0, 'end' => 86400], 'unix');

        $this->assertEquals('C:33:"Danhunsaker\Calends\Tests\Calends":40:{s:32:"40000000000000000000000000000000";}', serialize($test1));
        $this->assertEquals('C:33:"Danhunsaker\Calends\Tests\Calends":108:{a:2:{s:5:"start";s:32:"40000000000000000000000000000000";s:3:"end";s:32:"40000000000151800000000000000000";}}', serialize($test2));
    }

    /**
     * @covers ::unserialize
     */
    public function testUnserialize()
    {
        $test1 = Calends::create(0, 'unix');
        $test2 = Calends::create(['start' => 0, 'end' => 86400], 'unix');

        $this->assertEquals($test1, unserialize(serialize($test1)));
        $this->assertEquals($test2, unserialize(serialize($test2)));
    }

    /**
     * @covers ::jsonSerialize
     */
    public function testJsonSerialize()
    {
        $test1 = Calends::create(0, 'unix');
        $test2 = Calends::create(['start' => 0, 'end' => 86400], 'unix');

        $this->assertEquals('"40000000000000000000000000000000"', json_encode($test1));
        $this->assertEquals('{"start":"40000000000000000000000000000000","end":"40000000000151800000000000000000"}', json_encode($test2));
    }
}
