<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\Schema;

class AttendanceExport implements FromQuery, WithHeadings
{
    public function query()
    {
        return Attendance::query()
            ->join('users', 'attendances.user_id', '=', 'users.id')
            ->select([
                'users.email',
                'users.name as username',
                'users.created_at',
                'attendances.schedule_latitude',
                'attendances.schedule_longitude',
                'attendances.schedule_start_time',
                'attendances.schedule_end_time',
                'attendances.start_latitude',
                'attendances.start_longitude',
                'attendances.start_time',
                'attendances.end_time',
                'attendances.end_latitude',
                'attendances.end_longitude',
            ]);
    }
    public function headings(): array
    {
        return ([
            'Email',
            'Username',
            'Created At',
            'Schedule Latitude',
            'Schedule Longitude',
            'Schedule Start Time',
            'Schedule End Time',
            'Start Latitude',
            'Start Longitude',
            'Start Time',
            'End Time',
            'End Latitude',
            'End Longitude'
        ]);
    }
}
