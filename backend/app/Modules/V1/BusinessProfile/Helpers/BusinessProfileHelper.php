<?php

namespace App\Modules\V1\BusinessProfile\Helpers;

use App\Http\Requests\V1\BusinessProfile\Update\DetailsRequest as UpdateDetailsRequest;
use App\Modules\V1\BusinessProfile\Bo\Update\DetailsBo as UpdateDetailsBo;
use App\Repositories\DAO\V1\BusinessProfileDAO;
use Illuminate\Support\Collection;

class BusinessProfileHelper
{
    public function prepareUpdateBo(UpdateDetailsRequest $updateDetailsRequest): UpdateDetailsBo
    {
        $updateDetailsBo = new UpdateDetailsBo;

        $updateDetailsBo->setCompanyName($updateDetailsRequest->input('company_name'));
        $updateDetailsBo->setAddress($updateDetailsRequest->input('address'));
        $updateDetailsBo->setPhone($updateDetailsRequest->input('phone'));
        $updateDetailsBo->setTaxId($updateDetailsRequest->input('tax_id'));

        return $updateDetailsBo;
    }

    public function prepareUpdateDao(UpdateDetailsBo $updateDetailsBo, int $userId): BusinessProfileDAO
    {
        $businessProfileDao = new BusinessProfileDAO;

        $businessProfileDao->setUserId($userId);
        $businessProfileDao->setCompanyName($updateDetailsBo->getCompanyName());
        $businessProfileDao->setAddress($updateDetailsBo->getAddress());
        $businessProfileDao->setPhone($updateDetailsBo->getPhone());
        $businessProfileDao->setTaxId($updateDetailsBo->getTaxId());

        return $businessProfileDao;
    }

    public function format(Collection $businessProfile): array
    {
        return [
            'company_name' => $businessProfile->get('company_name'),
            'address' => $businessProfile->get('address'),
            'phone' => $businessProfile->get('phone'),
            'tax_id' => $businessProfile->get('tax_id'),
        ];
    }
}
