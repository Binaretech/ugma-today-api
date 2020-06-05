<?php

namespace Tests\Unit;

use App\Feedback;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_relation()
    {
        $report = factory(Feedback::class)->create([
            'user_id' => factory(User::class)->create(),
        ]);

        $this->assertNotNull($report->user);
    }
}
