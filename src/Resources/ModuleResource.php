<?php

namespace HoceineEl\FilamentModularSubscriptions\Resources;

use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use HoceineEl\FilamentModularSubscriptions\Models\Module;
use HoceineEl\FilamentModularSubscriptions\Resources\ModuleResource\Pages;

class ModuleResource extends Resource
{
    protected static ?string $model = Module::class;

    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';

    public static function getPluralModelLabel(): string
    {
        return __('filament-modular-subscriptions::modular-subscriptions.resources.module.name');
    }

    public static function getModelLabel(): string
    {
        return __('filament-modular-subscriptions::modular-subscriptions.resources.module.singular_name');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament-modular-subscriptions::modular-subscriptions.menu_group.subscription_management');
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label(__('filament-modular-subscriptions::modular-subscriptions.resources.module.fields.name')),
                Forms\Components\Select::make('class')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->options(function () {
                        return config('filament-modular-subscriptions.modules');
                    })
                    ->label(__('filament-modular-subscriptions::modular-subscriptions.resources.module.fields.class')),
                Forms\Components\Toggle::make('is_active')
                    ->default(true)
                    ->label(__('filament-modular-subscriptions::modular-subscriptions.resources.module.fields.is_active')),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament-modular-subscriptions::modular-subscriptions.resources.module.fields.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('class')
                    ->label(__('filament-modular-subscriptions::modular-subscriptions.resources.module.fields.class')),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label(__('filament-modular-subscriptions::modular-subscriptions.resources.module.fields.is_active')),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')
                    ->options([
                        '1' => __('filament-modular-subscriptions::modular-subscriptions.active'),
                        '0' => __('filament-modular-subscriptions::modular-subscriptions.inactive'),
                    ])
                    ->label(__('filament-modular-subscriptions::modular-subscriptions.resources.module.fields.is_active')),
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
            'index' => Pages\ListModules::route('/'),
            'create' => Pages\CreateModule::route('/create'),
            'edit' => Pages\EditModule::route('/{record}/edit'),
        ];
    }
}
