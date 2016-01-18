<?php

namespace Danhunsaker\Calends\Converter;

use Danhunsaker\Calends\Calends;
use League\Period\Period as Source;
use DateTimeImmutable;

/**
 * Convert between Calends and Period objects
 *
 * @see https://github.com/danhunsaker/calends The official repo for the library
 * @author Daniel Hunsaker <dan.hunsaker+calends@gmail.com>
 * @copyright 2015-2016 Daniel Hunsaker
 * @license MIT
 */
class Period implements ConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public static function import($source)
    {
        return Calends::create([
            'start' => $source->getStartDate()->getTimestamp(),
            'end'   => $source->getEndDate()->getTimestamp()
        ], 'unix');
    }

    /**
     * {@inheritdoc}
     */
    public static function convert(Calends $cal)
    {
        return new Source(DateTimeImmutable::createFromFormat('U.u', rtrim(rtrim($cal->getDate('unix'), '0'), '.')),
                          DateTimeImmutable::createFromFormat('U.u', rtrim(rtrim($cal->getEndDate('unix'), '0'), '.')));
    }
}
