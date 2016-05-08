<?php

namespace Danhunsaker\Calends\Calendar;

/**
 * Class-style (static) interface for calendars
 *
 * Classes meant to provide access to a new calendar system via static methods
 * must implement this interface.  This is the default and preferred interface
 * for calendar implementations, but there may be cases where using the
 * `Calendar\ObjectDefinitionInterface` makes more sense.
 *
 * @see https://github.com/danhunsaker/calends The official repo for the library
 * @author Daniel Hunsaker <dan.hunsaker+calends@gmail.com>
 * @copyright 2015-2016 Daniel Hunsaker
 * @license MIT
 */
interface DefinitionInterface
{
    /**
     * Convert a date representation to an internal TAI array
     *
     * @param string|object $date Value to parse/convert into the internal TAI
     *        array representation
     * @return string[]
     */
    public static function toInternal($date);

    /**
     * Convert an internal TAI array to a date representation
     *
     * @param string[] $stamp The internal TAI array representation to convert
     *        into the appropriate date/time value
     * @param string $format Optional date format string; may be ignored
     * @return mixed
     */
    public static function fromInternal($stamp, $format);

    /**
     * Calculate the TAI array at a given offset from another TAI array
     *
     * @param string[] $stamp The internal TAI array to offset
     * @param float|integer|string|object $offset The offset to calculate
     * @return string[]
     */
    public static function offset($stamp, $offset);
}
