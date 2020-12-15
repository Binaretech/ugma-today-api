<?php

namespace App\Http\Resources;

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
            'id' => $this->id,
            'username' => $this->username,
            'status' => $this->status,
			'type' => $this->type,
			'profile' => new ProfileResource($this->whenLoaded('profile')),
			'profileImage' => optional($this->profile_image)->url,
            $this->mergeWhen($request->get('withTimestamps') === true, [
                'createdAt' => $this->created_at,
                'updatedAt' => $this->updated_at,
            ])
        ];
    }
}
