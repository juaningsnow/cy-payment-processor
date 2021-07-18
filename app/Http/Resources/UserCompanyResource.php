<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserCompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'companyId' => $this->company_id,
            'company' => new CompanyResource($this->whenLoaded('company')),
            'isActive' => (bool) $this->is_active,
        ];
    }
}
