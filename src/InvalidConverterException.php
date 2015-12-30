<?php

namespace Danhunsaker\Calends;

/**
 * Converter class doesn't implement correct interface
 *
 * This `Exception` is thrown when `Calends::registerClassConverter()` is passed
 * a class or object which doesn't implement the `Converter\ConverterInterface`.
 *
 * @see https://github.com/danhunsaker/calends The official repo for the library
 * @author Daniel Hunsaker <dan.hunsaker+calends@gmail.com>
 * @copyright 2015-2016 Daniel Hunsaker
 * @license MIT
 */
class InvalidConverterException extends \Exception
{
}
