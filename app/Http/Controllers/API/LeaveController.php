<?php

namespace App\Http\Controllers\API;

use App\Models\Leave;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LeaveController extends Controller
{
    public function index()
    {
        try {
            $leaves = Leave::with('user')->orderBy('id', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $leaves,
                'message' => 'Leaves retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => 'false',
                'message' => 'Error retrieving leaves'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                /**
                 * Start Date
                 * 
                 * @example 2024-10-25
                 */
                'start_date' => 'required|date',
                /**
                 * End Date
                 * 
                 * @example 2024-10-27
                 */
                'end_date' => 'required|date|after_or_equal:start_date',
                /**
                 * Reason
                 * 
                 * @example Keperluan Keluarga
                 */
                'reason' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $leave = Leave::create([
                'user_id' => Auth::id(),
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'reason' => $request->reason,
                'status' => 'pending', // Default status
                'note' => $request->note
            ]);

            $leave->load('user');

            return response()->json([
                'success' => true,
                'data' => $leave,
                'message' => 'Leave request created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating leave request'
            ], 500);
        }
    }
}
