<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name' => $this->name,
            'category' => $this->category,
            'description' => $this->description,
            'date_and_time' => $this->date_and_time,
            'files' => $this->media()->exists() ? MediaResource::collection($this->getMedia()) : [],
            'editUrl' => "/products/{$this->id}/edit"
        ];
    }
}
