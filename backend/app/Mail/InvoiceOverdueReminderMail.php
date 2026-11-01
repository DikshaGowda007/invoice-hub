<?php

namespace App\Mail;

use App\Modules\V1\Invoice\Dto\InvoiceOverdueReminderMailDto;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceOverdueReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public InvoiceOverdueReminderMailDto $dto,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment overdue for invoice '.$this->dto->getInvoiceNumber(),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice_overdue_reminder',
            with: [
                'clientName' => $this->dto->getClientName(),
                'invoiceNumber' => $this->dto->getInvoiceNumber(),
                'total' => $this->dto->getTotal(),
                'dueDate' => $this->dto->getDueDate(),
                'businessName' => $this->dto->getBusinessName(),
            ],
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->dto->getPdfContent(), $this->dto->getInvoiceNumber().'.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
