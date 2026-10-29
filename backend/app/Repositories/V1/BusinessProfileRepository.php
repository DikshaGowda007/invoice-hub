<?php

namespace App\Repositories\V1;

use App\Models\BusinessProfile;
use App\Repositories\DAO\V1\BusinessProfileDAO;
use Illuminate\Database\Eloquent\Collection;

interface BusinessProfileRepository
{
    public function insert(BusinessProfileDAO $businessProfileDao): BusinessProfile;

    public function findByUserId(int $userId): Collection;

    public function updateByUserId(int $userId, BusinessProfileDAO $businessProfileDao): bool;
}
