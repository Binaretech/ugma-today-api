<?php

namespace Tests\Unit;

use App\Models\Feedback;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FeedbackTest extends TestCase
{
    use DatabaseTransactions;

    public function test_user_relation()
    {
        $report = Feedback::factory()->create([
            'user_id' => User::factory()->active()->create(),
        ]);

        $this->assertNotNull($report->user);
    }
}
