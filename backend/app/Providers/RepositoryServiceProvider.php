<?php

namespace App\Providers;

use App\Repositories\MySql\PasswordResetTokenRepositoryImpl;
use App\Repositories\MySql\UserRepositoryImpl;
use App\Repositories\MySql\V1\BusinessProfileRepositoryImpl;
use App\Repositories\MySql\V1\ClientRepositoryImpl;
use App\Repositories\MySql\V1\InvoiceActivityRepositoryImpl;
use App\Repositories\MySql\V1\InvoiceLineItemRepositoryImpl;
use App\Repositories\MySql\V1\InvoiceRepositoryImpl;
use App\Repositories\MySql\V1\InvoiceStatusHistoryRepositoryImpl;
use App\Repositories\MySql\V1\RecurringInvoiceLineItemRepositoryImpl;
use App\Repositories\MySql\V1\RecurringInvoiceRepositoryImpl;
use App\Repositories\PasswordResetTokenRepository;
use App\Repositories\UserRepository;
use App\Repositories\V1\BusinessProfileRepository;
use App\Repositories\V1\ClientRepository;
use App\Repositories\V1\InvoiceActivityRepository;
use App\Repositories\V1\InvoiceLineItemRepository;
use App\Repositories\V1\InvoiceRepository;
use App\Repositories\V1\InvoiceStatusHistoryRepository;
use App\Repositories\V1\RecurringInvoiceLineItemRepository;
use App\Repositories\V1\RecurringInvoiceRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepository::class, UserRepositoryImpl::class);
        $this->app->bind(PasswordResetTokenRepository::class, PasswordResetTokenRepositoryImpl::class);
        $this->app->bind(ClientRepository::class, ClientRepositoryImpl::class);
        $this->app->bind(BusinessProfileRepository::class, BusinessProfileRepositoryImpl::class);
        $this->app->bind(InvoiceRepository::class, InvoiceRepositoryImpl::class);
        $this->app->bind(InvoiceLineItemRepository::class, InvoiceLineItemRepositoryImpl::class);
        $this->app->bind(InvoiceStatusHistoryRepository::class, InvoiceStatusHistoryRepositoryImpl::class);
        $this->app->bind(InvoiceActivityRepository::class, InvoiceActivityRepositoryImpl::class);
        $this->app->bind(RecurringInvoiceRepository::class, RecurringInvoiceRepositoryImpl::class);
        $this->app->bind(RecurringInvoiceLineItemRepository::class, RecurringInvoiceLineItemRepositoryImpl::class);
    }

    public function boot(): void {}
}
