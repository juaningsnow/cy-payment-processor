<?php

namespace BaseCode\Common\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuditResource extends JsonResource
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
            'event' => strtoupper($this->event),
            'oldValues' => $this->old_values,
            'newValues' => $this->new_values,
            'ipAddress' => $this->ip_address,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'user' => $this->user ? $this->user : null,
        ];
    }
}
