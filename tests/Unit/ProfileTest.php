<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    public function test_user_relation()
    {
        $profile = User::factory()->active()->create()->profile;

        $this->assertNotNull($profile);
        $this->assertDatabaseHas('profiles', ['user_id' => $profile->user_id]);
    }
}
