<?php

namespace Tests\Unit;

use App\Models\Feedback;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_relation()
    {
        $report = Feedback::factory()->create([
            'user_id' => User::factory()->active()->create(),
        ]);

        $this->assertNotNull($report->user);
    }
}
