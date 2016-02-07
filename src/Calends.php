<?php

namespace Danhunsaker\Calends;

use JsonSerializable;
use RMiller\Caser\Cased;
use Serializable;

/**
 * Arbitrary calendar systems in PHP
 *
 * This is the core class of the Calends library.  The entire "public API" of
 * the library is contained in this class, so generally speaking, it will be the
 * only one you need to use in your code, as the others are referenced
 * internally from here as needed.
 *
 * @see https://github.com/danhunsaker/calends The official repo for the library
 * @author Daniel Hunsaker <dan.hunsaker+calends@gmail.com>
 * @copyright 2015-2016 Daniel Hunsaker
 * @license MIT
 */
class Calends implements Serializable, JsonSerializable
{
    /**
     * @var string[] $internalTime Stores the internal time representation for the start date
     */
    protected $internalTime = ['seconds' => 0];

    /**
     * @var integer|string $duration Stores the duration of the object's date range in seconds
     */
    protected $duration = 0;

    /**
     * @var string[] $endTime Stores the internal time representation for the end date
     */
    protected $endTime = ['seconds' => 0];

    /**
     * @var (callable[])[] $timeConverters Stores all registered calendar definition and class converter functions
     */
    protected static $timeConverters = [
        'toInternal'   => [],
        'fromInternal' => [],
        'offset'       => [],
        'import'       => [],
        'convert'      => [],
    ];

    // Setup Functions

    /**
     * Create a new Calends object
     *
     * The Calends constructor.
     *
     * @api
     *
     * @param string|object|float|integer|(string|object|float|integer)[] $stamp
     *        Either a single date value to parse, or an array with start and
     *        end dates to parse
     * @param string $calendar The calendar system definition to parse $stamp
     *        with
     * @throws InvalidCalendarException
     * @throws UnknownCalendarException
     **/
    public function __construct($stamp = null, $calendar = 'unix')
    {
        static::registerCalendar('unix', __NAMESPACE__ . '\\Calendar\\Unix');
        static::registerCalendar('jdc', __NAMESPACE__ . '\\Calendar\\JulianDayCount');
        static::registerCalendar('tai', __NAMESPACE__ . '\\Calendar\\TAI64');

        if (is_array($stamp)) {
            $this->internalTime = call_user_func(static::$timeConverters['toInternal'][static::getCalendar($calendar)], $stamp['start']);
            $this->endTime      = call_user_func(static::$timeConverters['toInternal'][static::getCalendar($calendar)], $stamp['end']);
            $this->duration     = $this->difference($this, 'end-start');
        } else {
            $this->internalTime = call_user_func(static::$timeConverters['toInternal'][static::getCalendar($calendar)], $stamp);
            $this->endTime      = $this->internalTime;
            $this->duration     = 0;
        }
    }

    /**
     * Create a new Calends object
     *
     * Calls the Calends constructor in a static context rather than a
     * `new Class` context.  Useful for method chaining.
     *
     * @api
     *
     * @param string|object|float|integer|(string|object|float|integer)[] $stamp
     *        Either a single date value to parse, or an array with start and
     *        end dates to parse
     * @param string $calendar The calendar system definition to parse $stamp
     *        with
     * @throws InvalidCalendarException
     * @throws UnknownCalendarException
     * @return self
     **/
    public static function create($stamp = null, $calendar = 'unix')
    {
        return new static($stamp, $calendar);
    }

    /**
     * Canonicalize the calendar name and ensure it is available
     *
     * Transforms calendar names into camelCase, then ensures the appropriate
     * calendar system class/object is registered.  Will throw an
     * `UnknownCalendarException` for calendars it cannot find.
     *
     * @internal
     *
     * @param string $calendar The calendar system to retrieve
     * @throws InvalidCalendarException
     * @throws UnknownCalendarException
     * @return string The canonical index for accessing $calendar functions
     **/
    protected static function getCalendar($calendar)
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

