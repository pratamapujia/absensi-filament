<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Scedule;
use App\Models\Leave;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Auth;

class AttendanceController extends Controller
{
    public function getAttendanceToday()
    {
        $userId = auth()->user()->id;
        $today = now()->toDateString();
        $currentMonth = now()->month;

        $attendanceToday = Attendance::select('start_time', 'end_time')
            ->where('user_id', $userId)
            ->whereDate('created_at', $today)
            ->first();

        $attendanceThisMonth = Attendance::select('start_time', 'end_time', 'created_at')
            ->where('user_id', $userId)
            ->whereMonth('created_at', $currentMonth)
            ->get()
            ->map(function ($attendance) {
                return [
                    'start_time' => $attendance->start_time,
                    'end_time' => $attendance->end_time,
                    'date' => $attendance->created_at->toDateString()
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Attendance retrieved successfully.',
            'data' => [
                'today' => $attendanceToday,
                'this_month' => $attendanceThisMonth
            ]
        ]);
    }

    public function getSchedule()
    {
        $schedule = Scedule::with(['office', 'shift'])->where('user_id', auth()->user()->id)->first();

        if ($schedule == null) {
            return response()->json([
                'success' => false,
                'message' => 'User belum mendapatkan jadwal kerja, segera hubungi Admin.',
                'data' => null
            ], 404);
        }

        $today = Carbon::today()->format('Y-m-d');
        $approvalLeave = Leave::where('user_id', Auth::user()->id)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->exists();

        if ($approvalLeave) {
            return response()->json([
                'success' => true,
                'message' => 'Anda tidak dapat melakukan presensi karena sedang cuti.',
                'data' => null
            ], 404);
        }

        if ($schedule->is_banned) {
            return response()->json([
                'success' => false,
                'message' => 'User sedang dibanned, segera hubungi Admin.',
                'data' => null
            ], 404);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Schedule retrieved successfully.',
                'data' => $schedule
            ]);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'data' => null
            ], 422);
        }

        $schedule = Scedule::where('user_id', auth()->user()->id)->first();

        if ($schedule == null) {
            return response()->json([
                'success' => false,
                'message' => 'User belum mendapatkan jadwal kerja, segera hubungi Admin.',
                'data' => null
            ], 404);
        }

        $today = Carbon::today()->format('Y-m-d');
        $approvalLeave = Leave::where('user_id', Auth::user()->id)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->exists();

        if ($approvalLeave) {
            return response()->json([
                'success' => true,
                'message' => 'Anda tidak dapat melakukan presensi karena sedang cuti.',
                'data' => null
            ], 404);
        }

        if ($schedule) {
            $attendance = Attendance::where('user_id', Auth::user()->id)->whereDate('created_at', now()->toDateString())->first();

            if (!$attendance) {
                $attendance = Attendance::create([
                    'user_id' => Auth::user()->id,
                    'schedule_latitude' => $schedule->office->latitude,
                    'schedule_longitude' => $schedule->office->longitude,
                    'schedule_start_time' => $schedule->shift->start_time,
                    'schedule_end_time' => $schedule->shift->end_time,
                    'start_latitude' => $request->latitude,
                    'start_longitude' => $request->longitude,
                    'start_time' => Carbon::now()->toTimeString(),
                    'end_time' => Carbon::now()->toTimeString(),
                ]);
            } else {
                $attendance->update([
                    'end_latitude' => $request->latitude,
                    'end_longitude' => $request->longitude,
                    'end_time' => Carbon::now()->toTimeString(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Attendance created successfully.',
                'data' => $attendance
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Schedule not found.',
            'data' => null
        ], 404);
    }

    public function getAttendanceByMonthYear($month, $year)
    {
        $validator = Validator::make(['month' => $month, 'year' => $year], [
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:1900|max:' . date('Y'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'data' => null
            ], 422);
        }

        $userId = auth()->user()->id;
        $attendanceList = Attendance::where('user_id', $userId)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get()
            ->map(function ($attendance) {
                return [
                    'start_time' => $attendance->start_time,
                    'end_time' => $attendance->end_time,
                    'date' => $attendance->created_at->toDateString()
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Attendance retrieved successfully.',
            'data' => $attendanceList
        ]);
    }

    public function banned()
    {
        $schedule = Scedule::where('user_id', auth()->user()->id)->first();

        if ($schedule) {
            $schedule->update([
                'is_banned' => true
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Success banned Schedule.',
            'data' => $schedule
        ]);
    }

    public function getPhoto()
    {
        $user = auth()->user();
        return response()->json([
            'success' => true,
            'message' => 'Success get photo profile',
            'data' => $user->image
        ]);
    }
}
