<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'path' => $this->path,
            'width' => $this->width,
            'height' => $this->height,
            'is_main' => (bool) $this->whenPivotLoaded('article_images', function () {
                return $this->pivot->is_main;
            }),
        ];
    }
}