    /**
     * Register a calendar system class/object
     *
     * Transforms $calendar to camelCase, then registers the appropriate methods
     * from $className for later use.
     *
     * @api
     *
     * @param string $calendar The calendar system name to register
     * @param string|object $className The calendar system class, or a calendar
     *        system object instance, to register under $calendar
     * @throws InvalidCalendarException
     * @return void
     **/
    public static function registerCalendar($calendar, $className)
    {
        $calendar = Cased::fromCamelCase($calendar)->asCamelCase();

        if (array_key_exists($calendar, static::$timeConverters['toInternal'])) {
            return;
        }

        if ( ! ((is_string($className) && class_exists($className) && is_a($className, 'Danhunsaker\Calends\Calendar\DefinitionInterface', true))
            || (is_object($className) && is_a($className, 'Danhunsaker\Calends\Calendar\ObjectDefinitionInterface')))) {
            throw new InvalidCalendarException('Not a valid calendar definition class name or instance: ' . var_export($className, true));
        }

        static::$timeConverters['toInternal'][$calendar]   = [$className, 'toInternal'];
        static::$timeConverters['fromInternal'][$calendar] = [$className, 'fromInternal'];
        static::$timeConverters['offset'][$calendar]       = [$className, 'offset'];
    }

    /**
     * Resolve the converter name and ensure it is available
     *
     * If an object is passed, resolves its class name, or otherwise uses the
     * passed value directly, to ensure the appropriate converter class is
     * registered.  Will throw an `UnknownConverterException` for converters it
     * cannot find.
     *
     * @internal
     *
     * @param string $converter The converter class to retrieve
     * @throws InvalidConverterException
     * @throws UnknownConverterException
     * @return string The canonical index for accessing the $converter
     **/
    protected static function getConverter($converter)
    {
        if (is_object($converter)) {
            $converter = get_class($converter);
        }

        if ( ! array_key_exists($converter, static::$timeConverters['import'])) {
            $className = __NAMESPACE__ . "\\Converter\\" . @array_pop(explode('\\', $converter));

            if (class_exists($className)) {
                static::registerConverter($converter, $className);
            } else {
                throw new UnknownConverterException("Can't find the '{$converter}' converter!");
            }
        }

        return $converter;
    }

    /**
     * Register a converter class
     *
     * Registers the apprpriate methods from $conversionClass under $className.
     *
     * @api
     *
     * @param string $className The name of the converter to register
     * @param string $conversionClass The converter class to register under
     *        $className
     * @throws InvalidConverterException
     * @return void
     **/
    public static function registerConverter($className, $conversionClass)
    {
        if (array_key_exists($className, static::$timeConverters['import'])) {
            return;
        }

        if ( ! (((is_string($conversionClass) && class_exists($conversionClass)) || is_object($conversionClass)) && is_a($conversionClass, 'Danhunsaker\Calends\Converter\ConverterInterface', true))) {
            throw new InvalidConverterException('Not a valid conversion class name or instance: ' . var_export($conversionClass, true));
        }

        static::$timeConverters['import'][$className]  = [$conversionClass, 'import'];
        static::$timeConverters['convert'][$className] = [$conversionClass, 'convert'];
    }

    // Conversion Functions

    /**
     * Convert a Unix timestamp to an internal TAI array
     *
     * @internal
     *
     * @param string|float|integer $stamp Unix timestamp to convert into the
     *        internal TAI array representation
     * @return string[]
     */
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

    /**
     * Convert an internal TAI array to a Unix timestamp
     *
     * @internal
     *
     * @param string[] $stamp The internal TAI array representation to convert
     *        into a Unix timestamp
     * @return string|float|integer
     */
    public static function fromInternalToUnix($time)
    {
        return bcadd(bcsub($time['seconds'], bcpow(2, 62), 0), bcdiv(bcadd(bcdiv($time['atto'], bcpow(10, 9), 9), $time['nano'], 9), bcpow(10, 9), 18), 18);
    }

