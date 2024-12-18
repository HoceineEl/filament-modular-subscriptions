<?php

namespace NewTags\FilamentModularSubscriptions\Models;

use NewTags\FilamentModularSubscriptions\Modules\BaseModule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    protected $fillable = ['name', 'class', 'is_active', 'is_persistent'];

    protected $casts = [
        'is_active' => 'boolean',
        'is_persistent' => 'boolean',
    ];

    public function getTable()
    {
        return config('filament-modular-subscriptions.tables.module');
    }

    public function moduleUsages(): HasMany
    {
        return $this->hasMany(config('filament-modular-subscriptions.models.usage'));
    }

    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    public function scopeActive($query): Builder
    {
        return $query->where('is_active', true);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    public static function registerModule(string $moduleClass): self
    {
        $module = new $moduleClass;

        return self::updateOrCreate(
            ['name' => $module->getName()],
            [
                'class' => $moduleClass,
                'is_active' => true,
                'is_persistent' => true,
            ]
        );
    }

    public function getInstance(): BaseModule
    {
        return new $this->class;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabelKey(): string
    {
        return $this->getInstance()->getLabelKey();
    }

    public function calculateUsage(Subscription $subscription): int
    {
        return $this->getInstance()->calculateUsage($subscription);
    }

    public function getPricing(Subscription $subscription): float
    {
        return $this->getInstance()->getPricing($subscription);
    }

    public function canUse(Subscription $subscription): bool
    {
        return $this->getInstance()->canUse($subscription);
    }

    public function getLabel(): string
    {
        return $this->getInstance()->getLabel();
    }

    public function planModules(): HasMany
    {
        return $this->hasMany(PlanModule::class);
    }

    public function plans(): HasMany
    {
        return $this->hasMany(config('filament-modular-subscriptions.models.plan_module'));
    }
}
