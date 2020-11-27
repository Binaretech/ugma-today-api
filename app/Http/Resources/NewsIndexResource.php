<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NewsIndexResource extends JsonResource
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
			'title' => $this->title,
			'user' => new UserResource($this->user),
			'likes' => $this->likesCount,   
			'comments' => $this->commentsCount,
			$this->mergeWhen($request->get('withTimestamps') === true, [
                'createdAt' => $this->created_at,
                'updatedAt' => $this->updated_at,
            ]),	
		];
	}
}

