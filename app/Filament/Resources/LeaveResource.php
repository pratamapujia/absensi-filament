<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaveResource\Pages;
use App\Filament\Resources\LeaveResource\RelationManagers;
use App\Models\Leave;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Auth;

class LeaveResource extends Resource
{
    protected static ?string $model = Leave::class;

    protected static ?string $navigationIcon = 'hugeicons-mail-open';

    protected static ?int $navigationSort = 9;

    protected static ?string $navigationGroup = 'Attendance Management';

    public static function form(Form $form): Form
    {
        $schema = [
            Forms\Components\Section::make('Detail')
                ->schema([
                    Forms\Components\Group::make()
                        ->schema([
                            Forms\Components\DatePicker::make('start_date')
                                ->required(),
                            Forms\Components\DatePicker::make('end_date')
                                ->required(),
                        ])->columns(2),
                    Forms\Components\Textarea::make('reason')
                        ->required()
                        ->columnSpanFull(),
                ]),
        ];
        if (Auth::user()->hasRole('super_admin')) {
            $schema[] = Forms\Components\Section::make('Approval')
                ->schema([
                    Forms\Components\Select::make('status')
                        ->options([
                            'approved' => 'Approved',
                            'rejected' => 'Rejected',
                        ]),
                    Forms\Components\TextInput::make('note')
                        ->columnSpanFull(),
                ]);
        }
        return $form
            ->schema($schema);
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
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pegawai')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'gray',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    })
                    ->description(fn(Leave $record): string => $record->note ? $record->note : ''),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
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
            'index' => Pages\ListLeaves::route('/'),
            'create' => Pages\CreateLeave::route('/create'),
            'edit' => Pages\EditLeave::route('/{record}/edit'),
        ];
    }
}
