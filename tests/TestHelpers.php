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
        $eIDs = static::defineEloquentEras($uIDs);
        static::defineEloquentFragmentFormats($calId, $uIDs, $eIDs);
        static::defineEloquentCalendarFormats($calId);
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

    protected static function defineEloquentEras($uIDs)
    {
        $eIDs = [
            'years' => DB::table('eras')->insertGetId(['unit_id' => $uIDs['year'], 'internal_name' => 'gregorian-years']),
            'hours' => DB::table('eras')->insertGetId(['unit_id' => $uIDs['hour'], 'internal_name' => '12-hour-time']),
        ];

        DB::table('era_ranges')->insert(['era_id' => $eIDs['years'], 'range_code' => 'bc', 'start_value' => 0,  'end_value' => null, 'start_display' => 1,  'direction' => 'desc']);
        DB::table('era_ranges')->insert(['era_id' => $eIDs['years'], 'range_code' => 'ad', 'start_value' => 1,  'end_value' => null, 'start_display' => 1,  'direction' => 'asc']);

        DB::table('era_ranges')->insert(['era_id' => $eIDs['hours'], 'range_code' => 'am', 'start_value' => 0,  'end_value' => 0,    'start_display' => 12, 'direction' => 'asc']);
        DB::table('era_ranges')->insert(['era_id' => $eIDs['hours'], 'range_code' => 'am', 'start_value' => 1,  'end_value' => 11,   'start_display' => 1,  'direction' => 'asc']);
        DB::table('era_ranges')->insert(['era_id' => $eIDs['hours'], 'range_code' => 'pm', 'start_value' => 12, 'end_value' => 12,   'start_display' => 12, 'direction' => 'asc']);
        DB::table('era_ranges')->insert(['era_id' => $eIDs['hours'], 'range_code' => 'pm', 'start_value' => 13, 'end_value' => 23,   'start_display' => 1,  'direction' => 'asc']);
        DB::table('era_ranges')->insert(['era_id' => $eIDs['hours'], 'range_code' => 'am', 'start_value' => 24, 'end_value' => 24,   'start_display' => 12, 'direction' => 'asc']);

        return $eIDs;
    }

    protected static function defineEloquentFragmentFormats($calId, $uIDs, $eIDs)
    {
        $fIDs = [
            'd' => DB::table('fragment_formats')->insertGetId([
                'calendar_id'   => $calId,
                'format_code'   => 'd',
                'fragment_type' => 'Danhunsaker\Calends\Eloquent\Unit',
                'fragment_id'   => $uIDs['day'],
                'format_string' => '%{value}$02d',
                'description'   => 'Day of the month, 2 digits with leading zeros',
            ]),
            'j' => DB::table('fragment_formats')->insertGetId([
                'calendar_id'   => $calId,
                'format_code'   => 'j',
                'fragment_type' => 'Danhunsaker\Calends\Eloquent\Unit',
                'fragment_id'   => $uIDs['day'],
                'format_string' => '%{value}$d',
                'description'   => 'Day of the month without leading zeros',
            ]),
            'W' => DB::table('fragment_formats')->insertGetId([
                'calendar_id'   => $calId,
                'format_code'   => 'W',
                'fragment_type' => 'Danhunsaker\Calends\Eloquent\Unit',
                'fragment_id'   => $uIDs['week'],
                'format_string' => '%{value}$d',
                'description'   => 'Week number of year',
            ]),
            'F' => DB::table('fragment_formats')->insertGetId([
                'calendar_id'   => $calId,
                'format_code'   => 'F',
                'fragment_type' => 'Danhunsaker\Calends\Eloquent\Unit',
                'fragment_id'   => $uIDs['month'],
                'format_string' => '%{value}$s',
                'description'   => 'A full textual representation of a month, such as January or March',
            ]),
            'm' => DB::table('fragment_formats')->insertGetId([
                'calendar_id'   => $calId,
                'format_code'   => 'm',
                'fragment_type' => 'Danhunsaker\Calends\Eloquent\Unit',
                'fragment_id'   => $uIDs['month'],
                'format_string' => '%{value}$02d',
                'description'   => 'Numeric representation of a month, with leading zeros',
            ]),
            'M' => DB::table('fragment_formats')->insertGetId([
                'calendar_id'   => $calId,
                'format_code'   => 'M',
                'fragment_type' => 'Danhunsaker\Calends\Eloquent\Unit',
                'fragment_id'   => $uIDs['month'],
                'format_string' => '%{value}$s',
                'description'   => 'A short textual representation of a month, three letters',
            ]),
            'n' => DB::table('fragment_formats')->insertGetId([
                'calendar_id'   => $calId,
                'format_code'   => 'n',
                'fragment_type' => 'Danhunsaker\Calends\Eloquent\Unit',
                'fragment_id'   => $uIDs['month'],
                'format_string' => '%{value}$d',
                'description'   => 'Numeric representation of a month, without leading zeros',
            ]),
            'Y' => DB::table('fragment_formats')->insertGetId([
                'calendar_id'   => $calId,
                'format_code'   => 'Y',
                'fragment_type' => 'Danhunsaker\Calends\Eloquent\Era',
                'fragment_id'   => $eIDs['years'],
                'format_string' => '%{value}$04d',
                'description'   => 'A full numeric representation of a year, 4 digits',
            ]),
            'y' => DB::table('fragment_formats')->insertGetId([
                'calendar_id'   => $calId,
                'format_code'   => 'y',
                'fragment_type' => 'Danhunsaker\Calends\Eloquent\Era',
                'fragment_id'   => $eIDs['years'],
                'format_string' => '%{value}%100$02d',
                'description'   => 'A two digit representation of a year',
            ]),
            'E' => DB::table('fragment_formats')->insertGetId([
                'calendar_id'   => $calId,
                'format_code'   => 'E',
                'fragment_type' => 'Danhunsaker\Calends\Eloquent\Era',
                'fragment_id'   => $eIDs['years'],
                'format_string' => '%{code}$s',
                'description'   => 'The calendar epoch (BC/AD)',
            ]),
            'a' => DB::table('fragment_formats')->insertGetId([
                'calendar_id'   => $calId,
                'format_code'   => 'a',
                'fragment_type' => 'Danhunsaker\Calends\Eloquent\Era',
                'fragment_id'   => $eIDs['hours'],
                'format_string' => '%{code}$s',
                'description'   => 'Lowercase Ante meridiem and Post meridiem',
            ]),
            'A' => DB::table('fragment_formats')->insertGetId([
                'calendar_id'   => $calId,
                'format_code'   => 'A',
                'fragment_type' => 'Danhunsaker\Calends\Eloquent\Era',
                'fragment_id'   => $eIDs['hours'],
                'format_string' => '%{code}$s',
                'description'   => 'Uppercase Ante meridiem and Post meridiem',
            ]),
            'g' => DB::table('fragment_formats')->insertGetId([
                'calendar_id'   => $calId,
                'format_code'   => 'g',
                'fragment_type' => 'Danhunsaker\Calends\Eloquent\Era',
                'fragment_id'   => $eIDs['hours'],
                'format_string' => '%{value}$d',
                'description'   => '12-hour format of an hour without leading zeros',
            ]),
            'G' => DB::table('fragment_formats')->insertGetId([
                'calendar_id'   => $calId,
                'format_code'   => 'G',
                'fragment_type' => 'Danhunsaker\Calends\Eloquent\Unit',
                'fragment_id'   => $uIDs['hour'],
                'format_string' => '%{value}$d',
                'description'   => '24-hour format of an hour without leading zeros',
            ]),
            'h' => DB::table('fragment_formats')->insertGetId([
                'calendar_id'   => $calId,
                'format_code'   => 'h',
                'fragment_type' => 'Danhunsaker\Calends\Eloquent\Era',
                'fragment_id'   => $eIDs['hours'],
                'format_string' => '%{value}$02d',
                'description'   => '12-hour format of an hour with leading zeros',
            ]),
            'H' => DB::table('fragment_formats')->insertGetId([
                'calendar_id'   => $calId,
                'format_code'   => 'H',
                'fragment_type' => 'Danhunsaker\Calends\Eloquent\Unit',
                'fragment_id'   => $uIDs['hour'],
                'format_string' => '%{value}$02d',
                'description'   => '24-hour format of an hour with leading zeros',
            ]),
            'i' => DB::table('fragment_formats')->insertGetId([
                'calendar_id'   => $calId,
                'format_code'   => 'i',
                'fragment_type' => 'Danhunsaker\Calends\Eloquent\Unit',
                'fragment_id'   => $uIDs['minute'],
                'format_string' => '%{value}$02d',
                'description'   => 'Minutes with leading zeros',
            ]),
            's' => DB::table('fragment_formats')->insertGetId([
                'calendar_id'   => $calId,
                'format_code'   => 's',
                'fragment_type' => 'Danhunsaker\Calends\Eloquent\Unit',
                'fragment_id'   => $uIDs['second'],
                'format_string' => '%{value}$02d',
                'description'   => 'Seconds, with leading zeros',
            ]),
            'u' => DB::table('fragment_formats')->insertGetId([
                'calendar_id'   => $calId,
                'format_code'   => 'u',
                'fragment_type' => 'Danhunsaker\Calends\Eloquent\Unit',
                'fragment_id'   => $uIDs['microsecond'],
                'format_string' => '%{value}$06d',
                'description'   => 'Microseconds',
            ]),
        ];

        DB::table('fragment_texts')->insert(['fragment_format_id' => $fIDs['F'], 'fragment_value' => 1, 'fragment_text' => 'January']);
        DB::table('fragment_texts')->insert(['fragment_format_id' => $fIDs['F'], 'fragment_value' => 2, 'fragment_text' => 'February']);
        DB::table('fragment_texts')->insert(['fragment_format_id' => $fIDs['F'], 'fragment_value' => 3, 'fragment_text' => 'March']);
        DB::table('fragment_texts')->insert(['fragment_format_id' => $fIDs['F'], 'fragment_value' => 4, 'fragment_text' => 'April']);
        DB::table('fragment_texts')->insert(['fragment_format_id' => $fIDs['F'], 'fragment_value' => 5, 'fragment_text' => 'May']);
        DB::table('fragment_texts')->insert(['fragment_format_id' => $fIDs['F'], 'fragment_value' => 6, 'fragment_text' => 'June']);
        DB::table('fragment_texts')->insert(['fragment_format_id' => $fIDs['F'], 'fragment_value' => 7, 'fragment_text' => 'July']);
        DB::table('fragment_texts')->insert(['fragment_format_id' => $fIDs['F'], 'fragment_value' => 8, 'fragment_text' => 'August']);
        DB::table('fragment_texts')->insert(['fragment_format_id' => $fIDs['F'], 'fragment_value' => 9, 'fragment_text' => 'September']);
        DB::table('fragment_texts')->insert(['fragment_format_id' => $fIDs['F'], 'fragment_value' => 10, 'fragment_text' => 'October']);
        DB::table('fragment_texts')->insert(['fragment_format_id' => $fIDs['F'], 'fragment_value' => 11, 'fragment_text' => 'November']);
        DB::table('fragment_texts')->insert(['fragment_format_id' => $fIDs['F'], 'fragment_value' => 12, 'fragment_text' => 'December']);

        DB::table('fragment_texts')->insert(['fragment_format_id' => $fIDs['M'], 'fragment_value' => 1, 'fragment_text' => 'Jan']);
        DB::table('fragment_texts')->insert(['fragment_format_id' => $fIDs['M'], 'fragment_value' => 2, 'fragment_text' => 'Feb']);
        DB::table('fragment_texts')->insert(['fragment_format_id' => $fIDs['M'], 'fragment_value' => 3, 'fragment_text' => 'Mar']);
        DB::table('fragment_texts')->insert(['fragment_format_id' => $fIDs['M'], 'fragment_value' => 4, 'fragment_text' => 'Apr']);
        DB::table('fragment_texts')->insert(['fragment_format_id' => $fIDs['M'], 'fragment_value' => 5, 'fragment_text' => 'May']);
        DB::table('fragment_texts')->insert(['fragment_format_id' => $fIDs['M'], 'fragment_value' => 6, 'fragment_text' => 'Jun']);
        DB::table('fragment_texts')->insert(['fragment_format_id' => $fIDs['M'], 'fragment_value' => 7, 'fragment_text' => 'Jul']);
        DB::table('fragment_texts')->insert(['fragment_format_id' => $fIDs['M'], 'fragment_value' => 8, 'fragment_text' => 'Aug']);
        DB::table('fragment_texts')->insert(['fragment_format_id' => $fIDs['M'], 'fragment_value' => 9, 'fragment_text' => 'Sep']);
        DB::table('fragment_texts')->insert(['fragment_format_id' => $fIDs['M'], 'fragment_value' => 10, 'fragment_text' => 'Oct']);
        DB::table('fragment_texts')->insert(['fragment_format_id' => $fIDs['M'], 'fragment_value' => 11, 'fragment_text' => 'Nov']);
        DB::table('fragment_texts')->insert(['fragment_format_id' => $fIDs['M'], 'fragment_value' => 12, 'fragment_text' => 'Dec']);

        DB::table('fragment_texts')->insert(['fragment_format_id' => $fIDs['E'], 'fragment_value' => 'bc', 'fragment_text' => 'BC']);
        DB::table('fragment_texts')->insert(['fragment_format_id' => $fIDs['E'], 'fragment_value' => 'ad', 'fragment_text' => 'AD']);

        DB::table('fragment_texts')->insert(['fragment_format_id' => $fIDs['a'], 'fragment_value' => 'am', 'fragment_text' => 'am']);
        DB::table('fragment_texts')->insert(['fragment_format_id' => $fIDs['a'], 'fragment_value' => 'pm', 'fragment_text' => 'pm']);

        DB::table('fragment_texts')->insert(['fragment_format_id' => $fIDs['A'], 'fragment_value' => 'am', 'fragment_text' => 'AM']);
        DB::table('fragment_texts')->insert(['fragment_format_id' => $fIDs['A'], 'fragment_value' => 'pm', 'fragment_text' => 'PM']);
    }

    protected static function defineEloquentCalendarFormats($calId)
    {
        $formatId = DB::table('calendar_formats')->insertGetId(['calendar_id' => $calId, 'format_name' => 'eloquent', 'format_string' => 'd M Y H:i:s.u', 'description' => 'A basic date format']);
        DB::table('calendar_formats')->insert(['calendar_id' => $calId, 'format_name' => 'mod8601', 'format_string' => 'Y-m-d H:i:s.u', 'description' => 'A modified ISO 8601 date']);
        DB::table('calendar_formats')->insert(['calendar_id' => $calId, 'format_name' => 'filestr', 'format_string' => 'Y-m-d_H-i-s.u', 'description' => 'A date suitable for use in filenames']);

        DB::table('calendars')->where('id', $calId)->update(['default_format' => $formatId]);
    }
}
