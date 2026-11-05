<?php

namespace App\Mail;

use App\Modules\V1\Invoice\Dto\InvoiceSentMailDto;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceSentMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public InvoiceSentMailDto $dto,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invoice '.$this->dto->getInvoiceNumber().' from '.$this->dto->getBusinessName(),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice_sent',
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
