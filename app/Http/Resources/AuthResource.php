<?php

namespace App\Http\Resources;

use App\User;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    // protected $collect = User::class;
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
            'type' => $this->type,
            'profile' => new ProfileResource($this->profile),
            $this->mergeWhen($request->get('withTimestamps') === true, [
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ]),
            'token' => $this->createToken(
                'UgmaToday Access Token',
                [User::TYPES[$this->type]]
            )->accessToken,
        ];
    }
}
