<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class CategoryResource extends JsonResource {

    public function toArray(Request $request) {
        return [
            'id' => $this->id,
            'translations' => $this->translations
        ];
    }

}
