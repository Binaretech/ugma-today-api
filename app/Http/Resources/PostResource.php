<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
			'content' => $this->content,
			'type' => $this->type,
			'user' => new UserResource($this->user),
			'likedByUser' => $this->likedByUser,
			'likesCount' => $this->likesCount,
			'commentsCount' => $this->CommentsCount,
			'comments' => CommentResource::collection($this->comments()->whereNull('reply_to_id')->paginate($request->pagination??10)),
			$this->mergeWhen($request->get('withTimestamps') === 'true', [
                'createdAt' => $this->created_at,
                'updatedAt' => $this->updated_at,
            ]),	
		];
	}
}

