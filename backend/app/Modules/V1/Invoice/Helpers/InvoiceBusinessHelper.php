<?php

namespace App\Modules\V1\Invoice\Helpers;

use App\Models\User;
use App\Repositories\V1\BusinessProfileRepository;
use Illuminate\Support\Collection;

class InvoiceBusinessHelper
{
    public function __construct(
        private readonly BusinessProfileRepository $businessProfileRepository,
    ) {}

    public function format(int $userId): Collection
    {
        $user = $this->findUser($userId);
        $businessProfile = $this->findBusinessProfile($userId);

        return collect([
            'firstName' => $user->get('first_name'),
            'lastName' => $user->get('last_name'),
            'email' => $user->get('email'),
            'displayName' => $this->resolveDisplayName($user, $businessProfile),
            'address' => $businessProfile->get('address'),
            'phone' => $businessProfile->get('phone'),
            'taxId' => $businessProfile->get('tax_id'),
        ]);
    }

    private function findUser(int $userId): Collection
    {
        return collect(User::find($userId));
    }

    private function findBusinessProfile(int $userId): Collection
    {
        return collect($this->businessProfileRepository->findByUserId($userId)->first());
    }

    private function resolveDisplayName(Collection $user, Collection $businessProfile): string
    {
        $companyName = $businessProfile->get('company_name');
        $fullName = trim($user->get('first_name').' '.$user->get('last_name'));

        return $companyName ?: $fullName;
    }
}
