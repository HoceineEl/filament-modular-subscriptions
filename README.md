# Filament Modular Subscriptions

[![Latest Version on Packagist](https://img.shields.io/packagist/v/hoceineel/filament-modular-subscriptions.svg?style=flat-square)](https://packagist.org/packages/hoceineel/filament-modular-subscriptions)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/hoceineel/filament-modular-subscriptions/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/hoceineel/filament-modular-subscriptions/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/hoceineel/filament-modular-subscriptions/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/hoceineel/filament-modular-subscriptions/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/hoceineel/filament-modular-subscriptions.svg?style=flat-square)](https://packagist.org/packages/hoceineel/filament-modular-subscriptions)

A powerful Filament plugin for managing modular subscriptions with pricing and usage calculation.

## Features

- Manage subscription plans with flexible pricing options
- Create and manage modules for granular feature control
- Track module usage and calculate pricing based on usage
- Support for trial periods, grace periods, and different billing intervals
- Multi-currency support
- Translatable fields for multilingual applications
- Seamless integration with Filament admin panel

## Installation

You can install the package via composer:

```bash
composer require hoceineel/filament-modular-subscriptions
```

After installation, publish and run the migrations:

```bash
php artisan vendor:publish --tag="filament-modular-subscriptions-migrations"
php artisan migrate
```

Publish the config file:

```bash
php artisan vendor:publish --tag="filament-modular-subscriptions-config"
```

## Configuration

The published config file `config/filament-modular-subscriptions.php` allows you to customize various aspects of the package. You can specify:

- Available modules
- Custom model classes
- Custom resource classes
- Supported currencies
- Locales for translations

## Usage

### Registering the Plugin

Register the plugin in your Filament panel provider:

```php
use HoceineEl\FilamentModularSubscriptions\ModularSubscriptionsPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            // ...
            ->plugins([
                ModularSubscriptionsPlugin::make(),
            ]);
    }
}
```

### Creating a Module

To create a new module, extend the `BaseModule` class:

```php
use HoceineEl\FilamentModularSubscriptions\Modules\BaseModule;
use HoceineEl\FilamentModularSubscriptions\Models\Subscription;

class MyFeatureModule extends BaseModule
{
    public function getName(): string
    {
        return 'My Feature';
    }

    public function getLabelKey(): string
    {
        return 'my_feature_module';
    }

    public function calculateUsage(Subscription $subscription): int
    {
        // Implement your usage calculation logic here
    }

    public function getPricing(Subscription $subscription): float
    {
        // Implement your pricing logic here
    }

    public function canUse(Subscription $subscription): bool
    {
        // Implement your access control logic here
    }
}
```

Then, register your module in the `config/filament-modular-subscriptions.php` file.

### Managing Subscriptions

The package provides Filament resources for managing plans, subscriptions, modules, and module usage. These are automatically available in your Filament admin panel after registering the plugin.

### Checking Subscription Status

You can use the provided methods on the `Subscription` model to check the status of a subscription:

```php
$subscription->onTrial(); // Check if the subscription is on trial
$subscription->hasExpiredTrial(); // Check if the trial has expired
```

## Multilingual Support

The package supports translatable fields for plans. Make sure to set the `translatable` option to `true` in the config file and specify the locales you want to support.

## Extending the Package

You can extend or override the default behavior of the package by:

1. Extending the provided models and specifying your custom models in the config file.
2. Creating custom Filament resources that extend the package's resources.
3. Implementing custom modules to add new features or modify existing ones.


Usage Examples
Creating a Plan
You can create a new subscription plan using the Plan model:

```php
use HoceineEl\FilamentModularSubscriptions\Models\Plan;

$plan = Plan::create([
    'name' => ['en' => 'Pro Plan', 'es' => 'Plan Pro'],
    'slug' => 'pro-plan',
    'description' => ['en' => 'Our premium offering', 'es' => 'Nuestra oferta premium'],
    'is_active' => true,
    'price' => 99.99,
    'currency' => 'USD',
    'trial_period' => 14,
    'trial_interval' => 'day',
    'invoice_period' => 1,
    'invoice_interval' => 'month',
    'grace_period' => 3,
    'grace_interval' => 'day',
]);
Creating a Subscription
To create a new subscription for a user:
phpCopyuse HoceineEl\FilamentModularSubscriptions\Models\Subscription;

$user = User::find(1);
$plan = Plan::where('slug', 'pro-plan')->first();

$subscription = Subscription::create([
    'plan_id' => $plan->id,
    'subscribable_id' => $user->id,
    'subscribable_type' => get_class($user),
    'starts_at' => now(),
    'ends_at' => now()->addMonth(),
    'trial_ends_at' => now()->addDays(14),
    'status' => 'active',
]);
```

Using a Custom Module
Here's an example of how to implement and use a custom module for tracking API calls:

```php
use HoceineEl\FilamentModularSubscriptions\Modules\BaseModule;
use HoceineEl\FilamentModularSubscriptions\Models\Subscription;

class ApiCallsModule extends BaseModule
{
    public function getName(): string
    {
        return 'API Calls';
    }

    public function getLabelKey(): string
    {
        return 'api_calls_module';
    }

    public function calculateUsage(Subscription $subscription): int
    {
        // Assume we have a method to get API calls for the subscription's user
        return $subscription->subscribable->getApiCallsCount();
    }

    public function getPricing(Subscription $subscription): float
    {
        $usage = $this->calculateUsage($subscription);
        // Example pricing: $0.01 per API call
        return $usage * 0.01;
    }

    public function canUse(Subscription $subscription): bool
    {
        $usage = $this->calculateUsage($subscription);
        // Example: Allow up to 10,000 API calls
        return $usage < 10000;
    }
}
Then, in your application code:

```php
use HoceineEl\FilamentModularSubscriptions\Facades\ModularSubscriptions;

$user = User::find(1);
$subscription = $user->subscription;

$apiCallsModule = ModularSubscriptions::module('ApiCallsModule');

if ($apiCallsModule->canUse($subscription)) {
    // Proceed with API call
    $user->makeApiCall();
} else {
    // Notify user they've reached their limit
    throw new \Exception('API call limit reached');
}
```

Checking Subscription Status
Here's how you can check various aspects of a subscription's status:

```php
$subscription = User::find(1)->subscription;

if ($subscription->onTrial()) {
    echo "This subscription is currently on trial.";
}

if ($subscription->hasExpiredTrial()) {
    echo "The trial period for this subscription has ended.";
}

if ($subscription->isActive()) {
    echo "This subscription is active.";
}

if ($subscription->isInactive()) {
    echo "This subscription is inactive.";
}

if ($subscription->isCancelled()) {
    echo "This subscription has been cancelled.";
}

if ($subscription->hasEnded()) {
    echo "This subscription has ended.";
}

## Testing

Run the package tests with:

```bash
composer test
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