    /**
     * Create a new instance of a Calends object from a different class
     *
     * @api
     *
     * @param object $source The object to convert from
     * @throws InvalidConverterException
     * @throws UnknownConverterException
     * @throws InvalidCalendarException
     * @throws UnknownCalendarException
     * @return self
     */
    public static function import($source)
    {
        return call_user_func(static::$timeConverters['import'][static::getConverter($source)], $source);
    }

    /**
     * Create a new instance of a different class from a Calends object
     *
     * @api
     *
     * @param string|object $className The name (or an instance) of the class to
     *        convert to
     * @throws InvalidConverterException
     * @throws UnknownConverterException
     * @return object
     */
    public function convert($className)
    {
        return call_user_func(static::$timeConverters['convert'][static::getConverter($className)], $this);
    }

    // Getters

    /**
     * Gets the internal TAI representation of the start time
     *
     * @api
     *
     * @return string[]
     **/
    public function getInternalTime()
    {
        return $this->internalTime;
    }

    /**
     * Format the (start) date/time according to a calendar system
     *
     * @api
     *
     * @param string $calendar The calendar system definition to convert to
     * @throws InvalidCalendarException
     * @throws UnknownCalendarException
     * @return string|object|float|integer
     **/
    public function getDate($calendar = 'unix')
    {
        return call_user_func(static::$timeConverters['fromInternal'][static::getCalendar($calendar)], $this->internalTime);
    }

    /**
     * Gets the number of seconds between the start and end times
     *
     * @api
     *
     * @return float|integer
     **/
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Gets the internal TAI representation of the end time
     *
     * @api
     *
     * @return string[]
     **/
    public function getInternalEndTime()
    {
        return $this->endTime;
    }

    /**
     * Format the end date/time according to a calendar system
     *
     * @api
     *
     * @param string $calendar The calendar system definition to convert to
     * @throws InvalidCalendarException
     * @throws UnknownCalendarException
     * @return string|object|float|integer
     **/
    public function getEndDate($calendar = 'unix')
    {
        return call_user_func(static::$timeConverters['fromInternal'][static::getCalendar($calendar)], $this->endTime);
    }

    // Comparison Functions

    /**
     * Collapses an internal TAI time to a single string value usable by BCMath
     *
     * @internal
     *
     * @param string[] $time The internal TAI representation to collapse
     * @return string
     **/
    protected static function getInternalTimeAsString($time)
    {
        $frac = str_pad($time['nano'], 9, '0', STR_PAD_LEFT) . str_pad($time['atto'], 9, '0', STR_PAD_LEFT);

        return "{$time['seconds']}.{$frac}";
    }

    /**
     * Retrieves the start time, end time, or duration from two Calends objects
     *
     * @internal
     *
     * @param Calends $a One of the two Calends objects to grab times from
     * @param Calends $b One of the two Calends objects to grab times from
     * @param string $mode One of start, end, start-end, end-start, or duration
     * @return (string|float|integer)[]
     **/
    protected static function getTimesByMode(Calends $a, Calends $b, $mode = 'start')
    {
        switch ($mode) {
            case "duration":
                $times = [$a->duration, $b->duration];
                break;
            case "start-end":
                $times = [static::getInternalTimeAsString($a->internalTime), static::getInternalTimeAsString($b->endTime)];
                break;
            case "end-start":
                $times = [static::getInternalTimeAsString($a->endTime), static::getInternalTimeAsString($b->internalTime)];
                break;
            case "end":
                $times = [static::getInternalTimeAsString($a->endTime), static::getInternalTimeAsString($b->endTime)];
                break;
            case "start":
            default:
                $times = [static::getInternalTimeAsString($a->internalTime), static::getInternalTimeAsString($b->internalTime)];
        }

        return $times;
    }

