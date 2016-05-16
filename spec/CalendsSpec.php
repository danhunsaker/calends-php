<?php

namespace spec\Danhunsaker\Calends;

use Danhunsaker\BC;
use Danhunsaker\Calends\Tests\TestHelpers;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CalendsSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        TestHelpers::ensureEloquentSampleCalendar();

        // Standard constructor
        $this->shouldHaveType('Danhunsaker\Calends\Calends');

        // static ::create() method
        $this::create()->shouldHaveType('Danhunsaker\Calends\Calends');

        // With array
        $this::create(['start' => 0, 'end' => 0])->shouldHaveType('Danhunsaker\Calends\Calends');
    }

    public function it_should_support_calendars()
    {
        // Autoload
        $this->beConstructedWith('now', 'gregorian');
        $this->shouldHaveType('Danhunsaker\Calends\Calends');

        // Don't complain about duplicates
        $this->registerCalendar('gregorian', 'Danhunsaker\Calends\Calendar\Gregorian')->shouldBeNull();

        // Throw exceptions on invalid or unknown calendars
        $this->shouldThrow('Danhunsaker\Calends\UnknownCalendarException')->during('create', ['now', 'invalid']);
        $this->shouldThrow('Danhunsaker\Calends\InvalidCalendarException')->during('registerCalendar', ['invalid', 'NonExistentClass']);
    }

    public function it_should_support_converters()
    {
        // Autoload
        $this->convert('Carbon\Carbon')->shouldHaveKey('duration');

        // Don't complain about duplicates
        $this->registerConverter('Carbon\Carbon', 'Danhunsaker\Calends\Converter\Carbon')->shouldBeNull();

        // Throw exceptions on invalid or unknown converters
        $this->shouldThrow('Danhunsaker\Calends\UnknownConverterException')->during('convert', ['invalid']);
        $this->shouldThrow('Danhunsaker\Calends\InvalidConverterException')->during('registerConverter', ['invalid', 'NonExistentClass']);
    }

    public function it_should_get_time_values()
    {
        $this->getInternalTime()->shouldHaveKey('seconds');
        $this->getDate()->shouldBeString();
        $this->getDuration()->shouldBeLike('0');
        $this->getInternalEndTime()->shouldHaveKey('seconds');
        $this->getEndDate()->shouldBeString();
    }

    public function it_should_convert_between_internal_and_unix()
    {
        $this::toInternalFromUnix(0)->shouldHaveKey('seconds');
        $this::toInternalFromUnix(BC::sub(-1, BC::pow(2, 62)))->shouldHaveKeyWithValue('seconds', '0');
        $this::toInternalFromUnix(BC::sub(BC::pow(2, 63), BC::pow(2, 62)))->shouldHaveKeyWithValue('seconds', BC::sub(BC::pow(2, 63), 1, 0));
        $this::fromInternalToUnix(['seconds' => 0, 'nano' => 0, 'atto' => 0])->shouldBeString();
        $this::fromInternalToUnix(['seconds' => 0, 'nano' => 0, 'atto' => 0])->shouldBeLike('-4611686018427387904');
    }

    public function it_should_compare_correctly()
    {
        BC::scale(18);

        $time  = microtime(true);
        $start = BC::sub($time, BC::mod($time, 86400), 0);
        $end   = BC::add($start, 86400);
        $this->beConstructedWith(['start' => $start, 'end' => $end]);

        $today     = $this->getWrappedObject();
        $now       = $today->setDate($time)->setEndDate($time);
        $last24hrs = $today->previous('1 day', 'gregorian');
        $next24hrs = $today->next();
        $next48hrs = $today->next('2 day', 'gregorian');
        $this48hrs = $next48hrs->previous();

        $this->startsBefore($today)->shouldBe(false);
        $this->isBefore($today)->shouldBe(false);
        $this->endsBefore($today)->shouldBe(false);
        $this->isSame($today)->shouldBe(true);
        $this->startsDuring($today)->shouldBe(true);
        $this->isDuring($today)->shouldBe(true);
        $this->endsDuring($today)->shouldBe(true);
        $this->contains($today)->shouldBe(true);
        $this->overlaps($today)->shouldBe(true);
        $this->abuts($today)->shouldBe(false);
        $this->startsAfter($today)->shouldBe(false);
        $this->isAfter($today)->shouldBe(false);
        $this->endsAfter($today)->shouldBe(false);
        $this->isLonger($today)->shouldBe(false);
        $this->isShorter($today)->shouldBe(false);
        $this->isSameDuration($today)->shouldBe(true);

        $this->startsBefore($now)->shouldBe(true);
        $this->isBefore($now)->shouldBe(false);
        $this->endsBefore($now)->shouldBe(false);
        $this->isSame($now)->shouldBe(false);
        $this->startsDuring($now)->shouldBe(false);
        $this->isDuring($now)->shouldBe(false);
        $this->endsDuring($now)->shouldBe(false);
        $this->contains($now)->shouldBe(true);
        $this->overlaps($now)->shouldBe(true);
        $this->abuts($now)->shouldBe(false);
        $this->startsAfter($now)->shouldBe(false);
        $this->isAfter($now)->shouldBe(false);
        $this->endsAfter($now)->shouldBe(true);
        $this->isLonger($now)->shouldBe(true);
        $this->isShorter($now)->shouldBe(false);
        $this->isSameDuration($now)->shouldBe(false);

        $this->startsBefore($last24hrs)->shouldBe(false);
        $this->isBefore($last24hrs)->shouldBe(false);
        $this->endsBefore($last24hrs)->shouldBe(false);
        $this->isSame($last24hrs)->shouldBe(false);
        $this->startsDuring($last24hrs)->shouldBe(false);
        $this->isDuring($last24hrs)->shouldBe(false);
        $this->endsDuring($last24hrs)->shouldBe(false);
        $this->contains($last24hrs)->shouldBe(false);
        $this->overlaps($last24hrs)->shouldBe(false);
        $this->abuts($last24hrs)->shouldBe(true);
        $this->startsAfter($last24hrs)->shouldBe(true);
        $this->isAfter($last24hrs)->shouldBe(true);
        $this->endsAfter($last24hrs)->shouldBe(true);
        $this->isLonger($last24hrs)->shouldBe(false);
        $this->isShorter($last24hrs)->shouldBe(false);
        $this->isSameDuration($last24hrs)->shouldBe(true);

        $this->startsBefore($next24hrs)->shouldBe(true);
        $this->isBefore($next24hrs)->shouldBe(true);
        $this->endsBefore($next24hrs)->shouldBe(true);
        $this->isSame($next24hrs)->shouldBe(false);
        $this->startsDuring($next24hrs)->shouldBe(false);
        $this->isDuring($next24hrs)->shouldBe(false);
        $this->endsDuring($next24hrs)->shouldBe(false);
        $this->contains($next24hrs)->shouldBe(false);
        $this->overlaps($next24hrs)->shouldBe(false);
        $this->abuts($next24hrs)->shouldBe(true);
        $this->startsAfter($next24hrs)->shouldBe(false);
        $this->isAfter($next24hrs)->shouldBe(false);
        $this->endsAfter($next24hrs)->shouldBe(false);
        $this->isLonger($next24hrs)->shouldBe(false);
        $this->isShorter($next24hrs)->shouldBe(false);
        $this->isSameDuration($next24hrs)->shouldBe(true);

        $this->startsBefore($this48hrs)->shouldBe(false);
        $this->isBefore($this48hrs)->shouldBe(false);
        $this->endsBefore($this48hrs)->shouldBe(false);
        $this->isSame($this48hrs)->shouldBe(false);
        $this->startsDuring($this48hrs)->shouldBe(true);
        $this->isDuring($this48hrs)->shouldBe(true);
        $this->endsDuring($this48hrs)->shouldBe(true);
        $this->contains($this48hrs)->shouldBe(false);
        $this->overlaps($this48hrs)->shouldBe(true);
        $this->abuts($this48hrs)->shouldBe(false);
        $this->startsAfter($this48hrs)->shouldBe(true);
        $this->isAfter($this48hrs)->shouldBe(false);
        $this->endsAfter($this48hrs)->shouldBe(false);
        $this->isLonger($this48hrs)->shouldBe(false);
        $this->isShorter($this48hrs)->shouldBe(true);
        $this->isSameDuration($this48hrs)->shouldBe(false);

        $this->startsBefore($next48hrs)->shouldBe(true);
        $this->isBefore($next48hrs)->shouldBe(true);
        $this->endsBefore($next48hrs)->shouldBe(true);
        $this->isSame($next48hrs)->shouldBe(false);
        $this->startsDuring($next48hrs)->shouldBe(false);
        $this->isDuring($next48hrs)->shouldBe(false);
        $this->endsDuring($next48hrs)->shouldBe(false);
        $this->contains($next48hrs)->shouldBe(false);
        $this->overlaps($next48hrs)->shouldBe(false);
        $this->abuts($next48hrs)->shouldBe(true);
        $this->startsAfter($next48hrs)->shouldBe(false);
        $this->isAfter($next48hrs)->shouldBe(false);
        $this->endsAfter($next48hrs)->shouldBe(false);
        $this->isLonger($next48hrs)->shouldBe(false);
        $this->isShorter($next48hrs)->shouldBe(true);
        $this->isSameDuration($next48hrs)->shouldBe(false);
    }

    public function it_should_modify_dates()
    {
        BC::scale(18);

        $time  = microtime(true);
        $start = BC::sub($time, BC::mod($time, 86400), 0);
        $end   = BC::add($start, 86400);
        $this->beConstructedWith(['start' => $start, 'end' => $end]);

        $today     = $this->getWrappedObject();
        $next24hrs = $today->setDate($time)->setEndDate(BC::add($time, 86400));
        $yesterday = $today->previous('1 day', 'gregorian');

        $subtracted = $this->subtractFromEnd('50', 'unix');
        $subtracted->shouldHaveType('Danhunsaker\Calends\Calends');
        $subtracted->getEndDate()->shouldBeLike(BC::sub($this->getWrappedObject()->getEndDate(), 50));

        $duration = $this->setDuration('50', 'unix');
        $duration->shouldHaveType('Danhunsaker\Calends\Calends');
        $duration->getEndDate()->shouldBeLike(BC::add($this->getWrappedObject()->getDate(), 50));

        $endDuration = $this->setDurationFromEnd('50', 'unix');
        $endDuration->shouldHaveType('Danhunsaker\Calends\Calends');
        $endDuration->getDate()->shouldBeLike(BC::sub($this->getWrappedObject()->getEndDate(), 50));

        $merged = $this->merge($yesterday);
        $merged->shouldHaveType('Danhunsaker\Calends\Calends');
        $merged->getDate()->shouldBeLike($yesterday->getDate());
        $merged->getEndDate()->shouldBeLike($this->getWrappedObject()->getEndDate());

        $this->shouldThrow('Danhunsaker\Calends\InvalidCompositeRangeException')->duringIntersect($yesterday);

        $intersection = $this->intersect($next24hrs);
        $intersection->shouldHaveType('Danhunsaker\Calends\Calends');
        $intersection->getDate()->shouldBeLike($next24hrs->getDate());
        $intersection->getEndDate()->shouldBeLike($this->getWrappedObject()->getEndDate());

        $this->shouldThrow('Danhunsaker\Calends\InvalidCompositeRangeException')->duringGap($next24hrs);

        $gap = $this->gap($yesterday);
        $gap->shouldHaveType('Danhunsaker\Calends\Calends');
        $gap->getDate()->shouldBeLike($yesterday->getEndDate());
        $gap->getEndDate()->shouldBeLike($this->getWrappedObject()->getDate());
    }

    public function it_should_work_magic()
    {
        $this->__toString()->shouldBeString();
        $this->serialize()->shouldBeString();
        $this->jsonSerialize()->shouldBeString();
        $serialized = serialize($this->getWrappedObject());
        expect(unserialize($serialized))->shouldHaveType('Danhunsaker\Calends\Calends');
    }

    public function it_should_recognize_gregorian()
    {
        $this->beConstructedWith('1970-01-01 00:00:00.000000 +00:00', 'gregorian');
        $this->shouldHaveType('Danhunsaker\Calends\Calends');
        $this->getDate('unix')->shouldBeLike('0');

        $this->add('1 day', 'gregorian')->getDate('unix')->shouldBeLike('86400');

        $this->getDate('gregorian')->shouldBeLike('Thu, 01 Jan 1970 00:00:00.000000 +00:00');
        $this->getDate('gregorian', DATE_RFC2822)->shouldBeLike('Thu, 01 Jan 1970 00:00:00 +0000');
        $this->getDate('gregorian', DATE_W3C)->shouldBeLike('1970-01-01T00:00:00+00:00');
        $this->getDate('gregorian', 'Y-m-d_H-i-s.u')->shouldBeLike('1970-01-01_00-00-00.000000');
    }

    public function it_should_recognize_hebrew()
    {
        $this->beConstructedWith('5730-04-22 00:00:00.000000 +00:00', 'hebrew');
        $this->shouldHaveType('Danhunsaker\Calends\Calends');
        $this->getDate('unix')->shouldBeLike('0');

        $this->add('1 day', 'hebrew')->getDate('unix')->shouldBeLike('86400');

        $this->getDate('hebrew')->shouldBeLike('22 Tebeth 5730 00:00:00.000000 +00:00');
        $this::create('5731-04-22 00:00:00.000000 +00:00', 'hebrew')->getDate('hebrew')->shouldBeLike('22 Tebeth 5731 00:00:00.000000 +00:00');
    }

    public function it_should_recognize_julian()
    {
        $this->beConstructedWith('1969-12-18 00:00:00.000000 +00:00', 'julian');
        $this->shouldHaveType('Danhunsaker\Calends\Calends');
        $this->getDate('unix')->shouldBeLike('0');

        $this->add('1 day', 'julian')->getDate('unix')->shouldBeLike('86400');

        $this->getDate('julian')->shouldBeLike('Thu, 18 Dec 1969 00:00:00.000000 +00:00');
    }

    public function it_should_recognize_jdc()
    {
        $this->beConstructedWith('2440587.5', 'jdc');
        $this->shouldHaveType('Danhunsaker\Calends\Calends');
        $this->getDate('unix')->shouldBeLike('0');

        $this::create('2440587.5', 'jdc', 'geo-centric')->getDate('unix')->shouldBeLike('0');
        $this::create('40587.5', 'jdc', 'reduced')->getDate('unix')->shouldBeLike('0');
        $this::create('40587', 'jdc', 'modified')->getDate('unix')->shouldBeLike('0');
        $this::create('0587', 'jdc', 'truncated')->getDate('unix')->shouldBeLike('0');
        $this::create('25567.5', 'jdc', 'dublin')->getDate('unix')->shouldBeLike('0');
        $this::create('-10957.5', 'jdc', 'j2000')->getDate('unix')->shouldBeLike('0');
        $this::create('141428', 'jdc', 'lilian')->getDate('unix')->shouldBeLike('0');
        $this::create('719163', 'jdc', 'rata-die')->getDate('unix')->shouldBeLike('0');
        $this::create('34127.339438826655247253', 'jdc', 'mars-sol')->getDate('unix')->shouldBeLike('0.000000000000086400');
        $this::create('2440587.5', 'jdc', 'invalid')->getDate('unix')->shouldBeLike('0');

        $this->add('1', 'jdc')->getDate('unix')->shouldBeLike('86400');

        $this->getDate('jdc')->shouldBeLike('2440587.5');
        $this->getDate('jdc', 'geo-centric')->shouldBeLike('2440587.5');
        $this->getDate('jdc', 'reduced')->shouldBeLike('40587.5');
        $this->getDate('jdc', 'modified')->shouldBeLike('40587');
        $this->getDate('jdc', 'truncated')->shouldBeLike('0587');
        $this->getDate('jdc', 'dublin')->shouldBeLike('25567.5');
        $this->getDate('jdc', 'j2000')->shouldBeLike('-10957.5');
        $this->getDate('jdc', 'lilian')->shouldBeLike('141428');
        $this->getDate('jdc', 'rata-die')->shouldBeLike('719163');
        $this->getDate('jdc', 'mars-sol')->shouldBeLike('34127.339438826655247253');
        $this->getDate('jdc', 'invalid')->shouldBeLike('2440587.5');
    }

    public function it_should_recognize_tai()
    {
        $this->beConstructedWith('4000000000000000', 'tai');
        $this->shouldHaveType('Danhunsaker\Calends\Calends');
        $this->getDate('unix')->shouldBeLike('0');

        $this::create('4000000000000000', 'tai', 'tai64')->getDate('unix')->shouldBeLike('0');
        $this::create('400000000000000000000000', 'tai', 'tai64n')->getDate('unix')->shouldBeLike('0');
        $this::create('40000000000000000000000000000000', 'tai', 'tai64na')->getDate('unix')->shouldBeLike('0');
        $this::create('4611686018427387904.000000000000000000', 'tai', 'numeric')->getDate('unix')->shouldBeLike('0');
        $this::create('4611686018427387904', 'tai', 'numeric')->getDate('unix')->shouldBeLike('0');
        $this::create('-1', 'tai', 'numeric')->getDate('tai', 'numeric')->shouldBeLike('0');
        $this::create('40000000000000000000000000000000', 'tai', 'invalid')->getDate('unix')->shouldBeLike('0');

        $this->add('0000000000015180', 'tai')->getDate('unix')->shouldBeLike('86400');

        $this->getDate('tai')->shouldBeLike('40000000000000000000000000000000');
        $this->getDate('tai', 'tai64')->shouldBeLike('4000000000000000');
        $this->getDate('tai', 'tai64n')->shouldBeLike('400000000000000000000000');
        $this->getDate('tai', 'tai64na')->shouldBeLike('40000000000000000000000000000000');
        $this->getDate('tai', 'numeric')->shouldBeLike('4611686018427387904.000000000000000000');
        $this->getDate('tai', 'invalid')->shouldBeLike('40000000000000000000000000000000');

        $this::create('8000000000000000', 'tai')->getDate('tai')->shouldBeLike('7fffffffffffffff3b9ac9ff3b9ac9ff');
    }

    public function it_should_recognize_eloquent()
    {
        TestHelpers::ensureEloquentSampleCalendar();

        $this->beConstructedWith('01 Jan 1970 00:00:00', 'eloquent');
        $this->shouldHaveType('Danhunsaker\Calends\Calends');
        $this->getDate('unix')->shouldBeLike('0');

        $this::create('%_year_%: 1970; dm: 01Jan', 'eloquent', '%_\\y\\e\\a\\r_%: Y; \d\m: dM')->getDate('unix')->shouldBeLike('0');

        $this->add('6 days 1 week 13 minutes 2 year 2 month', 'eloquent')->getDate('unix')->shouldBeLike('69293580');
        $this->add('69293580 second', 'eloquent')->getDate('unix')->shouldBeLike('69293580');

        $this->getDate('eloquent')->shouldBeLike('01 Jan 1970 00:00:00');
        $this->getDate('eloquent', 'filestr')->shouldBeLike('1970-01-01_00-00-00');
        $this->getDate('eloquent', '\\y\\e\\a\\r: Y')->shouldBeLike('year: 1970');
    }

    public function it_should_recognize_broken()
    {
        TestHelpers::ensureEloquentSampleCalendar();

        $this->beConstructedWith('', 'broken');
        $this->shouldHaveType('Danhunsaker\Calends\Calends');
        $this->getDate('unix')->shouldBeLike('0');

        $this->getDate('broken')->shouldBeLike('');
        $this->getDate('broken', '\\s\\e\\c\\o\\n\\d: s')->shouldBeLike('second: 00');
    }

    public function it_should_recognize_extra_broken()
    {
        TestHelpers::ensureEloquentSampleCalendar();

        $this->beConstructedWith('', 'extra-broken');
        $this->shouldHaveType('Danhunsaker\Calends\Calends');
        $this->getDate('unix')->shouldBeLike('0');
    }

    public function it_should_convert_date_time()
    {
        $this->beConstructedThrough('import', [date_create()]);
        $this->shouldHaveType('Danhunsaker\Calends\Calends');

        $this->convert('DateTime')->shouldHaveKey('duration');
    }

    public function it_should_convert_date_time_immutable()
    {
        if ( ! class_exists('\DateTimeImmutable')) {
            return;
        }
        $this->beConstructedThrough('import', [new \DateTimeImmutable()]);
        $this->shouldHaveType('Danhunsaker\Calends\Calends');

        $this->convert('DateTimeImmutable')->shouldHaveKey('duration');
    }

    public function it_should_convert_moment()
    {
        $this->beConstructedThrough('import', [new \Moment\Moment()]);
        $this->shouldHaveType('Danhunsaker\Calends\Calends');

        $this->convert('Moment\Moment')->shouldHaveKey('duration');
    }

    public function it_should_convert_carbon()
    {
        $this->beConstructedThrough('import', [new \Carbon\Carbon]);
        $this->shouldHaveType('Danhunsaker\Calends\Calends');

        $this->convert('Carbon\Carbon')->shouldHaveKey('duration');
    }

    public function it_should_convert_date()
    {
        $this->beConstructedThrough('import', [new \Jenssegers\Date\Date]);
        $this->shouldHaveType('Danhunsaker\Calends\Calends');

        $this->convert('Jenssegers\Date\Date')->shouldHaveKey('duration');
    }

    public function it_should_convert_intl_calendar()
    {
        if ( ! class_exists('\IntlCalendar')) {
            return;
        }
        $this::import(\IntlCalendar::createInstance(null, 'en_US@calendar=persian'))->shouldHaveType('Danhunsaker\Calends\Calends');

        $this->convert('IntlCalendar')->shouldHaveKey('duration');
    }

    public function it_should_convert_intl_gregorian_calendar()
    {
        if ( ! class_exists('\IntlCalendar')) {
            return;
        }
        $this::import(\IntlCalendar::fromDateTime(date_create()))->shouldHaveType('Danhunsaker\Calends\Calends');

        $this->convert('IntlGregorianCalendar')->shouldHaveKey('duration');
    }

    public function it_should_convert_period()
    {
        $dtClass = class_exists('\\DateTimeImmutable') ? '\\DateTimeImmutable' : '\\DateTime';

        $this->beConstructedThrough('import', [new \League\Period\Period($dtClass::createFromFormat('U.u', (string) microtime(true)), $dtClass::createFromFormat('U.u', (string) microtime(true)))]);
        $this->shouldHaveType('Danhunsaker\Calends\Calends');

        $this->convert('League\Period\Period')->shouldHaveType('League\Period\Period');
    }
}
