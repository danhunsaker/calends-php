<?php

namespace Danhunsaker\Calends;

use Danhunsaker\Calends\Calendar\DefinitionInterface as CalendarDefinition;
use Danhunsaker\Calends\Converter\ConverterInterface as ConversionClass;
use Danhunsaker\Calends\Calendar\ObjectDefinitionInterface as ObjectDefinition;
use JsonSerializable;
use RMiller\Caser\Cased;
use Serializable;

class Calends implements Serializable, JsonSerializable
{
    /**
     * Stores the internal time representation for the start date
     *
     * @var array
     **/
    protected $internalTime = ['seconds' => 0];

    /**
     * Stores the duration of the object's date range in seconds
     *
     * @var string|int
     **/
    protected $duration = 0;

    /**
     * Stores the internal time representation for the end date
     *
     * @var array
     **/
    protected $endTime = ['seconds' => 0];

    /**
     * Stores all registered calendar definition functions
     *
     * @var array
     **/
    protected static $timeConverters = [
        'toInternal'   => [],
        'fromInternal' => [],
        'offset'       => [],
        'import'       => [],
        'convert'      => [],
    ];

    // Setup Functions

    /**
     * create a new Calends object
     *
     * The Calends constructor.
     *
     * @param mixed stamp Either a single date value to parse, or an array with start and end dates to parse
     * @param string calendar The calendar system definition to parse $stamp with
     **/
    public function __construct($stamp = null, $calendar = 'unix')
    {
        bcscale(18);

        static::registerCalendar('unix', __NAMESPACE__ . '\\Calendar\\Unix');
        static::registerCalendar('jdc', __NAMESPACE__ . '\\Calendar\\JulianDayCount');
        static::registerCalendar('tai', __NAMESPACE__ . '\\Calendar\\TAI64');

        if (is_array($stamp)) {
            $this->internalTime = call_user_func(static::$timeConverters['toInternal'][$this->getCalendar($calendar)], $stamp['start']);
            $this->endTime      = call_user_func(static::$timeConverters['toInternal'][$this->getCalendar($calendar)], $stamp['end']);
            $this->duration     = $this->difference($this, 'end-start');
        } else {
            $this->internalTime = call_user_func(static::$timeConverters['toInternal'][$this->getCalendar($calendar)], $stamp);
            $this->endTime      = $this->internalTime;
            $this->duration     = 0;
        }
    }

    /**
     * create a new Calends object
     *
     * Calls the Calends constructor in a static context rather than a
     * `new Class` context.  Useful for method chaining.
     *
     * @param mixed stamp Either a single date value to parse, or an array with start and end dates to parse
     * @param string calendar The calendar system definition to parse $stamp with
     * @return Danhunsaker\Calends\Calends
     **/
    public static function create($stamp = null, $calendar = 'unix')
    {
        return new static($stamp, $calendar);
    }

    protected function getCalendar($calendar)
    {
        $calendar = Cased::fromCamelCase($calendar)->asCamelCase();

        if ( ! array_key_exists($calendar, static::$timeConverters['toInternal'])) {
            $className = __NAMESPACE__ . '\\Calendar\\' . Cased::fromCamelCase($calendar)->asPascalCase();

            if (class_exists($className)) {
                static::registerCalendar($calendar, $className);
            } else {    // TODO: Implement Eloquent definitions, and check/register them before giving up
                throw new UnknownCalendarException("Can't find the '{$calendar}' calendar!");
            }
        }

        return $calendar;
    }

    public static function registerCalendar($calendar, $className)
    {
        $calendar = Cased::fromCamelCase($calendar)->asCamelCase();

        if (array_key_exists($calendar, static::$timeConverters['toInternal'])) {
            return;
        }

        if ( ! ((is_string($className) && class_exists($className) && is_a($className, CalendarDefinition::class, true))
            || (is_object($className) && is_a($className, ObjectDefinition::class)))) {
            throw new InvalidCalendarException('Not a vaild calendar definition class name or instance: ' . var_export($className, true));
        }

        static::$timeConverters['toInternal'][$calendar]   = [$className, 'toInternal'];
        static::$timeConverters['fromInternal'][$calendar] = [$className, 'fromInternal'];
        static::$timeConverters['offset'][$calendar]       = [$className, 'offset'];
    }

    protected function getConverter($convert)
    {
        if (is_object($convert)) $convert = get_class($convert);

        if ( ! array_key_exists($convert, static::$timeConverters['import'])) {
            $className = __NAMESPACE__ . "\\Converter\\{$convert}";

            if (class_exists($className)) {
                static::registerClassConverters($convert, $className);
            } else {
                throw new UnknownConverterException("Can't find the '{$convert}' converter!");
            }
        }

        return $convert;
    }

