<?php

namespace BaseCode\Auth\Resources;

use BaseCode\Auth\Resources\RoleResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->getEmail(),
            'username' => $this->getUsername(),
            'settings' => $this->getSettings(),
            'updatedAt' => $this->getUpdatedAt(),
            'roles' => new RoleResourceCollection($this->whenLoaded('roles')),
            'links' => [
                'self' => 'users/' . $this->getId(),
            ]
        ];
    }
}
