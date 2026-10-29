<?php

namespace App\Modules\V1\BusinessProfile\Services\Update;

use App\Http\Requests\V1\BusinessProfile\Update\DetailsRequest;
use App\Http\Services\AuthService;
use App\Models\BusinessProfile;
use App\Modules\V1\BusinessProfile\Bo\Update\DetailsBo;
use App\Modules\V1\BusinessProfile\Helpers\BusinessProfileHelper;
use App\Repositories\V1\BusinessProfileRepository;
use App\Utils\CommonUtils;

class DetailsService
{
    public function __construct(
        private BusinessProfileHelper $businessProfileHelper,
        private readonly BusinessProfileRepository $businessProfileRepository,
        private readonly AuthService $authService,
    ) {}

    public function prepareBo(DetailsRequest $detailsRequest): DetailsBo
    {
        return $this->businessProfileHelper->prepareUpdateBo($detailsRequest);
    }

    public function update(DetailsBo $detailsBo): array
    {
        $userId = $this->authService->getData()->get('userId');
        $businessProfile = $this->upsert($userId, $detailsBo);

        return CommonUtils::successDataResponse([
            'business_profile' => $this->businessProfileHelper->format(collect($businessProfile)),
        ]);
    }

    private function upsert(int $userId, DetailsBo $detailsBo): BusinessProfile
    {
        $businessProfileDao = $this->businessProfileHelper->prepareUpdateDao($detailsBo, $userId);
        $existing = $this->businessProfileRepository->findByUserId($userId)->first();

        if ($existing) {
            $this->businessProfileRepository->updateByUserId($userId, $businessProfileDao);

            return $this->businessProfileRepository->findByUserId($userId)->first();
        }

        return $this->businessProfileRepository->insert($businessProfileDao);
    }
}
