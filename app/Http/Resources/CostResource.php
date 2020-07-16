<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

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
            $this->mergeWhen(optional(Auth::user())->tokenCan('admin'), [
                "modified_by" => new UserResource($this->modified_by->load('profile'))
            ]),
            "name" => $this->name,
            "comment" => $this->comment,
            "price" => $this->price,
            "currency" => $this->currency,
            $this->mergeWhen($request->get('withTimestamps') === true, [
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ])
        ];
    }
}
