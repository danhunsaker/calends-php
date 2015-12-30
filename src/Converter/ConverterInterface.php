<?php

namespace Danhunsaker\Calends\Converter;

use Danhunsaker\Calends\Calends;

interface ConverterInterface
{
    // Create a new instance of a Calends object from a different class
    public static function import($source);

    // Create a new instance of a different class from a Calends object
    public static function convert(Calends $cal);
}
