<?php

namespace Danhunsaker\Calends;

use RMiller\Caser\Cased;
use Serializable;
use JsonSerializable;

class Calends implements Serializable, JsonSerializable
{
    protected $internalTime = ['seconds' => 0];

    protected static $timeConverters = [
        'toInternal'   => [],
        'fromInternal' => [],
    ];

    public function __construct($stamp = null, $calendar = 'unix')
    {
        bcscale(18);

        static::registerCalendar('unix', __NAMESPACE__ . '\\Calendar\\Unix');
        static::registerCalendar('jdc', __NAMESPACE__ . '\\Calendar\\JulianDayCount');
        static::registerCalendar('tai', __NAMESPACE__ . '\\Calendar\\TAI64');
        $this->internalTime = call_user_func(static::$timeConverters['toInternal'][$this->getCalendar($calendar)], $stamp);
    }

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

    public function getInternalTime()
    {
        return $this->internalTime;
    }

    public function getDate($calendar = 'unix')
    {
        return call_user_func(static::$timeConverters['fromInternal'][$this->getCalendar($calendar)], $this->internalTime);
    }

    protected function getCalendar($calendar)
    {
        $calendar = Cased::fromCamelCase($calendar)->asCamelCase();

        if ( ! array_key_exists($calendar, static::$timeConverters['toInternal'])) {
            $className = __NAMESPACE__ . '\\Calendar\\' . Cased::fromCamelCase($calendar)->asPascalCase();

            if (class_exists($className)) {
                static::registerCalendar($calendar, $className);
            } else {
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

        static::$timeConverters['toInternal'][$calendar]   = [$className, 'toInternal'];
        static::$timeConverters['fromInternal'][$calendar] = [$className, 'fromInternal'];
    }

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
        return $this->getDate('tai');
    }

    public function unserialize($str)
    {
        $this->__construct($str, 'tai');
    }

    public function jsonSerialize()
    {
        return $this->__toString();
    }
}
