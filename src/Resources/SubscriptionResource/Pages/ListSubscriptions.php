<?php

namespace HoceineEl\FilamentModularSubscriptions\Resources\SubscriptionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use HoceineEl\FilamentModularSubscriptions\Resources\SubscriptionResource;

class ListSubscriptions extends ListRecords
{
    protected static string $resource = SubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
