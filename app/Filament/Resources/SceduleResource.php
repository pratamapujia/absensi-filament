<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SceduleResource\Pages;
use App\Filament\Resources\SceduleResource\RelationManagers;
use App\Models\Scedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Auth;

class SceduleResource extends Resource
{
  protected static ?string $model = Scedule::class;

  protected static ?string $navigationIcon = 'heroicon-m-calendar-days';

  protected static ?int $navigationSort = 7;

  protected static ?string $navigationGroup = 'Attendance Management';

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Group::make()
          ->schema([
            Forms\Components\Section::make()
              ->schema([
                Forms\Components\Select::make('user_id')
                  ->relationship('user', 'name')
                  ->searchable()
                  ->required(),
                Forms\Components\Select::make('shift_id')
                  ->relationship('shift', 'name')
                  ->required(),
                Forms\Components\Select::make('office_id')
                  ->relationship('office', 'name')
                  ->required(),
                Forms\Components\Group::make()
                  ->schema([
                    Forms\Components\Toggle::make('is_wfa'),
                    Forms\Components\Toggle::make('is_banned'),
                  ])->columns(2),
              ]),
          ]),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->modifyQueryUsing(function (Builder $query) {
        $is_superAdmin = Auth()->user()->hasRole('super_admin');

        if (!$is_superAdmin) {
          $query->where('user_id', Auth::user()->id);
        }
      })
      ->columns([
        Tables\Columns\TextColumn::make('user.name')
          ->label('User')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('user.email')
          ->label('Email')
          ->searchable()
          ->sortable(),
        Tables\Columns\ToggleColumn::make('is_banned')
          ->label('Banned')
          ->hidden(fn() => !Auth::user()->hasRole('super_admin')),
        Tables\Columns\BooleanColumn::make('is_wfa')
          ->label('WFA'),
        Tables\Columns\TextColumn::make('shift.name')
          ->description(fn(Scedule $record): string => $record->shift->start_time . ' - ' . $record->shift->end_time)
          ->sortable(),
        Tables\Columns\TextColumn::make('office.name')
          ->sortable(),
        Tables\Columns\TextColumn::make('created_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('updated_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->filters([
        //
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
        ]),
      ]);
  }

  public static function getRelations(): array
  {
    return [
      //
    ];
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListScedules::route('/'),
      'create' => Pages\CreateScedule::route('/create'),
      'edit' => Pages\EditScedule::route('/{record}/edit'),
    ];
  }
}
