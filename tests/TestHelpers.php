<?php

namespace Danhunsaker\Calends\Tests;

use DB;

class TestHelpers
{
    public static function ensureEloquentSampleCalendar()
    {
        if ( ! defined('MIGRATE_SILENTLY')) {
            define('MIGRATE_SILENTLY', true);
        }
        require_once(__DIR__ . '/../extra/non-laravel-migrate.php');

        if (DB::table('calendars')->where('name', 'eloquent')->count() > 0) {
            return;
        }

        $calId               = DB::table('calendars')->insertGetId(['name' => 'eloquent', 'description' => 'A test calendar.  Feel free to destroy it.']);
        list($monId, $dayId) = static::defineEloquentUnits($calId);
        static::defineEloquentUnitLengths($monId, $dayId);
    }

    protected static function defineEloquentUnits($calId)
    {
        $secId = DB::table('units')->insertGetId([
            'calendar_id'   => $calId, 'singular_name' => 'second',
            'plural_name'   => 'seconds', 'scale_amount' => 1,
            'scale_inverse' => false, 'scale_to' => 0,
            'uses_zero'     => true, 'unix_epoch' => 0, 'is_auxiliary' => false,
        ]);

        $minId = DB::table('units')->insertGetId([
            'calendar_id'   => $calId, 'singular_name' => 'minute',
            'plural_name'   => 'minutes', 'scale_amount' => 60,
            'scale_inverse' => false, 'scale_to' => $secId,
            'uses_zero'     => true, 'unix_epoch' => 0, 'is_auxiliary' => false,
        ]);

        $hrId = DB::table('units')->insertGetId([
            'calendar_id'   => $calId, 'singular_name' => 'hour',
            'plural_name'   => 'hours', 'scale_amount' => 60,
            'scale_inverse' => false, 'scale_to' => $minId,
            'uses_zero'     => true, 'unix_epoch' => 0, 'is_auxiliary' => false,
        ]);

        $dayId = DB::table('units')->insertGetId([
            'calendar_id'   => $calId, 'singular_name' => 'day',
            'plural_name'   => 'days', 'scale_amount' => 24,
            'scale_inverse' => false, 'scale_to' => $hrId,
            'uses_zero'     => false, 'unix_epoch' => 1, 'is_auxiliary' => false,
        ]);

        $monId = DB::table('units')->insertGetId([
            'calendar_id'   => $calId, 'singular_name' => 'month',
            'plural_name'   => 'months', 'scale_amount' => null,
            'scale_inverse' => false, 'scale_to' => $dayId,
            'uses_zero'     => false, 'unix_epoch' => 1, 'is_auxiliary' => false,
        ]);

        $yrId = DB::table('units')->insertGetId([
            'calendar_id'   => $calId, 'singular_name' => 'year',
            'plural_name'   => 'years', 'scale_amount' => 12,
            'scale_inverse' => false, 'scale_to' => $monId,
            'uses_zero'     => true, 'unix_epoch' => 1970, 'is_auxiliary' => false,
        ]);

        $msId = DB::table('units')->insertGetId([
            'calendar_id'   => $calId, 'singular_name' => 'millisecond',
            'plural_name'   => 'milliseconds', 'scale_amount' => 1000,
            'scale_inverse' => true, 'scale_to' => $secId,
            'uses_zero'     => true, 'unix_epoch' => null, 'is_auxiliary' => true,
        ]);

        $usId = DB::table('units')->insertGetId([
            'calendar_id'   => $calId, 'singular_name' => 'microsecond',
            'plural_name'   => 'microseconds', 'scale_amount' => 1000,
            'scale_inverse' => true, 'scale_to' => $msId,
            'uses_zero'     => true, 'unix_epoch' => null, 'is_auxiliary' => true,
        ]);

        $nsId = DB::table('units')->insertGetId([
            'calendar_id'   => $calId, 'singular_name' => 'nanosecond',
            'plural_name'   => 'nanoseconds', 'scale_amount' => 1000,
            'scale_inverse' => true, 'scale_to' => $usId,
            'uses_zero'     => true, 'unix_epoch' => null, 'is_auxiliary' => true,
        ]);

        $psId = DB::table('units')->insertGetId([
            'calendar_id'   => $calId, 'singular_name' => 'picosecond',
            'plural_name'   => 'picoseconds', 'scale_amount' => 1000,
            'scale_inverse' => true, 'scale_to' => $nsId,
            'uses_zero'     => true, 'unix_epoch' => null, 'is_auxiliary' => true,
        ]);

        $fsId = DB::table('units')->insertGetId([
            'calendar_id'   => $calId, 'singular_name' => 'femtosecond',
            'plural_name'   => 'femtoseconds', 'scale_amount' => 1000,
            'scale_inverse' => true, 'scale_to' => $psId,
            'uses_zero'     => true, 'unix_epoch' => null, 'is_auxiliary' => true,
        ]);

        DB::table('units')->insert([
            [
                'calendar_id'   => $calId, 'singular_name' => 'week',
                'plural_name'   => 'weeks', 'scale_amount' => 7,
                'scale_inverse' => false, 'scale_to' => $dayId,
                'uses_zero'     => true, 'unix_epoch' => null, 'is_auxiliary' => true,
            ],
            [
                'calendar_id'   => $calId, 'singular_name' => 'quarter',
                'plural_name'   => 'quarters', 'scale_amount' => 3,
                'scale_inverse' => false, 'scale_to' => $monId,
                'uses_zero'     => false, 'unix_epoch' => null, 'is_auxiliary' => true,
            ],
            [
                'calendar_id'   => $calId, 'singular_name' => 'decade',
                'plural_name'   => 'decades', 'scale_amount' => 10,
                'scale_inverse' => false, 'scale_to' => $yrId,
                'uses_zero'     => false, 'unix_epoch' => null, 'is_auxiliary' => true,
            ],
            [
                'calendar_id'   => $calId, 'singular_name' => 'century',
                'plural_name'   => 'centuries', 'scale_amount' => 100,
                'scale_inverse' => false, 'scale_to' => $yrId,
                'uses_zero'     => false, 'unix_epoch' => null, 'is_auxiliary' => true,
            ],
            [
                'calendar_id'   => $calId, 'singular_name' => 'millenium',
                'plural_name'   => 'millenia', 'scale_amount' => 1000,
                'scale_inverse' => false, 'scale_to' => $yrId,
                'uses_zero'     => false, 'unix_epoch' => null, 'is_auxiliary' => true,
            ],
            [
                'calendar_id'   => $calId, 'singular_name' => 'attosecond',
                'plural_name'   => 'attoseconds', 'scale_amount' => 1000,
                'scale_inverse' => true, 'scale_to' => $fsId,
                'uses_zero'     => true, 'unix_epoch' => null, 'is_auxiliary' => true,
            ]
        ]);

        return [$monId, $dayId];
    }

