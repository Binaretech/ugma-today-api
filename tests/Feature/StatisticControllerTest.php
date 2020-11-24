<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class StatisticControllerTest extends TestCase
{
	use DatabaseTransactions;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_index()
	{
		Passport::actingAs(User::factory()->create(), ['admin']);

		User::factory()->times(10)->create()->each(function($user) {
			$user->posts()->save(Post::factory()->make());
		});

		$this->get('/api/admin/summary')
			->assertOk()
			->assertJsonStructure([
				'data' => [
					[
						'name',
						'count'
					]
				]			
			]);
    }
}
