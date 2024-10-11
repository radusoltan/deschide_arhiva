<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'locale' => app()->getLocale(),
            'title' => $this->title,
            'category' => new CategoryResource($this->category),
            'slug' => $this->slug,
            'lead' => $this->lead,
            'body' => $this->body,
            'images' => ImageResource::collection($this->images),
            'authors' => AuthorResource::collection($this->authors)
        ];
    }
}
