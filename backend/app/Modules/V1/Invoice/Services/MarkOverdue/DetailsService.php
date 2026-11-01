<?php

namespace App\Modules\V1\Invoice\Services\MarkOverdue;

use App\Constants\InvoiceConstants;
use App\Events\InvoiceOverdue;
use App\Modules\V1\Invoice\Helpers\InvoiceHelper;
use App\Repositories\DAO\V1\InvoiceStatusHistoryDAO;
use App\Repositories\V1\InvoiceRepository;
use App\Repositories\V1\InvoiceStatusHistoryRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class DetailsService
{
    private const LOCK_KEY = 'invoices:mark-overdue';

    private const LOCK_TTL_SECONDS = 900;

    public function __construct(
        private InvoiceHelper $invoiceHelper,
        private readonly InvoiceRepository $invoiceRepository,
        private readonly InvoiceStatusHistoryRepository $invoiceStatusHistoryRepository,
    ) {}

    /**
     * @return array{due: int, marked: int, failed: int, locked: bool}
     */
    public function markDue(bool $dryRun = false): array
    {
        $lock = Cache::lock(self::LOCK_KEY, self::LOCK_TTL_SECONDS);

        if (! $lock->get()) {
            Log::warning('invoices.mark_overdue.locked', [
                'message' => 'Skipped — a previous run is still holding the lock.',
            ]);

            return ['due' => 0, 'marked' => 0, 'failed' => 0, 'locked' => true];
        }

        try {
            return $this->execute($dryRun);
        } finally {
            $lock->release();
        }
    }

    private function execute(bool $dryRun): array
    {
        $today = Carbon::now()->toDateString();
        $candidates = $this->invoiceRepository->findByStatusAndDueDate(InvoiceConstants::STATUS_SENT, $today);

        $marked = 0;
        $failedIds = [];

        foreach ($candidates as $invoiceModel) {
            if ($dryRun) {
                continue;
            }

            $invoice = collect($invoiceModel);

            try {
                $this->markOverdue($invoice);
                $marked++;
            } catch (Throwable $e) {
                $failedIds[] = $invoice->get('id');

                Log::warning('invoices.mark_overdue.failed', [
                    'invoice_id' => $invoice->get('id'),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $summary = [
            'due' => $candidates->count(),
            'marked' => $marked,
            'failed' => count($failedIds),
            'locked' => false,
        ];

        Log::info('invoices.mark_overdue.summary', $summary + ['dry_run' => $dryRun]);

        return $summary;
    }

    private function markOverdue(Collection $invoice): void
    {
        $id = (int) $invoice->get('id');
        $userId = (int) $invoice->get('user_id');

        $this->invoiceRepository->updateById($id, $this->invoiceHelper->prepareUpdateStatusDao(InvoiceConstants::STATUS_OVERDUE));

        $historyDao = new InvoiceStatusHistoryDAO;
        $historyDao->setInvoiceId($id);
        $historyDao->setPreviousStatus(InvoiceConstants::STATUS_SENT);
        $historyDao->setNewStatus(InvoiceConstants::STATUS_OVERDUE);
        $historyDao->setNotes('Automatically marked overdue — due date passed.');
        $this->invoiceStatusHistoryRepository->insert($historyDao);

        InvoiceOverdue::dispatch($id, $userId);
    }
}
