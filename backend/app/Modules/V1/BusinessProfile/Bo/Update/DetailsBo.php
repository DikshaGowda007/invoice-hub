<?php

namespace App\Modules\V1\BusinessProfile\Bo\Update;

class DetailsBo
{
    private ?string $companyName = null;

    private ?string $address = null;

    private ?string $phone = null;

    private ?string $taxId = null;

    public function toArray(): array
    {
        $collection = [];

        if (isset($this->companyName)) {
            $collection['company_name'] = $this->companyName;
        }
        if (isset($this->address)) {
            $collection['address'] = $this->address;
        }
        if (isset($this->phone)) {
            $collection['phone'] = $this->phone;
        }
        if (isset($this->taxId)) {
            $collection['tax_id'] = $this->taxId;
        }

        return $collection;
    }

    /**
     * Get the value of companyName
     */
    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    /**
     * Set the value of companyName
     */
    public function setCompanyName(?string $companyName): self
    {
        $this->companyName = $companyName;

        return $this;
    }

    /**
     * Get the value of address
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * Set the value of address
     */
    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get the value of phone
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * Set the value of phone
     */
    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get the value of taxId
     */
    public function getTaxId(): ?string
    {
        return $this->taxId;
    }

    /**
     * Set the value of taxId
     */
    public function setTaxId(?string $taxId): self
    {
        $this->taxId = $taxId;

        return $this;
    }
}
