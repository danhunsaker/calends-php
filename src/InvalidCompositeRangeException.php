<?php

namespace Danhunsaker\Calends;

/**
 * Calends objects don't have a gap or overlap
 *
 * This `Exception` is thrown when `Calends::gap()` is passed a `Calends` object
 * which overlaps the calling object, or when `Calends::intersect()` is passed a
 * `Calends` object which doesn't overlap the calling object.  Since no valid
 * range exists in either of these cases, this `Exception` is thrown instead.
 *
 * @see https://github.com/danhunsaker/calends The official repo for the library
 * @author Daniel Hunsaker <dan.hunsaker+calends@gmail.com>
 * @copyright 2015-2016 Daniel Hunsaker
 * @license MIT
 */
class InvalidCompositeRangeException extends \Exception
{
}
