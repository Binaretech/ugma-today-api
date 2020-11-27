<?php

namespace App\CustomClasses;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use App\Models\Model;
use Illuminate\Container\Container;
use Laravel\Passport\HasApiTokens;

class User extends Model implements
  AuthenticatableContract,
  AuthorizableContract,
  CanResetPasswordContract
{
	use Authenticatable, Authorizable, CanResetPassword, MustVerifyEmail, HasApiTokens;

    /**
     * Create a new personal access token for the user.
     *
     * @param  string  $name
     * @param  array  $scopes
     * @return \Laravel\Passport\PersonalAccessTokenResult
     */
    public function createToken($name, array $scopes = [], $password)
    {
        return Container::getInstance()->make(PersonalAccessTokenFactory::class)->makeTokens(
            $this, $name, $scopes, $password
        );
    }


}