    public static function registerClassConverter($className, $conversionClass)
    {
        if (array_key_exists($className, static::$timeConverters['import'])) {
            return;
        }

        if ( ! (((is_string($conversionClass) && class_exists($conversionClass)) || is_object($conversionClass)) && is_a($conversionClass, ConversionClass::class, true))) {
            throw new InvalidConverterException('Not a vaild conversion class name or instance: ' . var_export($conversionClass, true));
        }

        static::$timeConverters['import'][$className]  = [$conversionClass, 'import'];
        static::$timeConverters['convert'][$className] = [$conversionClass, 'convert'];
    }

    // Conversion Functions

    public static function toInternalFromUnix($stamp)
    {
        $stamp = is_null($stamp) ? microtime(true) : $stamp;

        $time = [];
        if (bccomp($stamp, bcsub(0, bcpow(2, 62))) === -1) {
            $stamp = bcsub(0, bcpow(2, 62));
        } elseif (bccomp($stamp, bcsub(bcpow(2, 63), bcpow(2, 62))) >= 0) {
            $stamp = bcsub(bcsub(bcpow(2, 63), bcpow(2, 62)), bcpow(10, -18));
        }
        $unix_seconds = bcdiv($stamp, 1, 0);

        $time['seconds'] = bcadd($unix_seconds, bcpow(2, 62), 0);
        $time['nano']    = gmp_strval(gmp_abs(bcmul(bcsub($stamp, $unix_seconds), bcpow(10, 9), 0)), 10);
        $time['atto']    = gmp_strval(gmp_abs(bcmul(bcsub(bcmul(bcsub($stamp, $unix_seconds), bcpow(10, 9), 9), bcmul(bccomp($unix_seconds, 0), $time['nano'])), bcpow(10, 9), 0)), 10);

        return $time;
    }

    public static function fromInternalToUnix($time)
    {
        return bcadd(bcsub($time['seconds'], bcpow(2, 62), 0), bcdiv(bcadd(bcdiv($time['atto'], bcpow(10, 9), 9), $time['nano'], 9), bcpow(10, 9), 18), 18);
    }

    public static function import($source)
    {
        return call_user_func(static::$timeConverters['import'][$this->getConverter($source)], $source);
    }

    public function convert($className)
    {
        return call_user_func(static::$timeConverters['convert'][$this->getConverter($className)], $this);
    }

    // Getters

    public function getInternalTime()
    {
        return $this->internalTime;
    }

    public function getDate($calendar = 'unix')
    {
        return call_user_func(static::$timeConverters['fromInternal'][$this->getCalendar($calendar)], $this->internalTime);
    }

    public function getDuration()
    {
        return $this->duration;
    }

    public function getEndTime()
    {
        return $this->endTime;
    }

    public function getEndDate($calendar = 'unix')
    {
        return call_user_func(static::$timeConverters['fromInternal'][$this->getCalendar($calendar)], $this->endTime);
    }

    // Comparison Functions

    protected function getInternalTimeAsString($time)
    {
        return "{$time['seconds']}.{$time['nano']}{$time['atto']}";
    }

    protected function getTimesByMode($a, $b, $mode = 'start')
    {
        switch ($mode) {
            case "duration":
                $times = [$a->duration, $b->duration];
                break;
            case "start-end":
                $times = [$this->getInternalTimeAsString($a->internalTime), $this->getInternalTimeAsString($b->endTime)];
                break;
            case "end-start":
                $times = [$this->getInternalTimeAsString($a->endTime), $this->getInternalTimeAsString($b->internalTime)];
                break;
            case "end":
                $times = [$this->getInternalTimeAsString($a->endTime), $this->getInternalTimeAsString($b->endTime)];
                break;
            case "start":
            default:
                $times = [$this->getInternalTimeAsString($a->internalTime), $this->getInternalTimeAsString($b->internalTime)];
                break;
        }

        return $times;
    }

    public function difference(Calends $compare, $mode = 'start')
    {
        $times = $this->getTimesByMode($this, $compare, $mode);

        return bcsub($times[0], $times[1]);
    }

    public static function compare(Calends $a, Calends $b, $mode = 'start')
    {
        $times = $this->getTimesByMode($a, $b, $mode);

        return bccomp($times[0], $times[1]);
    }

    public function isSame(Calends $compare)
    {
        return static::compare($this, $compare, 'start') === 0 && static::compare($this, $compare, 'end') === 0;
    }

    public function isDuring(Calends $compare)
    {
        return static::compare($this, $compare, 'start') >= 0 && static::compare($this, $compare, 'end') <= 0;
    }

    public function startsDuring(Calends $compare)
    {
        return static::compare($this, $compare, 'start') >= 0 && static::compare($this, $compare, 'start-end') <= 0;
    }

    public function endsDuring(Calends $compare)
    {
        return static::compare($this, $compare, 'end-start') >= 0 && static::compare($this, $compare, 'end') <= 0;
    }

    public function contains(Calends $compare)
    {
        return static::compare($this, $compare, 'start') <= 0 && static::compare($this, $compare, 'end') >= 0;
    }

    public function overlaps(Calends $compare)
    {
        return $this->startsDuring($compare) || $this->endsDuring($compare) || $compare->startsDuring($this) || $compare->endsDuring($this);
    }