    /**
     * Retrieves the difference between the current Calends object and another
     *
     * @api
     *
     * @param Calends $compare The Calends object to calculate the difference of
     * @param string $mode One of start, end, start-end, end-start, or duration
     * @return string|float|integer
     **/
    public function difference(Calends $compare, $mode = 'start')
    {
        $times = static::getTimesByMode($this, $compare, $mode);

        return bcsub($times[0], $times[1]);
    }

    /**
     * Compares the start time, end time, or duration of two Calends objects
     *
     * @api
     *
     * @param Calends $a One of the two Calends objects to compare times from
     * @param Calends $b One of the two Calends objects to compare times from
     * @param string $mode One of start, end, start-end, end-start, or duration
     * @return integer Values are -1 if a < b; 0 if a == b; +1 if a > b
     **/
    public static function compare(Calends $a, Calends $b, $mode = 'start')
    {
        $times = static::getTimesByMode($a, $b, $mode);

        return bccomp($times[0], $times[1]);
    }

    /**
     * Checks whether the current object has the same value(s) as another
     *
     * @api
     *
     * @param Calends $compare The Calends object to compare
     * @return boolean
     **/
    public function isSame(Calends $compare)
    {
        return static::compare($this, $compare, 'start') === 0 && static::compare($this, $compare, 'end') === 0;
    }

    /**
     * Checks whether the current object fits entirely within another
     *
     * @api
     *
     * @param Calends $compare The Calends object to compare
     * @return boolean
     **/
    public function isDuring(Calends $compare)
    {
        return static::compare($this, $compare, 'start') >= 0 && static::compare($this, $compare, 'end') <= 0;
    }

    /**
     * Checks whether the current object has its start point between another's
     * start and end points.
     *
     * @api
     *
     * @param Calends $compare The Calends object to compare
     * @return boolean
     **/
    public function startsDuring(Calends $compare)
    {
        return static::compare($this, $compare, 'start') >= 0 && (static::compare($this, $compare, 'start-end') < 0 || ($this->duration == 0 && static::compare($this, $compare, 'start-end') === 0));
    }

    /**
     * Checks whether the current object has its end point between another's
     * start and end points.
     *
     * @api
     *
     * @param Calends $compare The Calends object to compare
     * @return boolean
     **/
    public function endsDuring(Calends $compare)
    {
        return static::compare($this, $compare, 'end') <= 0 && (static::compare($this, $compare, 'end-start') > 0 || ($this->duration == 0 && static::compare($this, $compare, 'end-start') === 0));
    }

    /**
     * Checks whether another object fits entirely within the current one
     *
     * @api
     *
     * @param Calends $compare The Calends object to compare
     * @return boolean
     **/
    public function contains(Calends $compare)
    {
        return static::compare($this, $compare, 'start') <= 0 && static::compare($this, $compare, 'end') >= 0;
    }

    /**
     * Checks whether either of the current object's endpoints occur within
     * another object's period, or vice-versa
     *
     * @api
     *
     * @param Calends $compare The Calends object to compare
     * @return boolean
     **/
    public function overlaps(Calends $compare)
    {
        return $this->startsDuring($compare) || $compare->startsDuring($this) || $this->endsDuring($compare) || $compare->endsDuring($this);
    }

    /**
     * Checks whether neither of the current object's endpoints occur within
     * another object's period, and vice-versa
     *
     * @api
     *
     * @param Calends $compare The Calends object to compare
     * @return boolean
     **/
    public function abuts(Calends $compare)
    {
        return (static::compare($this, $compare, 'start-end') === 0 || static::compare($this, $compare, 'end-start') === 0) && ! ($this->contains($compare) || $compare->contains($this));
    }

    /**
     * Checks whether both of the current object's endpoints occur before
     * another object's start point
     *
     * @api
     *
     * @param Calends $compare The Calends object to compare
     * @return boolean
     **/
    public function isBefore(Calends $compare)
    {
        return static::compare($this, $compare, 'end-start') <= 0 && static::compare($this, $compare, 'start') === -1;
    }

