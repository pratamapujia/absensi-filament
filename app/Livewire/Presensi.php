<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Scedule;
use Livewire\Component;
use Auth;
use Illuminate\Support\Carbon;

class Presensi extends Component
{
  public $latitude;
  public $longitude;
  public $insideRadius = false;
  public function render()
  {
    $scedule = Scedule::where('user_id', Auth::user()->id)->first();
    $attendance = Attendance::where('user_id', Auth::user()->id)->whereDate('created_at', date('Y-m-d'))->first();
    return view('livewire.presensi', [
      'scedule' => $scedule,
      'insideRadius' => $this->insideRadius,
      'attendance' => $attendance,
    ]);
  }

  public function store()
  {
    $this->validate([
      'latitude' => 'required',
      'longitude' => 'required',
    ]);

    $scedule = Scedule::where('user_id', Auth::user()->id)->first();

    $today = Carbon::today()->format('Y-m-d');
    $approvalLeave = Leave::where('user_id', Auth::user()->id)
      ->where('status', 'approved')
      ->whereDate('start_date', '<=', $today)
      ->whereDate('end_date', '>=', $today)
      ->exists();

    if ($approvalLeave) {
      session()->flash('error', 'Anda tidak dapat melakukan presensi karena anda sedang cuti');
      return;
    }

    if ($scedule) {
      $attendance = Attendance::where('user_id', Auth::user()->id)->whereDate('created_at', date('Y-m-d'))->first();
      if (!$attendance) {
        $attendance = Attendance::create([
          'user_id' => Auth::user()->id,
          'schedule_latitude' => $scedule->office->latitude,
          'schedule_longitude' => $scedule->office->longitude,
          'schedule_start_time' => $scedule->shift->start_time,
          'schedule_end_time' => $scedule->shift->end_time,
          'start_latitude' => $this->latitude,
          'start_longitude' => $this->longitude,
          'start_time' => Carbon::now()->toTimeString(),
          'end_time' => Carbon::now()->toTimeString(),
        ]);
      } else {
        $attendance->update([
          'end_latitude' => $this->latitude,
          'end_longitude' => $this->longitude,
          'end_time' => Carbon::now()->toTimeString(),
        ]);
      }

      return redirect('admin/attendances');

      // return redirect()->route('presensi', [
      //   'scedule' => $scedule,
      //   'insideRadius' => false,
      // ]);
    }
  }
}
