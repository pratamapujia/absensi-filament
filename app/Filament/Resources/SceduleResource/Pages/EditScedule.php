<?php

namespace App\Filament\Resources\SceduleResource\Pages;

use App\Filament\Resources\SceduleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditScedule extends EditRecord
{
    protected static string $resource = SceduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