    /**
     * Checks whether the current object's start point occurs before another's
     *
     * @api
     *
     * @param Calends $compare The Calends object to compare
     * @return boolean
     **/
    public function startsBefore(Calends $compare)
    {
        return static::compare($this, $compare, 'start') === -1;
    }

    /**
     * Checks whether the current object's end point occurs before another's
     *
     * @api
     *
     * @param Calends $compare The Calends object to compare
     * @return boolean
     **/
    public function endsBefore(Calends $compare)
    {
        return static::compare($this, $compare, 'end') === -1;
    }

    /**
     * Checks whether both of the current object's endpoints occur after
     * another object's end point
     *
     * @api
     *
     * @param Calends $compare The Calends object to compare
     * @return boolean
     **/
    public function isAfter(Calends $compare)
    {
        return static::compare($this, $compare, 'start-end') >= 0 && static::compare($this, $compare, 'end') === +1;
    }

    /**
     * Checks whether the current object's start point occurs after another's
     *
     * @api
     *
     * @param Calends $compare The Calends object to compare
     * @return boolean
     **/
    public function startsAfter(Calends $compare)
    {
        return static::compare($this, $compare, 'start') === +1;
    }

    /**
     * Checks whether the current object's end point occurs after another's
     *
     * @api
     *
     * @param Calends $compare The Calends object to compare
     * @return boolean
     **/
    public function endsAfter(Calends $compare)
    {
        return static::compare($this, $compare, 'end') === +1;
    }

    /**
     * Checks whether the current object's duration is less than another's
     *
     * @api
     *
     * @param Calends $compare The Calends object to compare
     * @return boolean
     **/
    public function isShorter(Calends $compare)
    {
        return static::compare($this, $compare, 'duration') === -1;
    }

    /**
     * Checks whether the current object's duration is equal to another's
     *
     * @api
     *
     * @param Calends $compare The Calends object to compare
     * @return boolean
     **/
    public function isSameDuration(Calends $compare)
    {
        return static::compare($this, $compare, 'duration') === 0;
    }

    /**
     * Checks whether the current object's duration is greater than another's
     *
     * @api
     *
     * @param Calends $compare The Calends object to compare
     * @return boolean
     **/
    public function isLonger(Calends $compare)
    {
        return static::compare($this, $compare, 'duration') === +1;
    }

    // Modification Functions

    /**
     * Create a new Calends object a given offset after the current start point
     *
     * @api
     *
     * @param string|object|float|integer $offset A date offset value to parse
     * @param string $calendar The calendar system definition to parse $offset
     *        with
     * @throws InvalidCalendarException
     * @throws UnknownCalendarException
     * @return self
     **/
    public function add($offset, $calendar = 'unix')
    {
        return $this->setDate(call_user_func(
            static::$timeConverters['fromInternal'][static::getCalendar('tai')],
            call_user_func(static::$timeConverters['offset'][static::getCalendar($calendar)], $this->internalTime, $offset)
        ), 'tai');
    }

    /**
     * Create a new Calends object a given offset before the current start point
     *
     * @api
     *
     * @param string|object|float|integer $offset A date offset value to parse
     * @param string $calendar The calendar system definition to parse $offset
     *        with
     * @throws InvalidCalendarException
     * @throws UnknownCalendarException
     * @return self
     **/
    public function subtract($offset, $calendar = 'unix')
    {
        return $this->add("-{$offset}", $calendar);
    }

    /**
     * Create a new Calends object a given offset after the current end point
     *
     * @api
     *
     * @param string|object|float|integer $offset A date offset value to parse
     * @param string $calendar The calendar system definition to parse $offset
     *        with
     * @throws InvalidCalendarException
     * @throws UnknownCalendarException
     * @return self
     **/
    public function addFromEnd($offset, $calendar = 'unix')
    {
        return $this->setEndDate(call_user_func(
            static::$timeConverters['fromInternal'][static::getCalendar('tai')],
            call_user_func(static::$timeConverters['offset'][static::getCalendar($calendar)], $this->endTime, $offset)
        ), 'tai');
    }

