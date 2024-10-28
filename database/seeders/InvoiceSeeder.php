<?php

namespace Database\Seeders;

use Carbon\Carbon;
use HoceineEl\FilamentModularSubscriptions\Enums\PaymentStatus;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    public function run()
    {
        $subscriptionModel = config('filament-modular-subscriptions.models.subscription');
        $invoiceModel = config('filament-modular-subscriptions.models.invoice');
        $invoiceItemModel = config('filament-modular-subscriptions.models.invoice_item');

        $subscriptions = $subscriptionModel::with(['plan', 'subscribable', 'moduleUsages.module'])->get();

        foreach ($subscriptions as $subscription) {
            $invoiceCount = rand(1, 3);

            for ($i = 0; $i < $invoiceCount; $i++) {
                $invoiceDate = $subscription->starts_at->addMonths($i);
                $dueDate = $invoiceDate->copy()->addDays(config('filament-modular-subscriptions.invoice_due_date_days', 7));

                $invoice = $invoiceModel::create([
                    'subscription_id' => $subscription->id,
                    'tenant_id' => $subscription->subscribable_id,
                    'amount' => 0,
                    'status' => $this->getRandomStatus(),
                    'due_date' => $dueDate,
                    'paid_at' => $this->getPaidAtDate($dueDate),
                    'created_at' => $invoiceDate,
                    'updated_at' => $invoiceDate,
                ]);

                // Add subscription fee as an invoice item
                if (! $subscription->plan->is_pay_as_you_go) {
                    $invoiceItemModel::create([
                        'invoice_id' => $invoice->id,
                        'description' => __('filament-modular-subscriptions::fms.invoice.subscription_fee', ['plan' => $subscription->plan->trans_name]),
                        'quantity' => 1,
                        'unit_price' => $subscription->plan->price,
                        'total' => $subscription->plan->price,
                    ]);
                } else {
                    foreach ($subscription->moduleUsages as $moduleUsage) {
                        if ($moduleUsage->usage > 0) {
                            $unitPrice = $subscription->plan->modulePrice($moduleUsage->module);
                            $total = $moduleUsage->usage * $unitPrice;

                            $invoiceItemModel::create([
                                'invoice_id' => $invoice->id,
                                'description' => __('filament-modular-subscriptions::fms.invoice.module_usage', ['module' => $moduleUsage->module->getName()]),
                                'quantity' => $moduleUsage->usage,
                                'unit_price' => $unitPrice,
                                'total' => $total,
                            ]);
                        }
                    }
                }
                $totalAmount = $invoice->items()->sum('total');
                $invoice->update(['amount' => $totalAmount]);
            }
        }
    }

    private function getRandomStatus(): string
    {
        $statuses = PaymentStatus::cases();

        return array_rand($statuses);
    }

    private function getPaidAtDate(?Carbon $dueDate): ?Carbon
    {
        if (rand(0, 1) === 0) {
            return null; // 50% chance of being unpaid
        }

        // Paid between invoice creation and 5 days after due date
        return $dueDate->copy()->subDays(rand(0, $dueDate->diffInDays(now()) + 5));
    }
}
