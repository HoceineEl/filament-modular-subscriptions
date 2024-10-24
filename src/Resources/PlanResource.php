<?php

namespace HoceineEl\FilamentModularSubscriptions\Resources;

use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use HoceineEl\FilamentModularSubscriptions\Enums\Interval;
use HoceineEl\FilamentModularSubscriptions\Models\Module;
use HoceineEl\FilamentModularSubscriptions\Models\Plan;
use HoceineEl\FilamentModularSubscriptions\Resources\PlanResource\Pages\CreatePlan;
use HoceineEl\FilamentModularSubscriptions\Resources\PlanResource\Pages\EditPlan;
use HoceineEl\FilamentModularSubscriptions\Resources\PlanResource\Pages\ListPlans;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class PlanResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-s-squares-plus';

    public static function getModel(): string
    {
        return config('filament-modular-subscriptions.models.plan');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-modular-subscriptions::modular-subscriptions.resources.plan.name');
    }

    public static function getModelLabel(): string
    {
        return __('filament-modular-subscriptions::modular-subscriptions.resources.plan.singular_name');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament-modular-subscriptions::modular-subscriptions.menu_group.subscription_management');
    }
    public static function canDelete(Model $record): bool
    {
        return $record->subscriptions()->count() === 0;
    }
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Plan Details')
                    ->columnSpanFull()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make(__('filament-modular-subscriptions::modular-subscriptions.resources.plan.tabs.details'))
                            ->icon('heroicon-o-information-circle')
                            ->columns(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->translatable(true, config('filament-modular-subscriptions.locales'))
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(Set $set, $state) => $set('slug', str($state['name'][config('filament-modular-subscriptions.locales')[0] ?? app()->getLocale()])->slug()))
                                    ->columnSpanFull()
                                    ->label(__('filament-modular-subscriptions::modular-subscriptions.resources.plan.fields.name')),
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->label(__('filament-modular-subscriptions::modular-subscriptions.resources.plan.fields.slug')),
                                Forms\Components\Textarea::make('description')
                                    ->label(__('filament-modular-subscriptions::modular-subscriptions.resources.plan.fields.description'))
                                    ->translatable(true, config('filament-modular-subscriptions.locales'))
                                    ->columnSpanFull(),
                                Forms\Components\Toggle::make('is_active')
                                    ->default(true)
                                    ->label(__('filament-modular-subscriptions::modular-subscriptions.resources.plan.fields.is_active')),
                            ]),
                        Forms\Components\Tabs\Tab::make(__('filament-modular-subscriptions::modular-subscriptions.resources.plan.tabs.pricing'))
                            ->columns()
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Forms\Components\TextInput::make('price')
                                    ->numeric()
                                    ->required()
                                    ->label(__('filament-modular-subscriptions::modular-subscriptions.resources.plan.fields.price')),
                                Forms\Components\Select::make('currency')
                                    ->options(config('filament-modular-subscriptions.currencies'))
                                    ->default(config('filament-modular-subscriptions.main_currency'))
                                    ->required()
                                    ->label(__('filament-modular-subscriptions::modular-subscriptions.resources.plan.fields.currency')),
                            ]),
                        Forms\Components\Tabs\Tab::make(__('filament-modular-subscriptions::modular-subscriptions.resources.plan.tabs.billing'))
                            ->columns()
                            ->schema([
                                Forms\Components\TextInput::make('trial_period')
                                    ->numeric()
                                    ->default(0)
                                    ->label(__('filament-modular-subscriptions::modular-subscriptions.resources.plan.fields.trial_period')),
                                Forms\Components\Select::make('trial_interval')
                                    ->options(Interval::class)
                                    ->default(Interval::DAY)
                                    ->label(__('filament-modular-subscriptions::modular-subscriptions.resources.plan.fields.trial_interval')),
                                Forms\Components\TextInput::make('invoice_period')
                                    ->numeric()
                                    ->required()
                                    ->label(__('filament-modular-subscriptions::modular-subscriptions.resources.plan.fields.invoice_period')),
                                Forms\Components\Select::make('invoice_interval')
                                    ->options(Interval::class)
                                    ->default(Interval::MONTH)
                                    ->required()
                                    ->label(__('filament-modular-subscriptions::modular-subscriptions.resources.plan.fields.invoice_interval')),
                                Forms\Components\TextInput::make('grace_period')
                                    ->numeric()
                                    ->default(0)
                                    ->label(__('filament-modular-subscriptions::modular-subscriptions.resources.plan.fields.grace_period')),
                                Forms\Components\Select::make('grace_interval')
                                    ->options(Interval::class)
                                    ->default(Interval::DAY)
                                    ->label(__('filament-modular-subscriptions::modular-subscriptions.resources.plan.fields.grace_interval')),
                            ]),
                        Forms\Components\Tabs\Tab::make(__('filament-modular-subscriptions::modular-subscriptions.resources.plan.fields.modules'))
                            ->icon('heroicon-o-puzzle-piece')
                            ->schema([
                                Repeater::make('planModules')
                                    ->label('')
                                    ->relationship()
                                    ->columns(3)
                                    ->schema([
                                        Select::make('module_id')
                                            ->label(__('filament-modular-subscriptions::modular-subscriptions.resources.plan.fields.module'))
                                            ->options(function () {
                                                $modules = config('filament-modular-subscriptions.models.module')::all()->mapWithKeys(function ($module) {
                                                    return [$module->id => $module->getLabel()];
                                                });

                                                return $modules;
                                            })
                                            ->required()

                                            ->searchable(),
                                        TextInput::make('limit')
                                            ->label(__('filament-modular-subscriptions::modular-subscriptions.resources.plan.fields.module_limit'))
                                            ->numeric()
                                            ->nullable()
                                            ->hint(__('filament-modular-subscriptions::modular-subscriptions.resources.plan.hints.module_limit')),
                                        Forms\Components\TextInput::make('price')
                                            ->label(__('filament-modular-subscriptions::modular-subscriptions.resources.plan.fields.module_price'))
                                            ->numeric()
                                            ->nullable(),

                                        // Forms\Components\KeyValue::make('settings')
                                        //     ->label(__('filament-modular-subscriptions::modular-subscriptions.resources.plan.fields.module_settings'))
                                        //     ->keyLabel(__('filament-modular-subscriptions::modular-subscriptions.resources.plan.fields.setting_key'))
                                        //     ->valueLabel(__('filament-modular-subscriptions::modular-subscriptions.resources.plan.fields.setting_value'))
                                        //     ->keyPlaceholder(__('filament-modular-subscriptions::modular-subscriptions.resources.plan.placeholders.setting_key'))
                                        //     ->valuePlaceholder(__('filament-modular-subscriptions::modular-subscriptions.resources.plan.placeholders.setting_value'))
                                        //     ->nullable(),
                                    ])

                                    ->itemLabel(fn(array $state): ?string => config('filament-modular-subscriptions.models.module')::find($state['module_id'])?->getLabel() ?? null)
                                    ->collapsible()
                                    ->addActionLabel(__('filament-modular-subscriptions::modular-subscriptions.resources.plan.actions.add_module')),
                            ]),
                    ]),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('trans_name')
                    ->label(__('filament-modular-subscriptions::modular-subscriptions.resources.plan.fields.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money(config('filament-modular-subscriptions.main_currency'), locale: 'en')
                    ->label(__('filament-modular-subscriptions::modular-subscriptions.resources.plan.fields.price'))
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label(__('filament-modular-subscriptions::modular-subscriptions.resources.plan.fields.is_active')),
                Tables\Columns\TextColumn::make('invoice_period')
                    ->label(__('filament-modular-subscriptions::modular-subscriptions.resources.plan.fields.invoice_period'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('invoice_interval')
                    ->label(__('filament-modular-subscriptions::modular-subscriptions.resources.plan.fields.invoice_interval')),
                Tables\Columns\TextColumn::make('modules_count')
                    ->counts('modules')
                    ->label(__('filament-modular-subscriptions::modular-subscriptions.resources.plan.fields.modules_count')),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')
                    ->options([
                        true => __('filament-modular-subscriptions::modular-subscriptions.active'),
                        false => __('filament-modular-subscriptions::modular-subscriptions.inactive'),
                    ])
                    ->label(__('filament-modular-subscriptions::modular-subscriptions.resources.plan.fields.is_active')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
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
            'index' => ListPlans::route('/'),
            'create' => CreatePlan::route('/create'),
            'edit' => EditPlan::route('/{record}/edit'),
        ];
    }
}
