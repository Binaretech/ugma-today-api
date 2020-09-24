<?php

namespace Tests\Unit;

use App\Models\Advice;
use App\Models\Comment;
use App\Models\Cost;
use App\Models\Feedback;
use App\Models\File;
use App\Models\Like;
use App\Models\PasswordReset;
use App\Models\Post;
use App\Models\Report;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UserTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    /*----------------------------------------------*/
    /*                TEST RELATIONS                */
    /*----------------------------------------------*/

    public function test_password_reset_relation()
    {
        $user = User::factory()->active()->create();

        $token = base64_encode($user->id . password_hash(time() . rand(-99999, 99999), PASSWORD_DEFAULT) . uniqid());
        $expire = Carbon::now()->addHours(2);
        $user->password_reset()->save(new PasswordReset([
            'token' => $token,
            'expire_at' => $expire
        ]));

        $password_reset = $user->password_reset;

        $this->assertNotNull($password_reset);
        $this->assertEquals($token, $password_reset->token);
        $this->assertEquals($expire, $password_reset->expire_at);
    }

    public function test_profile_relation()
    {
        $user = User::factory()->active()->create();
        $this->assertNotNull($user->profile);
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_post_relation()
    {
        $user = User::factory()->active()->create();
        $user->posts()->saveMany(Post::factory()->times(10)->make([
            'id' => Post::generate_id($user->id),
        ]));

        $this->assertNotEmpty($user->posts);
        $this->assertCount(10, $user->posts);
    }

    public function test_comments_relation()
    {
        $user = User::factory()->active()->create();

        Post::factory()->create([
            'id' => Post::generate_id($user->id),
            'user_id' => User::factory()->active()->create(),
        ])->each(function (Post $post) use ($user) {
            $post->comments()->saveMany(Comment::factory()->times(10)->make([
                'user_id' => $user->id,
            ]));
        });

        $this->assertNotEmpty($user->comments);
        $this->assertCount(10, $user->comments);
    }

    public function test_likes_relation()
    {
        $user = User::factory()->active()->create();

        $post = Post::factory()
            ->create(['user_id' => User::factory()->active()->create()]);

        $post->likes()
            ->save(new Like([
                'user_id' => $user->id,
            ]));

        $this->assertNotEmpty($user->likes);
        $this->assertCount(1, $user->likes);
    }

    public function test_made_reports_relation()
    {
        $user = User::factory()->active()->create();

        Report::factory()->times(10)->make(['user_id' => $user->id])
            ->each(function (Report $report) {
                switch ($this->faker->numberBetween(0, 2)) {
                    case 0:
                        Post::factory()
                            ->create(['user_id' => User::factory()->active()->create()])
                            ->reports()->save($report);
                        break;
                    case 1:
                        Comment::factory()
                            ->create([
                                'user_id' => User::factory()->active()->create(),
                                'post_id' => Post::factory()
                                    ->create(['user_id' => User::factory()->active()->create()])
                            ])
                            ->reports()->save($report);
                        break;
                    default:
                        User::factory()->active()
                            ->create()
                            ->reports()->save($report);
                        break;
                }
            });


        $this->assertNotEmpty($user->made_reports);
        $this->assertCount(10, $user->made_reports);
    }

    public function test_reports_relation()
    {
        $user = User::factory()->active()->create();

        Report::factory()->times(10)->make(['user_id' => $user->id])
            ->each(function (Report $report) use ($user) {
                $user->reports()->save($report);
            });


        $this->assertNotEmpty($user->reports);
        $this->assertCount(10, $user->reports);
    }

    public function test_feedback_relation()
    {
        $user = User::factory()->active()->create();

        $user->feedback()->saveMany(Feedback::factory()->times(10)->create());

        $this->assertNotEmpty($user->feedback);
        $this->assertCount(10, $user->feedback);
    }

    public function test_file_relation()
    {
        $user = User::factory()->active()->create();

        $user->file()->save(File::factory()->make());

        $this->assertNotEmpty($user->file);
    }

    public function test_modified_cost_relation()
    {
        $user = User::factory()->active()->create();

        Passport::actingAs($user);

        Cost::factory()->create([
            'name' => $this->faker->randomElement(['Odontología', 'Ingeniería']),
            'modifier_user_id' => $user->id,
        ]);

        $this->assertNotEmpty($user->modified_costs);
    }

    public function test_modified_advice_relation()
    {
        $user = User::factory()->active()->create();

        Passport::actingAs($user);

        Advice::factory()->create([
            'modifier_user_id' => $user->id,
        ]);

        $this->assertNotEmpty($user->modified_advices);
    }

    /*----------------------------------------------*/
    /*          TEST ACCESSORS/MUTATORS             */
    /*----------------------------------------------*/

    public function test_password_mutator()
    {
        $user = User::factory()->active()->create(['password' => 'secret']);
        $this->assertNotEquals('secret', $user->password);
    }

    /*----------------------------------------------*/
    /*                TEST SCOPES                   */
    /*----------------------------------------------*/


    public function test_scopes()
    {
        User::factory()->times(5)->admin()->active()->create();

        User::factory()->times(20)->user()->active()->create();

        User::factory()->times(20)->user()->banned()->create();

        User::admin()->get()->each(function (User $user) {
            $this->assertEquals(User::TYPES['admin'], $user->type);
        });

        User::user()->get()->each(function (User $user) {
            $this->assertEquals(User::TYPES['user'], $user->type);
        });

        User::active()->get()->each(function (User $user) {
            $this->assertEquals(User::STATUS['ACTIVE'], $user->status);
        });

        User::banned()->get()->each(function (User $user) {
            $this->assertEquals(User::STATUS['BANNED'], $user->status);
        });
    }

    /*----------------------------------------------*/
    /*             TEST CUSTOM FUNCTIONS            */
    /*----------------------------------------------*/
    public function test_pagination_in_users()
    {
        User::factory()->user()->active()->times(20)->create();

        $users = User::paginate(10)->toArray();

        $this->assertArrayHasKey('data', $users);
        $this->assertArrayHasKey('ids', $users);

        $random_user_id = $users['ids'][0];
        $this->assertArrayHasKey($random_user_id, $users['data']);
    }
}
