<?php

namespace Tests\Unit;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{

    use RefreshDatabase;

    public function test_profile_relation()
    {
        $user = factory(User::class)->create();

        $this->assertNotNull($user->profile);
        $this->assertDatabaseHas('user', $user);
    }
}
