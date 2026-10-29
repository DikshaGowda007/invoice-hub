<?php

namespace App\Repositories\MySql\V1;

use App\Models\BusinessProfile;
use App\Repositories\DAO\V1\BusinessProfileDAO;
use App\Repositories\V1\BusinessProfileRepository;
use Illuminate\Database\Eloquent\Collection;

class BusinessProfileRepositoryImpl implements BusinessProfileRepository
{
    public function insert(BusinessProfileDAO $businessProfileDao): BusinessProfile
    {
        return BusinessProfile::create($businessProfileDao->toArray());
    }

    public function findByUserId(int $userId): Collection
    {
        return BusinessProfile::where('user_id', $userId)->get();
    }

    public function updateByUserId(int $userId, BusinessProfileDAO $businessProfileDao): bool
    {
        return BusinessProfile::where('user_id', $userId)->update($businessProfileDao->toArray());
    }
}
