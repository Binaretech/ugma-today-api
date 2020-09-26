<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Cost;

class CostResource extends JsonResource
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
            "id" => $this->id,
            "name" => $this->name,
            "comment" => $this->comment,
            "price" => $this->price,
            "currency" => $this->currency,
            "currencyName" => Cost::CURRENCIES[$this->currency],
            $this->mergeWhen($request->get('withTimestamps') === true, [
                'createdAt' => $this->created_at,
                'updatedAt' => $this->updated_at,
            ]),
            "modifiedBy" => $this->whenLoaded('modified_by', function () {
                return new UserResource($this->modified_by->load('profile'));
            }),
        ];
    }
}
