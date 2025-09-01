<?php

namespace App\Support;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

class Attendance
{
    public static function isWorkday(Carbon $date): bool
    {
        $workdays = config('attendance.workdays', [1,2,3,4,5]);
        return in_array($date->dayOfWeekIso, $workdays, true);
    }

    public static function deadlinePassedFor(Carbon $date): bool
    {
        $cutoff = config('attendance.cutoff_time', '17:00:00');
        if ($date->isPast()) return true;
        if ($date->isFuture()) return false;

        // same day
        return now()->format('H:i:s') >= $cutoff;
    }

    /** @return string[] daftar tanggal kerja (Y-m-d) di rentang [start..end] */
    public static function workdaysBetween(Carbon $start, Carbon $end): array
    {
        $dates = [];
        foreach (CarbonPeriod::create($start, $end) as $d) {
            if (self::isWorkday($d)) $dates[] = $d->toDateString();
        }
        return $dates;
    }

    /** Threshold keterlambatan = start_time + tolerance */
    public static function lateThreshold(Carbon $forDate): Carbon
    {
        $start = Carbon::parse($forDate->toDateString().' '.config('attendance.start_time','08:00:00'));
        [$h,$m,$s] = explode(':', config('attendance.tolerance','00:15:00'));
        return $start->copy()->addHours($h)->addMinutes($m)->addSeconds($s);
    }

    public static function officeEndAt(Carbon $forDate): Carbon
    {
        return Carbon::parse($forDate->toDateString().' '.config('attendance.end_time','17:00:00'));
    }
}
