<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
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
            'fileName' => $this->file_name,
            'path' => $this->getPath(),
            'fullUrl' => $this->getFullUrl(),
            'downloadUrl' => route('media_download', [$this->id]),
        ];
    }
}
