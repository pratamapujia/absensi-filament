<?php

namespace App\Livewire;

use App\Models\Attendance;
use Livewire\Component;

class Maps extends Component
{

    public function render()
    {
        $attendances = Attendance::with('user')->get();
        return view('livewire.maps', [
            'attendances' => $attendances,
        ]);
    }
}