    /**
     * Create a new Calends object a given offset before the current end point
     *
     * @api
     *
     * @param string|object|float|integer $offset A date offset value to parse
     * @param string $calendar The calendar system definition to parse $offset
     *        with
     * @throws InvalidCalendarException
     * @throws UnknownCalendarException
     * @return self
     **/
    public function subtractFromEnd($offset, $calendar = 'unix')
    {
        return $this->addFromEnd("-{$offset}", $calendar);
    }

    /**
     * Create a new Calends object with a range spanning a given offset, and
     * starting at the current end point
     *
     * @api
     *
     * @param string|object|float|integer $offset A date offset value to parse
     * @param string $calendar The calendar system definition to parse $offset
     *        with
     * @throws InvalidCalendarException
     * @throws UnknownCalendarException
     * @return self
     **/
    public function next($offset = null, $calendar = 'unix')
    {
        if (is_null($offset)) {
            $offset   = $this->duration;
            $calendar = 'unix';
        }

        return static::create(['start' => $this->getEndDate('tai'), 'end' => $this->addFromEnd($offset, $calendar)->getEndDate('tai')], 'tai');
    }

    /**
     * Create a new Calends object with a range spanning a given offset, and
     * ending at the current start point
     *
     * @api
     *
     * @param string|object|float|integer $offset A date offset value to parse
     * @param string $calendar The calendar system definition to parse $offset
     *        with
     * @throws InvalidCalendarException
     * @throws UnknownCalendarException
     * @return self
     **/
    public function previous($offset = null, $calendar = 'unix')
    {
        if (is_null($offset)) {
            $offset   = $this->duration;
            $calendar = 'unix';
        }

        return static::create(['start' => $this->subtract($offset, $calendar)->getDate('tai'), 'end' => $this->getDate('tai')], 'tai');
    }

    // Range Functions

    /**
     * Create a new Calends object starting at a given date
     *
     * @api
     *
     * @param string|object|float|integer $date The new date of the start point
     * @param string $calendar The calendar system definition to parse $date
     *        with
     * @throws InvalidCalendarException
     * @throws UnknownCalendarException
     * @return self
     **/
    public function setDate($date, $calendar = 'unix')
    {
        return static::create(['start' => $date, 'end' => $this->getEndDate($calendar)], $calendar);
    }

    /**
     * Create a new Calends object ending at a given date
     *
     * @api
     *
     * @param string|object|float|integer $date The new date of the end point
     * @param string $calendar The calendar system definition to parse $date
     *        with
     * @throws InvalidCalendarException
     * @throws UnknownCalendarException
     * @return self
     **/
    public function setEndDate($date, $calendar = 'unix')
    {
        return static::create(['start' => $this->getDate($calendar), 'end' => $date], $calendar);
    }

    /**
     * Create a new Calends object spanning from the current start point for a
     * given duration
     *
     * @api
     *
     * @param string|object|float|integer $duration A date offset value to parse
     * @param string $calendar The calendar system definition to parse $duration
     *        with
     * @throws InvalidCalendarException
     * @throws UnknownCalendarException
     * @return self
     **/
    public function setDuration($duration, $calendar = 'unix')
    {
        return $this->setEndDate($this->add($duration, $calendar)->getDate('tai'), 'tai');
    }

    /**
     * Create a new Calends object spanning for a given duration to the current
     * end point
     *
     * @api
     *
     * @param string|object|float|integer $duration A date offset value to parse
     * @param string $calendar The calendar system definition to parse $duration
     *        with
     * @throws InvalidCalendarException
     * @throws UnknownCalendarException
     * @return self
     **/
    public function setDurationFromEnd($duration, $calendar = 'unix')
    {
        return $this->setDate($this->subtractFromEnd($duration, $calendar)->getEndDate('tai'), 'tai');
    }

