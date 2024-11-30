<?php

namespace HoceineEl\FilamentModularSubscriptions\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use HoceineEl\FilamentModularSubscriptions\Components\FileEntry;
use HoceineEl\FilamentModularSubscriptions\Enums\InvoiceStatus;
use HoceineEl\FilamentModularSubscriptions\Enums\PaymentMethod;
use HoceineEl\FilamentModularSubscriptions\Enums\PaymentStatus;
use HoceineEl\FilamentModularSubscriptions\Resources\PaymentResource\Pages;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PaymentResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    public static function getModel(): string
    {
        return config('filament-modular-subscriptions.models.payment');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament-modular-subscriptions::fms.menu_group.subscription_management');
    }

    public static function getModelLabel(): string
    {
        return __('filament-modular-subscriptions::fms.resources.payment.singular_name');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-modular-subscriptions::fms.resources.payment.name');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice.subscription.subscribable.name')
                    ->sortable()
                    ->label(__('filament-modular-subscriptions::fms.resources.payment.fields.subscriber')),
                Tables\Columns\TextColumn::make('amount')
                    ->money(fn($record) => $record->invoice->subscription->plan->currency, locale: 'en')
                    ->sortable()
                    ->label(__('filament-modular-subscriptions::fms.resources.payment.fields.amount')),
                Tables\Columns\TextColumn::make('payment_method')
                    ->searchable()
                    ->badge()
                    ->label(__('filament-modular-subscriptions::fms.resources.payment.fields.payment_method')),
                Tables\Columns\TextColumn::make('transaction_id')
                    ->searchable()
                    ->toggledHiddenByDefault()
                    ->label(__('filament-modular-subscriptions::fms.resources.payment.fields.transaction_id')),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->label(__('filament-modular-subscriptions::fms.resources.payment.fields.status')),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label(__('filament-modular-subscriptions::fms.resources.payment.fields.created_at')),
                Tables\Columns\TextColumn::make('reviewed_at')
                    ->dateTime()
                    ->sortable()
                    ->toggledHiddenByDefault()
                    ->label(__('filament-modular-subscriptions::fms.resources.payment.fields.reviewed_at')),
                Tables\Columns\TextColumn::make('reviewer.name')
                    ->toggledHiddenByDefault()
                    ->label(__('filament-modular-subscriptions::fms.resources.payment.fields.reviewed_by')),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(PaymentStatus::class)
                    ->default(PaymentStatus::PENDING->value)
                    ->label(__('filament-modular-subscriptions::fms.resources.payment.fields.status')),
                Tables\Filters\SelectFilter::make('payment_method')
                    ->options(PaymentMethod::class)
                    ->default(PaymentMethod::BANK_TRANSFER->value)
                    ->label(__('filament-modular-subscriptions::fms.resources.payment.fields.payment_method')),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->label(__('filament-modular-subscriptions::fms.resources.payment.fields.created_from')),
                        DatePicker::make('created_until')
                            ->label(__('filament-modular-subscriptions::fms.resources.payment.fields.created_until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
                Tables\Filters\Filter::make('amount')
                    ->form([
                        TextInput::make('amount_from')
                            ->numeric()
                            ->label(__('filament-modular-subscriptions::fms.resources.payment.fields.amount_from')),
                        TextInput::make('amount_to')
                            ->numeric()
                            ->label(__('filament-modular-subscriptions::fms.resources.payment.fields.amount_to')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['amount_from'],
                                fn(Builder $query, $amount): Builder => $query->where('amount', '>=', $amount),
                            )
                            ->when(
                                $data['amount_to'],
                                fn(Builder $query, $amount): Builder => $query->where('amount', '<=', $amount),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Action::make('approve')
                    ->label(__('filament-modular-subscriptions::fms.resources.payment.actions.approve'))
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn($record) => $record->status === PaymentStatus::PENDING)
                    ->form([
                        TextInput::make('admin_notes')
                            ->label(__('filament-modular-subscriptions::fms.resources.payment.fields.admin_notes')),
                    ])
                    ->action(function ($record, array $data) {
                        DB::transaction(function () use ($record, $data) {
                            $record->update([
                                'status' => PaymentStatus::PAID,
                                'admin_notes' => $data['admin_notes'],
                                'reviewed_at' => now(),
                                'reviewed_by' => auth()->id(),
                            ]);

                            $invoice = $record->invoice;
                            $totalPaid = $invoice->payments()->where('status', PaymentStatus::PAID)->sum('amount');

                            if ($totalPaid >= $invoice->amount) {
                                $invoice->update([
                                    'status' => InvoiceStatus::PAID,
                                    'paid_at' => now(),
                                ]);

                                $invoice->subscription->renew();

                                Notification::make()
                                    ->title(__('filament-modular-subscriptions::fms.payment.subscription_renewed'))
                                    ->success()
                                    ->send();
                            } elseif ($totalPaid > 0) {
                                $invoice->update(['status' => InvoiceStatus::PARTIALLY_PAID]);

                                Notification::make()
                                    ->title(__('filament-modular-subscriptions::fms.payment.partially_paid'))
                                    ->success()
                                    ->send();
                            }
                        });

                        Notification::make()
                            ->title(__('filament-modular-subscriptions::fms.payment.approved'))
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->label(__('filament-modular-subscriptions::fms.resources.payment.actions.reject'))
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn($record) => $record->status === PaymentStatus::PENDING)
                    ->requiresConfirmation()
                    ->form([
                        TextInput::make('admin_notes')
                            ->label(__('filament-modular-subscriptions::fms.resources.payment.fields.admin_notes'))
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status' => PaymentStatus::CANCELLED,
                            'admin_notes' => $data['admin_notes'],
                            'reviewed_at' => now(),
                            'reviewed_by' => auth()->id(),
                        ]);

                        Notification::make()
                            ->title(__('filament-modular-subscriptions::fms.payment.rejected'))
                            ->danger()
                            ->send();
                    }),
                Action::make('undo')
                    ->label(__('filament-modular-subscriptions::fms.resources.payment.actions.undo'))
                    ->color('warning')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->visible(fn($record) => in_array($record->status, [PaymentStatus::PAID, PaymentStatus::CANCELLED]))
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        DB::transaction(function () use ($record) {
                            if ($record->status === PaymentStatus::PAID) {
                                $invoice = $record->invoice;
                                $totalPaid = $invoice->payments()->where('status', PaymentStatus::PAID)->where('id', '!=', $record->id)->sum('amount');

                                if ($totalPaid >= $invoice->amount) {
                                    $invoice->update(['status' => InvoiceStatus::PAID]);
                                } elseif ($totalPaid > 0) {
                                    $invoice->update(['status' => InvoiceStatus::PARTIALLY_PAID]);
                                } else {
                                    $invoice->update([
                                        'status' => InvoiceStatus::UNPAID,
                                        'paid_at' => null,
                                    ]);
                                }
                            }

                            $record->update([
                                'status' => PaymentStatus::PENDING,
                                'admin_notes' => null,
                                'reviewed_at' => null,
                                'reviewed_by' => null,
                            ]);

                            Notification::make()
                                ->title(__('filament-modular-subscriptions::fms.payment.undone'))
                                ->success()
                                ->send();
                        });
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('filament-modular-subscriptions::fms.resources.payment.sections.payment_details'))
                    ->schema([
                        Infolists\Components\TextEntry::make('invoice.subscription.subscriber.name')
                            ->label(__('filament-modular-subscriptions::fms.resources.payment.fields.subscriber')),
                        Infolists\Components\TextEntry::make('amount')
                            ->money(fn($record) => $record->invoice->subscription->plan->currency, locale: 'en')
                            ->label(__('filament-modular-subscriptions::fms.resources.payment.fields.amount')),
                        Infolists\Components\TextEntry::make('payment_method')
                            ->badge()
                            ->label(__('filament-modular-subscriptions::fms.resources.payment.fields.payment_method')),
                        Infolists\Components\TextEntry::make('transaction_id')
                            ->label(__('filament-modular-subscriptions::fms.resources.payment.fields.transaction_id')),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->label(__('filament-modular-subscriptions::fms.resources.payment.fields.status')),
                        Infolists\Components\TextEntry::make('created_at')
                            ->dateTime()
                            ->label(__('filament-modular-subscriptions::fms.resources.payment.fields.created_at')),
                    ])->columns(),
                FileEntry::make('receipt_file')
                    ->label(__('filament-modular-subscriptions::fms.resources.payment.fields.receipt_file'))
                    ->getStateUsing(fn($record) => $record->receipt_file ? Storage::url($record->receipt_file) : null)
                    ->visible(fn($record) => $record->receipt_file),
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
            'index' => Pages\ListPayments::route('/'),
        ];
    }
}