    protected static function defineEloquentUnitLengths($monId, $dayId)
    {
        DB::table('unit_lengths')->insert([
            ['unit_id' => $monId, 'unit_value' => 1, 'scale_amount' => 31, 'scale_to' => $dayId],
            ['unit_id' => $monId, 'unit_value' => 2, 'scale_amount' => 28, 'scale_to' => $dayId],
            ['unit_id' => $monId, 'unit_value' => 3, 'scale_amount' => 31, 'scale_to' => $dayId],
            ['unit_id' => $monId, 'unit_value' => 4, 'scale_amount' => 30, 'scale_to' => $dayId],
            ['unit_id' => $monId, 'unit_value' => 5, 'scale_amount' => 31, 'scale_to' => $dayId],
            ['unit_id' => $monId, 'unit_value' => 6, 'scale_amount' => 30, 'scale_to' => $dayId],
            ['unit_id' => $monId, 'unit_value' => 7, 'scale_amount' => 31, 'scale_to' => $dayId],
            ['unit_id' => $monId, 'unit_value' => 8, 'scale_amount' => 31, 'scale_to' => $dayId],
            ['unit_id' => $monId, 'unit_value' => 9, 'scale_amount' => 30, 'scale_to' => $dayId],
            ['unit_id' => $monId, 'unit_value' => 10, 'scale_amount' => 31, 'scale_to' => $dayId],
            ['unit_id' => $monId, 'unit_value' => 11, 'scale_amount' => 30, 'scale_to' => $dayId],
            ['unit_id' => $monId, 'unit_value' => 12, 'scale_amount' => 31, 'scale_to' => $dayId],
        ]);
    }
}
