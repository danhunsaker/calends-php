<?php

namespace Danhunsaker\Calends;

class CalendsTAI64 implements CalendarDefinitionInterface
{
    public static function toInternal($date)
    {
        $date = str_pad(str_pad($date, 16, '0', STR_PAD_LEFT), 32, '0', STR_PAD_RIGHT);

        $time = [
            'seconds' => gmp_strval(gmp_init('0x' . substr($date, 0, 16), 16), 10),
            'nano'    => gmp_strval(gmp_init('0x' . substr($date, 16, 8), 16), 10),
            'atto'    => gmp_strval(gmp_init('0x' . substr($date, 24, 8), 16), 10),
        ];

        if (bccomp($time['seconds'], bcpow(2, 63)) >= 0) {
            $time = [
                'seconds' => bcsub(bcpow(2, 63), 1, 0),
                'nano'    => '999999999',
                'atto'    => '999999999',
            ];
        }

        return $time;
    }

    public static function fromInternal($stamp)
    {
        return str_pad(gmp_strval(gmp_init($stamp['seconds'], 10), 16), 16, '0', STR_PAD_LEFT)
             . str_pad(gmp_strval(gmp_init($stamp['nano'], 10), 16), 8, '0', STR_PAD_LEFT)
             . str_pad(gmp_strval(gmp_init($stamp['atto'], 10), 16), 8, '0', STR_PAD_LEFT);
    }
}
