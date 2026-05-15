<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CategoryResource;
use Illuminate\Support\Facades\Storage;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'price'       => $this->price,
            'stock'       => $this->stock,
            'discount'    => $this->discount,
            'status'      => $this->status,
            'image'       => $this->image ? url(Storage::url($this->image)) : null,
            // include the related category when it has been loaded
            'category_id'    => $this->whenLoaded('category', function () {
                return $this->category ? [
                    'name' => $this->category->name,
                ] : null;
            }),
        ];
    }
}
