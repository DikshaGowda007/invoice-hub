<?php

namespace App\Modules\V1\Invoice\Dto;

class InvoiceReceiptMailDto
{
    private ?string $clientName = null;

    private ?string $invoiceNumber = null;

    private ?float $total = null;

    private ?string $issueDate = null;

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

    public function getIssueDate(): ?string
    {
        return $this->issueDate;
    }

    public function setIssueDate(?string $issueDate): self
    {
        $this->issueDate = $issueDate;

        return $this;
    }
}
