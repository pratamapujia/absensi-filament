<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Filament\Resources\AttendanceResource\RelationManagers;
use App\Models\Attendance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Auth;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'hugeicons-map-pin';

    protected static ?int $navigationSort = 8;

    protected static ?string $navigationGroup = 'Attendance Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user')
                    ->relationship('user', 'name')
                    ->disabled()
                    ->required(),
                Forms\Components\TextInput::make('schedule_latitude')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('schedule_longitude')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('schedule_start_time')
                    ->required(),
                Forms\Components\TextInput::make('schedule_end_time')
                    ->required(),
                Forms\Components\TextInput::make('start_latitude')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('start_longitude')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('start_time')
                    ->required(),
                Forms\Components\TextInput::make('end_time')
                    ->required(),
                Forms\Components\TextInput::make('end_latitude')
                    ->numeric(),
                Forms\Components\TextInput::make('end_longitude')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $is_super_admin = Auth::user()->hasRole('super_admin');

                if (!$is_super_admin) {
                    $query->where('user_id', Auth::user()->id);
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pegawai')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('is_late')
                    ->label('Status')
                    ->badge()
                    ->color(fn(Attendance $record): string => $record->isLate() ? 'danger' : 'success')
                    ->getStateUsing(function (Attendance $record) {
                        return $record->isLate() ? 'Terlambat' : 'Tepat Waktu';
                    })
                    ->description(fn(Attendance $record): string => 'Durasi Kerja : ' .  $record->workDuration()),
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Waktu Datang'),
                Tables\Columns\TextColumn::make('end_time')
                    ->label('Waktu Pulang'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
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
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}
