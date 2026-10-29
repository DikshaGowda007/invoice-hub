<?php

namespace App\Modules\V1\BusinessProfile\Services\Get;

use App\Http\Services\AuthService;
use App\Modules\V1\BusinessProfile\Helpers\BusinessProfileHelper;
use App\Repositories\V1\BusinessProfileRepository;
use App\Utils\CommonUtils;

class DetailsService
{
    public function __construct(
        private readonly BusinessProfileHelper $businessProfileHelper,
        private readonly BusinessProfileRepository $businessProfileRepository,
        private readonly AuthService $authService,
    ) {}

    public function get(): array
    {
        $userId = $this->authService->getData()->get('userId');
        $businessProfile = $this->businessProfileRepository->findByUserId($userId)->first();

        return CommonUtils::successDataResponse([
            'business_profile' => $this->businessProfileHelper->format(collect($businessProfile)),
        ]);
    }
}
