<?php

namespace Danhunsaker\Calends;

/**
 * Calendar class doesn't implement correct interface
 *
 * This `Exception` is thrown when `Calends::registerCalendar()` is passed a
 * class or object which doesn't implement the `Calendar\DefinitionInterface` or
 * the `Calendar\ObjectDefinitionInterface`.
 *
 * @see https://github.com/danhunsaker/calends The official repo for the library
 * @author Daniel Hunsaker <dan.hunsaker+calends@gmail.com>
 * @copyright 2015-2016 Daniel Hunsaker
 * @license MIT
 */
class InvalidCalendarException extends \Exception
{
}