    /**
     * Merges two Calends objects
     *
     * Creates a new `Calends` object with the earlier start and later end
     * points of the current object and another one
     *
     * @api
     *
     * @param Calends $composite The Calends object to merge
     * @throws InvalidCalendarException
     * @throws UnknownCalendarException
     * @return self
     **/
    public function merge(Calends $composite)
    {
        $start = $this->startsBefore($composite) ? $this->getDate('tai') : $composite->getDate('tai');
        $end   = $this->endsAfter($composite) ? $this->getEndDate('tai') : $composite->getEndDate('tai');

        return static::create(['start' => $start, 'end' => $end], 'tai');
    }

    /**
     * Gets the intersection of two Calends objects
     *
     * Creates a new `Calends` object with the overlapping time between the
     * current object and another one
     *
     * @api
     *
     * @param Calends $composite The Calends object to intersect
     * @throws InvalidCompositeRangeException
     * @throws InvalidCalendarException
     * @throws UnknownCalendarException
     * @return self
     **/
    public function intersect(Calends $composite)
    {
        if ( ! $this->overlaps($composite)) {
            throw new InvalidCompositeRangeException('The ranges given do not overlap - they have no intersection.');
        }

        $start = $this->startsDuring($composite) ? $this->getDate('tai') : $composite->getDate('tai');
        $end   = $this->endsDuring($composite) ? $this->getEndDate('tai') : $composite->getEndDate('tai');

        return static::create(['start' => $start, 'end' => $end], 'tai');
    }

    /**
     * Gets the gap between two Calends objects
     *
     * Creates a new `Calends` object with the gap in time between the current
     * object and another one
     *
     * @api
     *
     * @param Calends $composite The Calends object to get the gap from
     * @throws InvalidCompositeRangeException
     * @throws InvalidCalendarException
     * @throws UnknownCalendarException
     * @return self
     **/
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

    /**
     * Format the current date(s)/time(s) according to a calendar system
     *
     * @api
     *
     * @param string $calendar The calendar system definition to convert to
     * @throws InvalidCalendarException
     * @throws UnknownCalendarException
     * @return string|object|float|integer|(string|object|float|integer)[]
     **/
    public function __invoke($calendar = 'unix')
    {
        return $this->duration == 0 ? $this->getDate($calendar) : ['start' => $this->getDate($calendar), 'end' => $this->getEndDate($calendar)];
    }

    /**
     * Convert the start date/time to the TAI64NA external format
     *
     * @api
     *
     * @throws InvalidCalendarException
     * @throws UnknownCalendarException
     * @return string|object|float|integer
     **/
    public function __toString()
    {
        return $this->getDate('tai');
    }

    /**
     * Serialize the current object to a string for later retrieval
     *
     * @api
     *
     * @throws InvalidCalendarException
     * @throws UnknownCalendarException
     * @return string|object|float|integer|(string|object|float|integer)[]
     **/
    public function serialize()
    {
        return serialize($this('tai'));
    }

    /**
     * Restores a serialized value into the current object
     *
     * @api
     *
     * @param string $str A serialized representation of a Calends object
     * @throws InvalidCalendarException
     * @throws UnknownCalendarException
     * @return string|object|float|integer|(string|object|float|integer)[]
     **/
    public function unserialize($str)
    {
        $this->__construct(unserialize($str), 'tai');
    }

    /**
     * Serialize the current object into a JSON string for use elsewhere
     *
     * @api
     *
     * @throws InvalidCalendarException
     * @throws UnknownCalendarException
     * @return string|object|float|integer|(string|object|float|integer)[]
     **/
    public function jsonSerialize()
    {
        return $this('tai');
    }
}

bcscale(18);
