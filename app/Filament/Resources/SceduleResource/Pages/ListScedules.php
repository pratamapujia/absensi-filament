<?php

namespace App\Filament\Resources\SceduleResource\Pages;

use App\Filament\Resources\SceduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListScedules extends ListRecords
{
  protected static string $resource = SceduleResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\CreateAction::make(),
    ];
  }
}
