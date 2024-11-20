<?php

use App\Exports\AttendanceExport;
use Illuminate\Support\Facades\Route;
use App\Livewire\Presensi;

Route::group(['middleware' => 'auth'], function () {
  Route::get('presensi', Presensi::class)->name('presensi');
  Route::get('attendance/export', function () {
    return Excel::download(new AttendanceExport, 'attendances.xlsx');
  })->name('attendance-export');
});

Route::get('/login', function () {
  return redirect('admin/login');
})->name('login');

Route::get('/', function () {
  return view('welcome');
});
