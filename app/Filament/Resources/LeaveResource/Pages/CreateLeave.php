<?php

namespace App\Filament\Resources\LeaveResource\Pages;

use App\Filament\Resources\LeaveResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Auth;

class CreateLeave extends CreateRecord
{
    protected static string $resource = LeaveResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        $data['status'] = 'pending';

        return $data;
    }
}
