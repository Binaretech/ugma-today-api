<?php

namespace Tests\Unit;

use App\User;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    public function test_user_relation()
    {
        $profile = factory(User::class)->create()->profile;

        $this->assertNotNull($profile);
        $this->assertDatabaseHas('profiles', ['user_id' => $profile->user_id]);
    }
}
