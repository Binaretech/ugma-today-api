<?php

namespace Tests\Unit;

use App\Models\Advice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class AdviceTest extends TestCase
{
    use RefreshDatabase;

    public function test_modifyed_by_relation()
    {
        $user = User::factory()->active()->create();
        Passport::actingAs($user, ['admin']);

        $advice = Advice::factory()->create();
        $this->assertEquals($user->id, $advice->modified_by->id);
    }
}
