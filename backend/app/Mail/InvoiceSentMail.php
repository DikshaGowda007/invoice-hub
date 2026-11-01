<?php

namespace App\Mail;

use App\Modules\V1\Invoice\Helpers\InvoicePdfHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class InvoiceSentMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Collection $invoice,
        public Collection $client,
        public Collection $lineItems,
        public Collection $business,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invoice '.$this->invoice->get('invoice_number').' from '.$this->business->get('displayName'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice_sent',
            with: [
                'clientName' => $this->client->get('name'),
                'invoiceNumber' => $this->invoice->get('invoice_number'),
                'total' => $this->invoice->get('total'),
                'dueDate' => $this->invoice->get('due_date'),
                'businessName' => $this->business->get('displayName'),
            ],
        );
    }

    public function attachments(): array
    {
        $pdf = app(InvoicePdfHelper::class)->render($this->invoice, $this->client, $this->lineItems, $this->business);

        return [
            Attachment::fromData(fn () => $pdf->output(), $this->invoice->get('invoice_number').'.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
