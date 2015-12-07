<?php

namespace Danhunsaker\Calends;

class Calends
{
    protected $internalTime = ['seconds' => 0];

    protected $timeConverters = [
        'toInternal' => [],
        'fromInternal' => [],
    ];

    public function __construct($stamp = null, $format = 'unix')
    {
        $this->setupConverters();

        $this->internalTime = $this->timeConverters['toInternal'][$format]($stamp);
    }

    protected function setupConverters()
    {
        bcscale(18);

        $this->timeConverters['toInternal']['unix'] = [$this, 'toInternalFromUnix'];

        $this->timeConverters['toInternal']['jdc'] = function ($stamp) {
            return $this->toInternalFromUnix(bcmul(bcsub($stamp, 2440587.5), 86400));
        };

        $this->timeConverters['toInternal']['tai'] = function ($stamp) {
            $stamp = str_pad(str_pad($stamp, 16, '0', STR_PAD_LEFT), 32, '0', STR_PAD_RIGHT);

            $time = [
                'seconds' => gmp_strval(gmp_init('0x' . substr($stamp, 0, 16), 16), 10),
                'nano' => gmp_strval(gmp_init('0x' . substr($stamp, 16, 8), 16), 10),
                'atto' => gmp_strval(gmp_init('0x' . substr($stamp, 24, 8), 16), 10),
            ];

            if (bccomp($time['seconds'], bcpow(2, 63)) >= 0)
            {
                $time = [
                    'seconds' => bcsub(bcpow(2, 63), 1, 0),
                    'nano' => '999999999',
                    'atto' => '999999999',
                ];
            }

            return $time;
        };

        $this->timeConverters['fromInternal']['unix'] = [$this, 'fromInternalToUnix'];

        $this->timeConverters['fromInternal']['jdc'] = function ($time) {
            return bcadd(bcdiv($this->fromInternalToUnix($time), 86400), 2440587.5);
        };

        $this->timeConverters['fromInternal']['tai'] = function ($time) {
            return str_pad(gmp_strval(gmp_init($time['seconds'], 10), 16), 16, '0', STR_PAD_LEFT)
                 . str_pad(gmp_strval(gmp_init($time['nano'], 10), 16), 8, '0', STR_PAD_LEFT)
                 . str_pad(gmp_strval(gmp_init($time['atto'], 10), 16), 8, '0', STR_PAD_LEFT);
        };
    }

    protected function toInternalFromUnix($stamp = null)
    {
        $stamp = is_null($stamp) ? microtime(true) : $stamp;

        $time = [];
        if (bccomp($stamp, bcsub(0, bcpow(2, 62))) === -1)
        {
            $stamp = bcsub(0, bcpow(2, 62));
        }
        elseif (bccomp($stamp, bcsub(bcpow(2, 63), bcpow(2, 62))) >= 0)
        {
            $stamp = bcsub(bcsub(bcpow(2, 63), bcpow(2, 62)), bcpow(10, -18));
        }
        $unix_seconds = bcdiv($stamp, 1, 0);

        $time['seconds'] = bcadd($unix_seconds, bcpow(2, 62), 0);
        $time['nano'] = gmp_strval(gmp_abs(bcmul(bcsub($stamp, $unix_seconds), bcpow(10, 9), 0)), 10);
        $time['atto'] = gmp_strval(gmp_abs(bcmul(bcsub(bcmul(bcsub($stamp, $unix_seconds), bcpow(10, 9), 9), bcmul(bccomp($unix_seconds, 0), $time['nano'])), bcpow(10, 9), 0)), 10);

        return $time;
    }

    protected function fromInternalToUnix($time)
    {
        return bcadd(bcsub($time['seconds'], bcpow(2, 62), 0), bcdiv(bcadd(bcdiv($time['atto'], bcpow(10, 9), 9), $time['nano'], 9), bcpow(10, 9), 18), 18);
    }

    public function getInternalTime()
    {
        return $this->internalTime;
    }

    public function getDate($format = 'unix')
    {
        return $this->timeConverters['fromInternal'][$format]($this->internalTime);
    }
}
