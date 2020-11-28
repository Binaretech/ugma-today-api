<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
			'user' => new UserResource($this->user->load('profile')),
			'comment' => $this->comment,
			'replies' => $this->replies->count(),
			'likes' => $this->likes()->count(),
			'replies' => $this->replies()->count(),
			$this->mergeWhen($request->get('withTimestamps') === 'true', [
                'createdAt' => $this->created_at,
                'updatedAt' => $this->updated_at,
            ]),	
		];
    }
}
