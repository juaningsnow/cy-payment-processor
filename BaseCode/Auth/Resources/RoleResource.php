<?php

namespace BaseCode\Auth\Resources;

use BaseCode\Auth\Resources\PermissionResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use BaseCode\Auth\Resources\UserResource;

class RoleResource extends JsonResource
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
            'id' => $this->getId(),
            'name' => $this->getName(),
            'permissionsIds' => $this->getPermissionIds(),
            'permissions' => new PermissionResourceCollection($this->whenLoaded('permissions')),
            'updatedAt' => $this->getUpdatedAt(),
            'links' => [
                'self' => "roles/{$this->getId()}"
            ]
        ];
    }
}
