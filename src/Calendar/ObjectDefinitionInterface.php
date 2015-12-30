<?php

namespace Danhunsaker\Calends\Calendar;

/**
 * Object-style interface for calendars
 *
 * Classes meant to provide access to new calendar systems from an object
 * instance must implement this interface.  This allows a single class to handle
 * multiple calendar systems, selecting the appropriate one based on the object
 * instance being called.  It is identical to the `Calendar\DefinitionInterface`
 * except for using instance methods in place of static ones.
 *
 * @see https://github.com/danhunsaker/calends The official repo for the library
 * @author Daniel Hunsaker <dan.hunsaker+calends@gmail.com>
 * @copyright 2015-2016 Daniel Hunsaker
 * @license MIT
 */
interface ObjectDefinitionInterface
{
    /**
     * Convert a date representation to an internal TAI array
     *
     * @param string|object $date Value to parse/convert into the internal TAI
     *        array representation
     * @return string[]
     */
    public function toInternal($date);

    /**
     * Convert an internal TAI array to a date representation
     *
     * @param string[] $stamp The internal TAI array representation to convert
     *        into the appropriate date/time value
     * @return mixed
     */
    public function fromInternal($stamp);

    /**
     * Calculate the TAI array at a given offset from another TAI array
     *
     * @param string[] $stamp The internal TAI array to offset
     * @param float|integer|string|object $offset The offset to calculate
     * @return string[]
     */
    public function offset($stamp, $offset);
}
