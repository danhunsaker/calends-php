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
        require_once __DIR__ . '/../extra/non-laravel-migrate.php';

        if (DB::table('calendars')->where('name', 'eloquent')->count() > 0) {
            return;
        }

        $calId = DB::table('calendars')->insertGetId(['name' => 'eloquent', 'description' => 'A test calendar.  Feel free to destroy it.']);
        $uIDs  = static::defineEloquentUnits($calId);
        static::defineEloquentUnitLengths($uIDs);
    }

    protected static function defineEloquentUnits($calId)
    {
        $uIDs = [];

        $uIDs['second'] = DB::table('units')->insertGetId([
            'calendar_id'  => $calId, 'internal_name' => 'second',
            'scale_amount' => 1, 'scale_inverse' => false, 'scale_to' => 0,
            'uses_zero'    => true, 'unix_epoch' => 0, 'is_auxiliary' => false,
        ]);
        DB::table('unit_names')->insert(['unit_id' => $uIDs['second'], 'unit_name'  => 'seconds', 'name_context' => 'plural']);

        $uIDs['minute'] = DB::table('units')->insertGetId([
            'calendar_id'  => $calId, 'internal_name' => 'minute',
            'scale_amount' => 60, 'scale_inverse' => false, 'scale_to' => $uIDs['second'],
            'uses_zero'    => true, 'unix_epoch' => 0, 'is_auxiliary' => false,
        ]);
        DB::table('unit_names')->insert(['unit_id' => $uIDs['minute'], 'unit_name'  => 'minutes', 'name_context' => 'plural']);

        $uIDs['hour'] = DB::table('units')->insertGetId([
            'calendar_id'  => $calId, 'internal_name' => 'hour',
            'scale_amount' => 60, 'scale_inverse' => false, 'scale_to' => $uIDs['minute'],
            'uses_zero'    => true, 'unix_epoch' => 0, 'is_auxiliary' => false,
        ]);
        DB::table('unit_names')->insert(['unit_id' => $uIDs['hour'], 'unit_name'  => 'hours', 'name_context' => 'plural']);

        $uIDs['day'] = DB::table('units')->insertGetId([
            'calendar_id'  => $calId, 'internal_name' => 'day',
            'scale_amount' => 24, 'scale_inverse' => false, 'scale_to' => $uIDs['hour'],
            'uses_zero'    => false, 'unix_epoch' => 1, 'is_auxiliary' => false,
        ]);
        DB::table('unit_names')->insert(['unit_id' => $uIDs['day'], 'unit_name'  => 'days', 'name_context' => 'plural']);

        $uIDs['month'] = DB::table('units')->insertGetId([
            'calendar_id'  => $calId, 'internal_name' => 'month',
            'scale_amount' => null, 'scale_inverse' => false, 'scale_to' => $uIDs['day'],
            'uses_zero'    => false, 'unix_epoch' => 1, 'is_auxiliary' => false,
        ]);
        DB::table('unit_names')->insert(['unit_id' => $uIDs['month'], 'unit_name'  => 'months', 'name_context' => 'plural']);

        $uIDs['year'] = DB::table('units')->insertGetId([
            'calendar_id'  => $calId, 'internal_name' => 'year',
            'scale_amount' => 12, 'scale_inverse' => false, 'scale_to' => $uIDs['month'],
            'uses_zero'    => true, 'unix_epoch' => 1970, 'is_auxiliary' => false,
        ]);
        DB::table('unit_names')->insert(['unit_id' => $uIDs['year'], 'unit_name'  => 'years', 'name_context' => 'plural']);

        $uIDs['millisecond'] = DB::table('units')->insertGetId([
            'calendar_id'  => $calId, 'internal_name' => 'millisecond',
            'scale_amount' => 1000, 'scale_inverse' => true, 'scale_to' => $uIDs['second'],
            'uses_zero'    => true, 'unix_epoch' => null, 'is_auxiliary' => true,
        ]);
        DB::table('unit_names')->insert(['unit_id' => $uIDs['millisecond'], 'unit_name'  => 'milliseconds', 'name_context' => 'plural']);

        $uIDs['microsecond'] = DB::table('units')->insertGetId([
            'calendar_id'  => $calId, 'internal_name' => 'microsecond',
            'scale_amount' => 1000, 'scale_inverse' => true, 'scale_to' => $uIDs['millisecond'],
            'uses_zero'    => true, 'unix_epoch' => null, 'is_auxiliary' => true,
        ]);
        DB::table('unit_names')->insert(['unit_id' => $uIDs['microsecond'], 'unit_name'  => 'microseconds', 'name_context' => 'plural']);

        $uIDs['nanosecond'] = DB::table('units')->insertGetId([
            'calendar_id'  => $calId, 'internal_name' => 'nanosecond',
            'scale_amount' => 1000, 'scale_inverse' => true, 'scale_to' => $uIDs['microsecond'],
            'uses_zero'    => true, 'unix_epoch' => null, 'is_auxiliary' => true,
        ]);
        DB::table('unit_names')->insert(['unit_id' => $uIDs['nanosecond'], 'unit_name'  => 'nanoseconds', 'name_context' => 'plural']);

        $uIDs['picosecond'] = DB::table('units')->insertGetId([
            'calendar_id'  => $calId, 'internal_name' => 'picosecond',
            'scale_amount' => 1000, 'scale_inverse' => true, 'scale_to' => $uIDs['nanosecond'],
            'uses_zero'    => true, 'unix_epoch' => null, 'is_auxiliary' => true,
        ]);
        DB::table('unit_names')->insert(['unit_id' => $uIDs['picosecond'], 'unit_name'  => 'picoseconds', 'name_context' => 'plural']);

        $uIDs['femtosecond'] = DB::table('units')->insertGetId([
            'calendar_id'  => $calId, 'internal_name' => 'femtosecond',
            'scale_amount' => 1000, 'scale_inverse' => true, 'scale_to' => $uIDs['picosecond'],
            'uses_zero'    => true, 'unix_epoch' => null, 'is_auxiliary' => true,
        ]);
        DB::table('unit_names')->insert(['unit_id' => $uIDs['femtosecond'], 'unit_name'  => 'femtoseconds', 'name_context' => 'plural']);

        $uIDs['week'] = DB::table('units')->insertGetId([
            'calendar_id'  => $calId, 'internal_name' => 'week',
            'scale_amount' => 7, 'scale_inverse' => false, 'scale_to' => $uIDs['day'],
            'uses_zero'    => true, 'unix_epoch' => null, 'is_auxiliary' => true,
        ]);
        DB::table('unit_names')->insert(['unit_id' => $uIDs['week'], 'unit_name'  => 'weeks', 'name_context' => 'plural']);

        $uIDs['quarter'] = DB::table('units')->insertGetId([
            'calendar_id'  => $calId, 'internal_name' => 'quarter',
            'scale_amount' => 3, 'scale_inverse' => false, 'scale_to' => $uIDs['month'],
            'uses_zero'    => false, 'unix_epoch' => null, 'is_auxiliary' => true,
        ]);
        DB::table('unit_names')->insert(['unit_id' => $uIDs['quarter'], 'unit_name'  => 'quarters', 'name_context' => 'plural']);

        $uIDs['decade'] = DB::table('units')->insertGetId([
            'calendar_id'  => $calId, 'internal_name' => 'decade',
            'scale_amount' => 10, 'scale_inverse' => false, 'scale_to' => $uIDs['year'],
            'uses_zero'    => false, 'unix_epoch' => null, 'is_auxiliary' => true,
        ]);
        DB::table('unit_names')->insert(['unit_id' => $uIDs['decade'], 'unit_name'  => 'decades', 'name_context' => 'plural']);

        $uIDs['century'] = DB::table('units')->insertGetId([
            'calendar_id'  => $calId, 'internal_name' => 'century',
            'scale_amount' => 100, 'scale_inverse' => false, 'scale_to' => $uIDs['year'],
            'uses_zero'    => false, 'unix_epoch' => null, 'is_auxiliary' => true,
        ]);
        DB::table('unit_names')->insert(['unit_id' => $uIDs['century'], 'unit_name'  => 'centuries', 'name_context' => 'plural']);

        $uIDs['millenium'] = DB::table('units')->insertGetId([
            'calendar_id'  => $calId, 'internal_name' => 'millenium',
            'scale_amount' => 1000, 'scale_inverse' => false, 'scale_to' => $uIDs['year'],
            'uses_zero'    => false, 'unix_epoch' => null, 'is_auxiliary' => true,
        ]);
        DB::table('unit_names')->insert(['unit_id' => $uIDs['millenium'], 'unit_name'  => 'millenia', 'name_context' => 'plural']);

        $uIDs['attosecond'] = DB::table('units')->insertGetId([
            'calendar_id'  => $calId, 'internal_name' => 'attosecond',
            'scale_amount' => 1000, 'scale_inverse' => true, 'scale_to' => $uIDs['femtosecond'],
            'uses_zero'    => true, 'unix_epoch' => null, 'is_auxiliary' => true,
        ]);
        DB::table('unit_names')->insert(['unit_id' => $uIDs['attosecond'], 'unit_name'  => 'attoseconds', 'name_context' => 'plural']);

        return $uIDs;
    }

    protected static function defineEloquentUnitLengths($uIDs)
    {
        DB::table('unit_lengths')->insert([
            ['unit_id' => $uIDs['month'], 'unit_value' => 1, 'scale_amount' => 31],
            ['unit_id' => $uIDs['month'], 'unit_value' => 2, 'scale_amount' => 28],
            ['unit_id' => $uIDs['month'], 'unit_value' => 3, 'scale_amount' => 31],
            ['unit_id' => $uIDs['month'], 'unit_value' => 4, 'scale_amount' => 30],
            ['unit_id' => $uIDs['month'], 'unit_value' => 5, 'scale_amount' => 31],
            ['unit_id' => $uIDs['month'], 'unit_value' => 6, 'scale_amount' => 30],
            ['unit_id' => $uIDs['month'], 'unit_value' => 7, 'scale_amount' => 31],
            ['unit_id' => $uIDs['month'], 'unit_value' => 8, 'scale_amount' => 31],
            ['unit_id' => $uIDs['month'], 'unit_value' => 9, 'scale_amount' => 30],
            ['unit_id' => $uIDs['month'], 'unit_value' => 10, 'scale_amount' => 31],
            ['unit_id' => $uIDs['month'], 'unit_value' => 11, 'scale_amount' => 30],
            ['unit_id' => $uIDs['month'], 'unit_value' => 12, 'scale_amount' => 31],
        ]);
    }
}
