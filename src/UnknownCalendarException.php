<?php

namespace Danhunsaker\Calends;

/**
 * Calendar class not found
 *
 * This `Exception` is thrown when `Calends::registerCalendar()` is passed a
 * class which doesn't exist.
 *
 * @see https://github.com/danhunsaker/calends The official repo for the library
 * @author Daniel Hunsaker <dan.hunsaker+calends@gmail.com>
 * @copyright 2015-2016 Daniel Hunsaker
 * @license MIT
 */
class UnknownCalendarException extends \Exception
{
}