    public function abuts(Calends $compare)
    {
        return static::compare($this, $compare, 'start-end') === 0 || static::compare($this, $compare, 'end-start') === 0;
    }

    public function isBefore(Calends $compare)
    {
        return static::compare($this, $compare, 'end-start') === -1;
    }

    public function startsBefore(Calends $compare)
    {
        return static::compare($this, $compare, 'start') === -1;
    }

    public function endsBefore(Calends $compare)
    {
        return static::compare($this, $compare, 'end') === -1;
    }

    public function isAfter(Calends $compare)
    {
        return static::compare($this, $compare, 'start-end') === +1;
    }

    public function startsAfter(Calends $compare)
    {
        return static::compare($this, $compare, 'start') === +1;
    }

    public function endsAfter(Calends $compare)
    {
        return static::compare($this, $compare, 'end') === +1;
    }

    public function isShorter(Calends $compare)
    {
        return static::compare($this, $compare, 'duration') === -1;
    }

    public function isSameDuration(Calends $compare)
    {
        return static::compare($this, $compare, 'duration') === 0;
    }

    public function isLonger(Calends $compare)
    {
        return static::compare($this, $compare, 'duration') === +1;
    }

    // Modification Functions

    public function add($offset, $calendar = 'unix')
    {
        return static::create(call_user_func(static::$timeConverters['offset'][$this->getCalendar($calendar)], $this->internalTime, $offset), $calendar);
    }

    public function subtract($offset, $calendar = 'unix')
    {
        return $this->add("-{$offset}", $calendar);
    }

    public function addFromEnd($offset, $calendar = 'unix')
    {
        return static::create(call_user_func(static::$timeConverters['offset'][$this->getCalendar($calendar)], $this->endTime, $offset), $calendar);
    }

    public function subtractFromEnd($offset, $calendar = 'unix')
    {
        return $this->addFromEnd("-{$offset}", $calendar);
    }

    public function next($offset = null, $calendar = 'unix')
    {
        if (is_null($offset)) {
            $offset = $this->duration;
        }

        return static::create(['start' => $this->getEndDate($calendar), 'end' => $this->addFromEnd($duration, $calendar)->getDate($calendar)], $calendar);
    }

    public function previous($offset = null, $calendar = 'unix')
    {
        if (is_null($offset)) {
            $offset = $this->duration;
        }

        return static::create(['start' => $this->subtract($duration, $calendar)->getDate($calendar), 'end' => $this->getDate($calendar)], $calendar);
    }

    // Range Functions

    public function setDate($date, $calendar = 'unix')
    {
        return static::create(['start' => $date, 'end' => $this->getEndDate($calendar)], $calendar);
    }

    public function setEndDate($date, $calendar = 'unix')
    {
        return static::create(['start' => $this->getDate($calendar), 'end' => $date], $calendar);
    }

    public function setDuration($duration, $calendar = 'unix')
    {
        return $this->setEndDate($this->add($duration, $calendar)->getDate('tai'), 'tai');
    }

    public function setDurationFromEnd($duration, $calendar = 'unix')
    {
        return $this->setDate($this->subtractFromEnd($duration, $calendar)->getDate('tai'), 'tai');
    }

    public function merge(Calends $composite)
    {
        $start = $this->startsBefore($composite) ? $this->getDate('tai') : $composite->getDate('tai');
        $end   = $this->endsAfter($composite) ? $this->getEndDate('tai') : $composite->getEndDate('tai');

        return static::create(['start' => $start, 'end' => $end], 'tai');
    }

    public function intersect(Calends $composite)
    {
        if ( ! $this->overlaps($composite)) {
            throw new InvalidCompositeRangeException('The ranges given do not overlap - they have no intersection.');
        }

        $start = $this->startsDuring($composite) ? $this->getDate('tai') : $composite->getDate('tai');
        $end   = $this->endsDuring($composite) ? $this->getEndDate('tai') : $composite->getEndDate('tai');

        return static::create(['start' => $start, 'end' => $end], 'tai');
    }

    public function gap(Calends $composite)
    {
        if ($this->overlaps($composite)) {
            throw new InvalidCompositeRangeException('The ranges given overlap - they have no gap.');
        }

        $start = $this->endsBefore($composite) ? $this->getEndDate('tai') : $composite->getEndDate('tai');
        $end   = $this->startsAfter($composite) ? $this->getDate('tai') : $composite->getDate('tai');

        return static::create(['start' => $start, 'end' => $end], 'tai');
    }

    // "Magic" Functions

    public function __invoke($calendar = 'unix')
    {
        return $this->getDate($calendar);
    }

    public function __toString()
    {
        return $this->getDate('tai');
    }

    public function serialize()
    {
        return serialize($this->jsonSerialize());
    }

    public function unserialize($str)
    {
        $this->__construct(unserialize($str), 'tai');
    }

    public function jsonSerialize()
    {
        return $this->duration == 0 ? $this->getDate('tai') : ['start' => $this->getDate('tai'), 'end' => $this->getEndDate('tai')];
    }
}
