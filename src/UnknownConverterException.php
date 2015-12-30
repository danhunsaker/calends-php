<?php

namespace Danhunsaker\Calends;

/**
 * Converter class not found
 *
 * This `Exception` is thrown when `Calends::registerClassConverter()` is passed
 * a class which doesn't exist.
 *
 * @see https://github.com/danhunsaker/calends The official repo for the library
 * @author Daniel Hunsaker <dan.hunsaker+calends@gmail.com>
 * @copyright 2015-2016 Daniel Hunsaker
 * @license MIT
 */
class UnknownConverterException extends \Exception
{
}
