<?php

namespace Tests\Unit;

use App\Advice;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class AdviceTest extends TestCase
{
    use RefreshDatabase;

    public function test_modifyed_by_relation()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user, ['admin']);

        $advice = factory(Advice::class)->create();
        $this->assertEquals($user->id, $advice->modified_by->id);
    }
}
