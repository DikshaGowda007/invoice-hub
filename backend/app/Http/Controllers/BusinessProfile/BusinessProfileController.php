<?php

namespace App\Http\Controllers\BusinessProfile;

use App\Constants\HttpStatusConstant;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\BusinessProfile\Get\DetailsRequest as GetDetailsRequest;
use App\Http\Requests\V1\BusinessProfile\Update\DetailsRequest as UpdateDetailsRequest;
use App\Modules\V1\BusinessProfile\Services\Get\DetailsService as GetDetailsService;
use App\Modules\V1\BusinessProfile\Services\Update\DetailsService as UpdateDetailsService;
use App\Utils\CommonUtils;
use Illuminate\Http\JsonResponse;
use Throwable;

class BusinessProfileController extends Controller
{
    public function get(GetDetailsRequest $getDetailsRequest): JsonResponse
    {
        try {
            $getDetailsService = app(GetDetailsService::class);

            return response()->json(
                $getDetailsService->get(),
                HttpStatusConstant::OK,
            );
        } catch (Throwable $e) {
            report($e);

            return response()->json(
                CommonUtils::errorResponse($e->getMessage()),
                HttpStatusConstant::INTERNAL_SERVER_ERROR,
            );
        }
    }

    public function update(UpdateDetailsRequest $updateDetailsRequest): JsonResponse
    {
        try {
            $updateDetailsService = app(UpdateDetailsService::class);
            $updateDetailsBo = $updateDetailsService->prepareBo($updateDetailsRequest);

            return response()->json(
                $updateDetailsService->update($updateDetailsBo),
                HttpStatusConstant::OK,
            );
        } catch (Throwable $e) {
            report($e);

            return response()->json(
                CommonUtils::errorResponse($e->getMessage()),
                HttpStatusConstant::INTERNAL_SERVER_ERROR,
            );
        }
    }
}
