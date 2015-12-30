<?php

namespace Danhunsaker\Calends\Converter;

use Danhunsaker\Calends\Calends;

/**
 * Interface for converters
 *
 * Classes meant to convert a `Calends` object into other date/time objects, or
 * vice-versa, must implement this interface.
 *
 * @see https://github.com/danhunsaker/calends The official repo for the library
 * @author Daniel Hunsaker <dan.hunsaker+calends@gmail.com>
 * @copyright 2015-2016 Daniel Hunsaker
 * @license MIT
 */
interface ConverterInterface
{
    /**
     * Create a new instance of a Calends object from a different class
     *
     * @param object $source The object to convert from
     * @return Calends
     */
    public static function import($source);

    /**
     * Create a new instance of a different class from a Calends object
     *
     * @param Calends $cal The Calends object to convert
     * @return object
     */
    public static function convert(Calends $cal);
}
