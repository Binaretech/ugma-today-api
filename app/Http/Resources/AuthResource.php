<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
	private $tokens;
	
	/**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @return void
     */
    public function __construct($resource, $tokens)
    {
		$this->resource = $resource;
		$this->tokens = $tokens;
    }


    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
			'user ' => [
				'id' => $this->id,
				'username' => $this->username,
				'type' => $this->type,
				'profile' => new ProfileResource($this->profile),
					$this->mergeWhen($request->get('withTimestamps') === true, [
					'createdAt' => $this->created_at,
					'updatedAt' => $this->updated_at,
				]),
			],
			'access_token' => $this->tokens['access_token'],
			'expires_in' => $this->tokens['expires_in'],
			'refresh_token' => $this->tokens['refresh_token'],
			'type' => $this->tokens['token_type'],
        ];
    }
}
