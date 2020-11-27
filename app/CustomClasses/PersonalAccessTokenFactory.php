<?php

namespace App\CustomClasses;

use Laravel\Passport\{Client, Passport, PersonalAccessTokenResult};
use Laravel\Passport\PersonalAccessTokenFactory as BasePassportPersonalAccessTokenFactory;
use Nyholm\Psr7\ServerRequest;

class PersonalAccessTokenFactory extends BasePassportPersonalAccessTokenFactory {
    public function makeTokens($user, $name, array $scopes = [], $password)
	{
		
		$response = $this->dispatchRequestToAuthorizationServer(
            $this->createTokenRequest(Client::where('password_client', true)->first(), $user, $scopes, $password)	
        );

        $token = tap($this->findAccessToken($response), function ($token) use ($user, $name) {
            $this->tokens->save($token->forceFill([
                'user_id' => $user->id,
                'name' => $name,
            ]));
        });

        return new PersonalAccessTokenResult(
            $response, $token
        );
	}

	/**
     * Create a request instance for the given client.
     *
     * @param  \Laravel\Passport\Client  $client
     * @param  mixed  $userId
     * @param  array  $scopes
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    protected function createTokenRequest($client, $user, array $scopes, $password)
	{
		return (new ServerRequest('POST', 'not-important'))->withParsedBody([
            'grant_type' => 'password',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
			'username' => $user->username,
			'password' => $password,
			'scope' => implode(' ', $scopes),
        ]);
    }
}
