<?php

namespace App\Console\Commands;

use App\Modules\V1\Invoice\Services\MarkOverdue\DetailsService;
use Illuminate\Console\Command;

class MarkOverdueInvoicesCommand extends Command
{
    protected $signature = 'invoices:mark-overdue {--dry-run : Preview what will happen without making any changes}';

    protected $description = 'Mark sent invoices as overdue once their due date has passed and notify clients';

    public function __construct(private readonly DetailsService $detailsService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $result = $this->detailsService->markDue($dryRun);

        if ($result['locked']) {
            $this->warn('Skipped — a previous run is still in progress.');

            return self::SUCCESS;
        }

        $this->info(sprintf(
            '%sDue: %d, Marked: %d, Failed: %d',
            $dryRun ? '[DRY RUN] ' : '',
            $result['due'],
            $result['marked'],
            $result['failed'],
        ));

        return self::SUCCESS;
    }
}
