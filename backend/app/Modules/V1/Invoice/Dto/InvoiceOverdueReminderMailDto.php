<?php

namespace App\Modules\V1\Invoice\Dto;

class InvoiceOverdueReminderMailDto
{
    private ?string $clientName = null;

    private ?string $invoiceNumber = null;

    private ?float $total = null;

    private ?string $dueDate = null;

    private ?string $businessName = null;

    private ?string $pdfContent = null;

    public function getClientName(): ?string
    {
        return $this->clientName;
    }

    public function setClientName(?string $clientName): self
    {
        $this->clientName = $clientName;

        return $this;
    }

    public function getInvoiceNumber(): ?string
    {
        return $this->invoiceNumber;
    }

    public function setInvoiceNumber(?string $invoiceNumber): self
    {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(?float $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getDueDate(): ?string
    {
        return $this->dueDate;
    }

    public function setDueDate(?string $dueDate): self
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    public function getBusinessName(): ?string
    {
        return $this->businessName;
    }

    public function setBusinessName(?string $businessName): self
    {
        $this->businessName = $businessName;

        return $this;
    }

    public function getPdfContent(): ?string
    {
        return $this->pdfContent;
    }

    public function setPdfContent(?string $pdfContent): self
    {
        $this->pdfContent = $pdfContent;

        return $this;
    }
}
