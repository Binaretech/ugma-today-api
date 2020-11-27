<?php

use Laravel\Passport\PersonalAccessTokenResult as BasePersonalAccessTokenResult;

class PersonalAcessTokenResult extends BasePersonalAccessTokenResult {
	
	/**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'tokens' => $this->accessToken,
            'token' => $this->token,
        ];
    }
}
