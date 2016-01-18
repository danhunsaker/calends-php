<?php

namespace spec\Danhunsaker\Calends;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CalendsSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Danhunsaker\Calends\Calends');
    }

    public function it_is_initializable_with_array()
    {
        $this->beConstructedWith(['start' => 0, 'end' => 0]);
        $this->shouldHaveType('Danhunsaker\Calends\Calends');
    }

    public function it_is_initializable_through_create()
    {
        $this->beConstructedThrough('create', [['start' => 0, 'end' => 0]]);
        $this->shouldHaveType('Danhunsaker\Calends\Calends');
    }

    public function it_should_auto_register_calendars()
    {
        $this->beConstructedWith('now', 'gregorian');
        $this->shouldHaveType('Danhunsaker\Calends\Calends');
    }

    public function it_should_throw_exception_for_unknown_calendars()
    {
        $this->beConstructedWith('now', 'invalid');
        $this->shouldThrow('Danhunsaker\Calends\UnknownCalendarException')->duringInstantiation();
    }

    public function it_should_throw_exception_for_invalid_calendars()
    {
        $this->shouldThrow('Danhunsaker\Calends\InvalidCalendarException')->during('registerCalendar', ['invalid', 'NonExistentClass']);
    }

    public function it_should_throw_exception_for_unknown_converters()
    {
        $this->shouldThrow('Danhunsaker\Calends\UnknownConverterException')->during('convert', ['invalid']);
    }

    public function it_should_throw_exception_for_invalid_converters()
    {
        $this->shouldThrow('Danhunsaker\Calends\InvalidConverterException')->during('registerClassConverter', ['invalid', 'NonExistentClass']);
    }

    public function it_should_not_complain_about_duplicate_converters()
    {
        $this->convert('Carbon')->shouldHaveKey('duration');
        $this->registerClassConverter('Carbon', 'Danhunsaker\Calends\Converter\Carbon')->shouldBeNull();
    }

    public function it_should_get_internal_time()
    {
        $this->getInternalTime()->shouldHaveKey('seconds');
    }

    public function it_should_get_date()
    {
        $this->getDate()->shouldBeString();
    }

    public function it_should_get_duration()
    {
        $this->getDuration()->shouldBeLike('0');
    }

    public function it_should_get_internal_end_time()
    {
        $this->getInternalEndTime()->shouldHaveKey('seconds');
    }

    public function it_should_get_end_date()
    {
        $this->getEndDate()->shouldBeString();
    }

    public function it_should_to_internal_from_unix()
    {
        $this::toInternalFromUnix(0)->shouldHaveKey('seconds');
    }

    public function it_should_wrap_below_min_to_internal_from_unix()
    {
        $this::toInternalFromUnix(bcsub(-1, bcpow(2, 62)))->shouldHaveKeyWithValue('seconds', '0');
    }

    public function it_should_wrap_above_max_to_internal_from_unix()
    {
        $this::toInternalFromUnix(bcsub(bcpow(2, 63), bcpow(2, 62)))->shouldHaveKeyWithValue('seconds', bcsub(bcpow(2, 63), 1, 0));
    }

    public function it_should_from_internal_to_unix()
    {
        $this::fromInternalToUnix(['seconds' => 0, 'nano' => 0, 'atto' => 0])->shouldBeString();
    }

    public function it_should_compare_correctly()
    {
        bcscale(18);

        $time  = microtime(true);
        $start = bcsub($time, bcmod($time, 86400), 0);
        $end   = bcadd($start, 86400);
        $this->beConstructedWith(['start' => $start, 'end' => $end]);

        $today     = $this->getWrappedObject();
        $now       = $today->setDate($time)->setEndDate($time);
        $last24hrs = $today->previous('1 day', 'gregorian');
        $next24hrs = $today->next();
        $next48hrs = $today->next('2 day', 'gregorian');
        $this48hrs = $next48hrs->previous();

        // echo "\n";
        // var_export($today());
        // echo "\n";
        // var_export($now());
        // echo "\n";
        // var_export($last24hrs());
        // echo "\n";
        // var_export($next48hrs());
        // echo "\n";

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

    public function it_should_subtract_from_end()
    {
        $subtracted = $this->subtractFromEnd('50', 'unix');
        $subtracted->shouldHaveType('Danhunsaker\Calends\Calends');
        $subtracted->getEndDate()->shouldBeLike(bcsub($this->getWrappedObject()->getEndDate(), 50));
    }

    public function it_should_set_duration()
    {
        $newObj = $this->setDuration('50', 'unix');
        $newObj->shouldHaveType('Danhunsaker\Calends\Calends');
        $newObj->getEndDate()->shouldBeLike(bcadd($this->getWrappedObject()->getDate(), 50));
    }

    public function it_should_set_duration_from_end()
    {
        $newObj = $this->setDurationFromEnd('50', 'unix');
        $newObj->shouldHaveType('Danhunsaker\Calends\Calends');
        $newObj->getDate()->shouldBeLike(bcsub($this->getWrappedObject()->getEndDate(), 50));
    }

    public function it_should_merge()
    {
        bcscale(18);

        $time  = microtime(true);
        $start = bcsub($time, bcmod($time, 86400), 0);
        $end   = bcadd($start, 86400);
        $this->beConstructedWith(['start' => $start, 'end' => $end]);

        $today     = $this->getWrappedObject();
        $last24hrs = $today->previous('1 day', 'gregorian');

        $merged = $this->merge($last24hrs);
        $merged->shouldHaveType('Danhunsaker\Calends\Calends');
        $merged->getDate()->shouldBeLike($last24hrs->getDate());
        $merged->getEndDate()->shouldBeLike($this->getWrappedObject()->getEndDate());
    }

    public function it_should_intersect()
    {
        bcscale(18);

        $time  = microtime(true);
        $start = bcsub($time, bcmod($time, 86400), 0);
        $end   = bcadd($start, 86400);
        $this->beConstructedWith(['start' => $start, 'end' => $end]);

        $today     = $this->getWrappedObject();
        $next24hrs = $today->setDate($time)->setEndDate(bcadd($time, 86400));
        $yesterday = $today->previous('1 day', 'gregorian');

        $this->shouldThrow('Danhunsaker\Calends\InvalidCompositeRangeException')->duringIntersect($yesterday);

        $intersection = $this->intersect($next24hrs);
        $intersection->shouldHaveType('Danhunsaker\Calends\Calends');
        $intersection->getDate()->shouldBeLike($next24hrs->getDate());
        $intersection->getEndDate()->shouldBeLike($this->getWrappedObject()->getEndDate());
    }

    public function it_should_gap()
    {
        bcscale(18);

        $time  = microtime(true);
        $start = bcsub($time, bcmod($time, 86400), 0);
        $end   = bcadd($start, 86400);
        $this->beConstructedWith(['start' => $start, 'end' => $end]);

        $today     = $this->getWrappedObject();
        $next24hrs = $today->setDate($time)->setEndDate(bcadd($time, 86400));
        $yesterday = $today->previous('1 day', 'gregorian');

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
        $serialized = serialize($this);
        unserialize($serialized)->shouldHaveType('Danhunsaker\Calends\Calends');
    }

    public function it_should_recognize_gregorian()
    {
        $this->beConstructedWith('1970-01-01 00:00:00.000000 +00:00', 'gregorian');
        $this->shouldHaveType('Danhunsaker\Calends\Calends');
        $this->getDate('unix')->shouldBeLike('0');
    }

    public function it_should_calculate_gregorian()
    {
        $this->beConstructedWith('0', 'unix');
        $this->add('1 day', 'gregorian')->getDate('unix')->shouldBeLike('86400');
    }

    public function it_should_output_gregorian()
    {
        $this->beConstructedWith('0', 'unix');
        $this->getDate('gregorian')->shouldBeLike('Thu, 01 Jan 1970 00:00:00.000000 +00:00');
    }

    public function it_should_recognize_julian()
    {
        $this->beConstructedWith('1969-12-18 00:00:00.000000 +00:00', 'julian');
        $this->shouldHaveType('Danhunsaker\Calends\Calends');
        $this->getDate('unix')->shouldBeLike('0');
    }

    public function it_should_calculate_julian()
    {
        $this->beConstructedWith('0', 'unix');
        $this->add('1 day', 'julian')->getDate('unix')->shouldBeLike('86400');
    }

    public function it_should_output_julian()
    {
        $this->beConstructedWith('0', 'unix');
        $this->getDate('julian')->shouldBeLike('Thu, 18 Dec 1969 00:00:00.000000 +00:00');
    }

    public function it_should_recognize_jdc()
    {
        $this->beConstructedWith('2440587.5', 'jdc');
        $this->shouldHaveType('Danhunsaker\Calends\Calends');
        $this->getDate('unix')->shouldBeLike('0');
    }

    public function it_should_calculate_jdc()
    {
        $this->beConstructedWith('0', 'unix');
        $this->add('1', 'jdc')->getDate('unix')->shouldBeLike('86400');
    }

    public function it_should_output_jdc()
    {
        $this->beConstructedWith('0', 'unix');
        $this->getDate('jdc')->shouldBeLike('2440587.5');
    }

    public function it_should_recognize_tai()
    {
        $this->beConstructedWith('4000000000000000', 'tai');
        $this->shouldHaveType('Danhunsaker\Calends\Calends');
        $this->getDate('unix')->shouldBeLike('0');
    }

    public function it_should_calculate_tai()
    {
        $this->beConstructedWith('0', 'unix');
        $this->add('0000000000015180', 'tai')->getDate('unix')->shouldBeLike('86400');
    }

    public function it_should_output_tai()
    {
        $this->beConstructedWith('0', 'unix');
        $this->getDate('tai')->shouldBeLike('40000000000000000000000000000000');
    }

    public function it_should_wrap_above_max_tai()
    {
        $this->beConstructedWith('8000000000000000', 'tai');
        $this->shouldHaveType('Danhunsaker\Calends\Calends');
        $this->getDate('tai')->shouldBeLike('7fffffffffffffff3b9ac9ff3b9ac9ff');
    }

    public function it_should_import_datetime()
    {
        $this->beConstructedThrough('import', [date_create()]);
        $this->shouldHaveType('Danhunsaker\Calends\Calends');
    }

    public function it_should_convert_datetime()
    {
        $this->convert('DateTime')->shouldHaveKey('duration');
    }

    public function it_should_import_carbon()
    {
        $this->beConstructedThrough('import', [new \Carbon\Carbon]);
        $this->shouldHaveType('Danhunsaker\Calends\Calends');
    }

    public function it_should_convert_carbon()
    {
        $this->convert('Carbon\Carbon')->shouldHaveKey('duration');
    }

    public function it_should_import_intl_calendar()
    {
        if ( ! class_exists('\IntlCalendar')) return;
        $this::import(\IntlCalendar::createInstance(NULL, 'en_US@calendar=persian'))->shouldHaveType('Danhunsaker\Calends\Calends');
    }

    public function it_should_convert_intl_calendar()
    {
        if ( ! class_exists('\IntlCalendar')) return;
        $this->convert('IntlCalendar')->shouldHaveKey('duration');
    }

    public function it_should_import_intl_gregorian_calendar()
    {
        if ( ! class_exists('\IntlCalendar')) return;
        $this::import(\IntlCalendar::fromDateTime(date_create()))->shouldHaveType('Danhunsaker\Calends\Calends');
    }

    public function it_should_convert_intl_gregorian_calendar()
    {
        if ( ! class_exists('\IntlCalendar')) return;
        $this->convert('IntlGregorianCalendar')->shouldHaveKey('duration');
    }

    public function it_should_import_period()
    {
        if (version_compare(phpversion(), '5.5') < 0) return;
        $this->beConstructedThrough('import', [new \League\Period\Period(\DateTimeImmutable::createFromFormat('U.u', microtime(true)), \DateTimeImmutable::createFromFormat('U.u', microtime(true)))]);
        $this->shouldHaveType('Danhunsaker\Calends\Calends');
    }

    public function it_should_convert_period()
    {
        if (version_compare(phpversion(), '5.5') < 0) return;
        $this->convert('League\Period\Period')->shouldHaveType('League\Period\Period');
    }
}
