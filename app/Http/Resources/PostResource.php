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
		$comments = $this->comments()
			->whereNull('reply_to_id')
			->orderBy('created_at', 'ASC')
			->paginate($request->pagination ?? 10);

		return [
			'id' => $this->id,
			'title' => $this->title,
			'content' => $this->content,
			'type' => $this->type,
			'user' => new UserResource($this->user),
			'likedByUser' => $this->likedByUser,
			'likesCount' => $this->likesCount,
			'commentsCount' => $this->CommentsCount,
			'comments' => CommentResource::collection($comments)->resource,
			'replies' => CommentResource::collection($this->comments()
				->whereIn('reply_to_id', $comments->pluck('id'))->get()->keyBy('id'))->resource,
			$this->mergeWhen($request->get('withTimestamps') === 'true', [
				'createdAt' => $this->created_at,
				'updatedAt' => $this->updated_at,
			]),
		];
	}
}
