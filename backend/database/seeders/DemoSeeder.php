<?php

namespace Database\Seeders;

use App\Constants\InvoiceActivityConstants;
use App\Constants\InvoiceConstants;
use App\Constants\RecurringInvoiceConstants;
use App\Models\BusinessProfile;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceActivity;
use App\Models\InvoiceLineItem;
use App\Models\InvoiceStatusHistory;
use App\Models\RecurringInvoice;
use App\Models\RecurringInvoiceLineItem;
use App\Models\User;
use App\Modules\V1\Invoice\Helpers\InvoiceHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    private const DEMO_EMAIL = 'demo@invoicehub.app';

    private const DEMO_PASSWORD = 'Demo@12345';

    /**
     * @var array<int, array{0: string, 1: int, 2: int}>
     */
    private const CATALOG = [
        ['Website redesign - Phase 1', 35000, 65000],
        ['Logo & brand identity package', 12000, 22000],
        ['UI/UX consulting retainer', 25000, 40000],
        ['Monthly hosting & maintenance', 2000, 4000],
        ['Freight coordination services', 40000, 70000],
        ['Warehouse audit', 8000, 15000],
        ['Data analytics dashboard setup', 60000, 95000],
        ['Social media management', 10000, 20000],
        ['Festive campaign creatives', 6000, 12000],
        ['Custom CRM integration', 90000, 140000],
        ['Inventory audit service', 18000, 32000],
        ['Quarterly SEO audit', 15000, 28000],
        ['Pop-up store design concept', 25000, 45000],
        ['Interior fit-out consulting', 50000, 80000],
        ['Wellness program branding', 20000, 35000],
        ['Annual compliance filing support', 15000, 25000],
    ];

    private InvoiceHelper $invoiceHelper;

    private int $sequence = 1;

    public function run(): void
    {
        $this->invoiceHelper = new InvoiceHelper;

        DB::transaction(function () {
            $user = $this->createDemoUser();
            $this->createBusinessProfile($user);
            $clients = $this->createClients($user);
            $this->createInvoices($user, $clients);
            $this->createRecurringInvoices($user, $clients);
        });

        $this->command->info('Demo data seeded.');
        $this->command->info('  Login: '.self::DEMO_EMAIL.' / '.self::DEMO_PASSWORD);
    }

    private function createDemoUser(): User
    {
        return User::create([
            'first_name' => 'Demo',
            'last_name' => 'User',
            'email' => self::DEMO_EMAIL,
            'password' => Hash::make(self::DEMO_PASSWORD),
        ]);
    }

    private function createBusinessProfile(User $user): void
    {
        BusinessProfile::create([
            'user_id' => $user->id,
            'company_name' => 'Harbor & Pine Studio',
            'address' => '48 Residency Road, Bengaluru, Karnataka 560025',
            'phone' => '+91 98450 11223',
            'tax_id' => '29AAAPL1234C1ZV',
        ]);
    }

    /**
     * @return array<int, Client>
     */
    private function createClients(User $user): array
    {
        $clients = [
            ['name' => 'Lumen Creative Agency', 'email' => 'accounts@lumencreative.in', 'phone' => '+91 98200 11234', 'address' => '12 MG Road, Pune, Maharashtra 411001', 'createdDaysAgo' => 340],
            ['name' => 'Northbridge Logistics Pvt Ltd', 'email' => 'billing@northbridgelogistics.com', 'phone' => '+91 98100 55210', 'address' => '7th Floor, Tower B, BKC, Mumbai, Maharashtra 400051', 'createdDaysAgo' => 310],
            ['name' => 'Aarav Mehta', 'email' => 'aarav.mehta@gmail.com', 'phone' => '+91 90040 33221', 'address' => '22 Palm Grove Society, Andheri West, Mumbai, Maharashtra 400058', 'createdDaysAgo' => 260],
            ['name' => 'Clearwater Analytics', 'email' => 'finance@clearwateranalytics.io', 'phone' => '+91 98330 77654', 'address' => 'Plot 14, Cyber City, Gurugram, Haryana 122002', 'createdDaysAgo' => 210],
            ['name' => 'Bloom & Co. Boutique', 'email' => 'hello@bloomandco.in', 'phone' => '+91 97690 44112', 'address' => '3rd Cross, Indiranagar, Bengaluru, Karnataka 560038', 'createdDaysAgo' => 150],
            ['name' => 'Solace Wellness Center', 'email' => 'hello@solacewellness.in', 'phone' => '+91 96540 22187', 'address' => '9 Lakeview Road, Kothrud, Pune, Maharashtra 411038', 'createdDaysAgo' => 95],
            ['name' => 'Ridgeline Builders LLP', 'email' => 'accounts@ridgelinebuilders.com', 'phone' => '+91 99870 66541', 'address' => 'Survey No. 45, Hinjewadi Phase 2, Pune, Maharashtra 411057', 'createdDaysAgo' => 180],
            ['name' => 'Priya Kulkarni', 'email' => 'priya.kulkarni@outlook.com', 'phone' => '+91 90210 44987', 'address' => '14 Sunrise Apartments, Kharadi, Pune, Maharashtra 411014', 'createdDaysAgo' => 120],
            ['name' => 'Meridian Consulting Group', 'email' => 'contact@meridianconsulting.co.in', 'phone' => '+91 98220 33456', 'address' => 'Level 9, One BKC, Mumbai, Maharashtra 400051', 'createdDaysAgo' => 75],
            ['name' => 'Fernweh Travel Co.', 'email' => 'ops@fernwehtravel.com', 'phone' => '+91 90280 11765', 'address' => '5 Church Street, Bengaluru, Karnataka 560001', 'createdDaysAgo' => 40],
            ['name' => 'Rohan Deshpande', 'email' => 'rohan.deshpande@gmail.com', 'phone' => '+91 99675 22341', 'address' => '3 Prabhat Road, Deccan, Pune, Maharashtra 411004', 'createdDaysAgo' => 15],
            ['name' => 'Vantage Point Realty', 'email' => 'billing@vantagepointrealty.in', 'phone' => '+91 97440 88654', 'address' => '21 Marine Drive, Mumbai, Maharashtra 400020', 'createdDaysAgo' => 6],
        ];

        return array_map(function (array $data) use ($user) {
            $client = Client::create([
                'user_id' => $user->id,
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'],
            ]);
            $this->backdate($client, Carbon::now()->subDays($data['createdDaysAgo']));

            return $client;
        }, $clients);
    }

    /**
     * @param  array<int, Client>  $clients
     */
    private function createInvoices(User $user, array $clients): void
    {
        // Two anchor PAID invoices per month for the last 11 months, so the
        // revenue-over-time chart has real variation across every range toggle.
        for ($monthsAgo = 11; $monthsAgo >= 1; $monthsAgo--) {
            foreach ([random_int(2, 10), random_int(12, 26)] as $dayOffset) {
                $issuedDaysAgo = $monthsAgo * 30 + $dayOffset;
                $this->seedInvoice($user, $this->randomClient($clients), InvoiceConstants::STATUS_PAID, $issuedDaysAgo);
            }
        }

        // Extra recent paid invoices, so the current/last month comparison has signal.
        foreach ([50, 44, 37, 29, 23, 17, 13, 9] as $issuedDaysAgo) {
            $this->seedInvoice($user, $this->randomClient($clients), InvoiceConstants::STATUS_PAID, $issuedDaysAgo);
        }

        // Currently open work.
        foreach ([32, 27, 21] as $issuedDaysAgo) {
            $this->seedInvoice($user, $this->randomClient($clients), InvoiceConstants::STATUS_OVERDUE, $issuedDaysAgo);
        }
        foreach ([11, 7, 5] as $issuedDaysAgo) {
            $this->seedInvoice($user, $this->randomClient($clients), InvoiceConstants::STATUS_SENT, $issuedDaysAgo);
        }
        foreach ([3, 1, 0] as $issuedDaysAgo) {
            $this->seedInvoice($user, $this->randomClient($clients), InvoiceConstants::STATUS_DRAFT, $issuedDaysAgo);
        }
        foreach ([26, 15] as $issuedDaysAgo) {
            $this->seedInvoice($user, $this->randomClient($clients), InvoiceConstants::STATUS_CANCELLED, $issuedDaysAgo);
        }
    }

    /**
     * @param  array<int, Client>  $clients
     */
    private function randomClient(array $clients): Client
    {
        return $clients[array_rand($clients)];
    }

    private function seedInvoice(User $user, Client $client, string $status, int $issuedDaysAgo): void
    {
        $itemCount = random_int(1, 2);
        $items = [];
        foreach ((array) array_rand(self::CATALOG, $itemCount) as $index) {
            [$description, $min, $max] = self::CATALOG[$index];
            $items[] = [$description, 1, random_int($min, $max)];
        }

        $issueDate = Carbon::now()->subDays($issuedDaysAgo);
        $dueDate = $issueDate->copy()->addDays(14);

        $subtotal = collect($items)->sum(fn (array $item) => $item[1] * $item[2]);
        $taxRate = 18.0;
        $totals = $this->invoiceHelper->computeTotals($subtotal, $taxRate, 0.0);

        $invoice = Invoice::create([
            'user_id' => $user->id,
            'client_id' => $client->id,
            'invoice_number' => 'INV-'.str_pad((string) $user->id, 3, '0', STR_PAD_LEFT).'-'.str_pad((string) $this->sequence++, 4, '0', STR_PAD_LEFT),
            'status' => InvoiceConstants::STATUS_DRAFT,
            'issue_date' => $issueDate->toDateString(),
            'due_date' => $dueDate->toDateString(),
            'subtotal' => $totals['subtotal'],
            'tax_rate' => $taxRate,
            'tax_amount' => $totals['tax_amount'],
            'discount_amount' => 0.0,
            'total' => $totals['total'],
        ]);
        $this->backdate($invoice, $issueDate);

        foreach ($items as [$description, $quantity, $unitPrice]) {
            InvoiceLineItem::create([
                'invoice_id' => $invoice->id,
                'description' => $description,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'line_total' => round($quantity * $unitPrice, 2),
            ]);
        }

        $this->logActivity($invoice, $user->id, InvoiceActivityConstants::TYPE_CREATED, 'Invoice created.', $issueDate);

        $this->advanceToStatus($invoice, $user, $status, $issueDate, $dueDate);
    }

    private function advanceToStatus(Invoice $invoice, User $user, string $targetStatus, Carbon $issueDate, Carbon $dueDate): void
    {
        if ($targetStatus === InvoiceConstants::STATUS_DRAFT) {
            return;
        }

        $sentAt = $issueDate->copy()->addHours(random_int(2, 30));
        $this->recordTransition($invoice, InvoiceConstants::STATUS_DRAFT, InvoiceConstants::STATUS_SENT, $user->id, $sentAt);
        $invoice->update(['status' => InvoiceConstants::STATUS_SENT]);
        $invoice->forceFill([
            'public_token' => Str::random(48),
            'public_token_expires_at' => Carbon::now()->addDays(InvoiceConstants::PUBLIC_TOKEN_TTL_DAYS),
            'updated_at' => $sentAt,
        ])->save();
        $this->logActivity($invoice, $user->id, InvoiceActivityConstants::TYPE_INVOICE_EMAILED, 'Invoice emailed to '.$invoice->client->email.'.', $sentAt);

        match ($targetStatus) {
            InvoiceConstants::STATUS_SENT => null,
            InvoiceConstants::STATUS_CANCELLED => $this->markCancelled($invoice, $user, $sentAt),
            InvoiceConstants::STATUS_OVERDUE => $this->markOverdue($invoice, $dueDate),
            InvoiceConstants::STATUS_PAID => $this->markPaid($invoice, $sentAt),
            default => null,
        };
    }

    private function markCancelled(Invoice $invoice, User $user, Carbon $sentAt): void
    {
        $at = $sentAt->copy()->addDays(random_int(3, 8));
        $this->recordTransition($invoice, InvoiceConstants::STATUS_SENT, InvoiceConstants::STATUS_CANCELLED, $user->id, $at);
        $invoice->update(['status' => InvoiceConstants::STATUS_CANCELLED]);
        $invoice->forceFill(['updated_at' => $at])->save();
    }

    private function markOverdue(Invoice $invoice, Carbon $dueDate): void
    {
        $at = $dueDate->copy()->addDays(random_int(1, 10));
        if ($at->greaterThan(Carbon::now())) {
            $at = Carbon::now()->subDay();
        }

        $this->recordTransition($invoice, InvoiceConstants::STATUS_SENT, InvoiceConstants::STATUS_OVERDUE, null, $at);
        $invoice->update(['status' => InvoiceConstants::STATUS_OVERDUE]);
        $invoice->forceFill(['updated_at' => $at])->save();
        $this->logActivity($invoice, null, InvoiceActivityConstants::TYPE_OVERDUE_REMINDER_SENT, 'Overdue reminder emailed to '.$invoice->client->email.'.', $at);
    }

    private function markPaid(Invoice $invoice, Carbon $sentAt): void
    {
        $at = $sentAt->copy()->addDays(random_int(5, 20));
        if ($at->greaterThan(Carbon::now())) {
            $at = Carbon::now()->subDay();
        }

        $this->recordTransition($invoice, InvoiceConstants::STATUS_SENT, InvoiceConstants::STATUS_PAID, null, $at);
        $invoice->update(['status' => InvoiceConstants::STATUS_PAID]);
        $invoice->forceFill(['updated_at' => $at])->save();
        $this->logActivity($invoice, null, InvoiceActivityConstants::TYPE_PAID, 'Invoice marked as paid.', $at);
        $this->logActivity($invoice, null, InvoiceActivityConstants::TYPE_RECEIPT_SENT, 'Receipt emailed to '.$invoice->client->email.'.', $at);
    }

    private function recordTransition(Invoice $invoice, string $from, string $to, ?int $changedBy, Carbon $at): void
    {
        $history = InvoiceStatusHistory::create([
            'invoice_id' => $invoice->id,
            'previous_status' => $from,
            'new_status' => $to,
            'changed_by' => $changedBy,
            'notes' => 'Status updated from '.$from.' to '.$to.'.',
        ]);
        $this->backdate($history, $at);
    }

    private function logActivity(Invoice $invoice, ?int $userId, string $type, string $description, Carbon $at): void
    {
        $activity = InvoiceActivity::create([
            'invoice_id' => $invoice->id,
            'user_id' => $userId,
            'type' => $type,
            'description' => $description,
        ]);
        $activity->forceFill(['created_at' => $at])->save();
    }

    private function backdate(Client|Invoice|InvoiceStatusHistory $model, Carbon $createdAt): void
    {
        $model->forceFill(['created_at' => $createdAt, 'updated_at' => $createdAt])->save();
    }

    /**
     * @param  array<int, Client>  $clients
     */
    private function createRecurringInvoices(User $user, array $clients): void
    {
        $plans = [
            ['client' => 1, 'items' => [['Monthly logistics coordination retainer', 1, 50000]]],
            ['client' => 3, 'items' => [['Monthly analytics dashboard maintenance', 1, 15000]]],
            ['client' => 5, 'items' => [['Monthly wellness program retainer', 1, 18000]]],
            ['client' => 8, 'items' => [['Monthly advisory retainer', 1, 32000]]],
        ];

        foreach ($plans as $plan) {
            $this->createRecurringInvoice($user, $clients[$plan['client']], $plan['items']);
        }
    }

    /**
     * @param  array<int, array{0: string, 1: int, 2: int}>  $items
     */
    private function createRecurringInvoice(User $user, Client $client, array $items): void
    {
        $subtotal = collect($items)->sum(fn (array $item) => $item[1] * $item[2]);
        $taxRate = 18.0;
        $totals = $this->invoiceHelper->computeTotals($subtotal, $taxRate, 0.0);

        $recurringInvoice = RecurringInvoice::create([
            'user_id' => $user->id,
            'client_id' => $client->id,
            'frequency' => RecurringInvoiceConstants::FREQUENCY_MONTHLY,
            'status' => RecurringInvoiceConstants::STATUS_ACTIVE,
            'next_run_at' => Carbon::now()->addMonth()->startOfMonth(),
            'last_generated_at' => Carbon::now()->subMonth()->startOfMonth(),
            'subtotal' => $totals['subtotal'],
            'tax_rate' => $taxRate,
            'tax_amount' => $totals['tax_amount'],
            'discount_amount' => 0.0,
            'total' => $totals['total'],
        ]);

        foreach ($items as [$description, $quantity, $unitPrice]) {
            RecurringInvoiceLineItem::create([
                'recurring_invoice_id' => $recurringInvoice->id,
                'description' => $description,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'line_total' => round($quantity * $unitPrice, 2),
            ]);
        }
    }
}
