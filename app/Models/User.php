<?php

namespace App\Models;

use Carbon\Carbon;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail as ContractAuthMustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use App\CustomClasses\User as Authenticable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticable
{
    use Notifiable, HasApiTokens, SoftDeletes, HasFactory;

    protected $fillable = [
        'username', 'password', 'type'
    ];

    // protected $appends = [
    //     'profile'
    // ];

    protected $hidden = [
        'password',
    ];

    public const TYPES = [
        0 => 'user',
        1 => 'admin',
        'user' => 0,
        'admin' => 1,
    ];

    public const STATUS = [
        0 => 'BANNED',
        1 => 'ACTIVE',
        'ACTIVE' => 1,
        'BANNED' => 0,
    ];

    public const REGISTER_RULES = [
        'username' => 'required|unique:users|min:3|max:40',
        'password' => 'required|min:6|max:45|confirmed',
        'name' => 'required|min:2|max:50',
        'lastname' => 'required|min:2|max:50',
        'email' => 'required|unique:profiles|email:rfc'
    ];

    public const LOGIN_RULES = [
        'username' => 'required_without:email|exists:users',
        'email' => 'required_without:username|exists:profiles',
        'password' => 'required|min:6|max:45',
    ];

    public const PASSWORD_RESET_RULES = [
        'email' => 'required|exists:profiles',
    ];

    public const FILTER_RULES = [
        'type' => 'sometimes|required|numeric|between:0,1',
        'status' => 'sometimes|required|numeric|between:0,1',
        'withDeleted' => 'sometimes|boolean',
        'deletedOnly' => 'sometimes|boolean',
    ];

    public static function register_rules()
    {
        return [
            'username' => 'required|unique:users|min:3|max:40',
            'password' => ['required', 'min:6', 'max:45', 'confirmed', function ($attribute, $value, $fail) {
                if ($value === '123456' || $value === 'abcdef') {
                    $fail(trans('exception.weak_password'));
                }
            }],
            'name' => 'required|min:2|max:50',
            'lastname' => 'required|min:2|max:50',
            'email' => 'required|unique:profiles|email:rfc'
        ];
    }

    /**
     * Validates unique fields in database on update data
     * @param mixed $original_value
     * @param String $attribute
     * @param mixed $value
     * @param Closure $fail
     * @param Class $instance the model class where to find resource under evaluation
     *
     * @return void
     * */
    private static function validate_different_value($original_value, $attribute, $value, $fail, $instance)
    {
        $different_value = $original_value !== $value;

        if ($different_value) {
            $count_duplicate_value = $instance->where($attribute, $value)->count();

            if ($different_value && $count_duplicate_value > 0) {
                $fail(trans("exception.{$attribute}_used"));
            }
        }
    }

    public static function update_rules()
    {
        return [
            'old_password' => 'sometimes|min:6|max:45',
            'password' => 'sometimes|required_with:old_password|min:6|max:45',
            'name' => 'sometimes|min:2|max:50',
            'lastname' => 'sometimes|min:2|max:50',
            'username' => ['sometimes', 'min:3', 'max:40', function ($attribute, $value, $fail) {
                $original_value = Auth::user()->username;
                self::validate_different_value($original_value, $attribute, $value, $fail, new User());
            }],
            'email' => ['sometimes', 'email:rfc', function ($attribute, $value, $fail) {
                $original_value = Auth::user()->profile->email;
                self::validate_different_value($original_value, $attribute, $value, $fail, new Profile());
            }]
        ];
    }

    public static function reset_rules()
    {
        return [
            'token' => ['required', 'exists:password_resets', function ($attribute, $value, $fail) {
                if (Carbon::now() > optional(PasswordReset::where('token', $value)->first())->expire_at) {
                    $fail(trans('validation.expired'));
                }
            }],
            'password' => ['required', 'min:6', 'max:45']
        ];
    }

    /**********************************************
     *
     *          MUTATORS
     *
     *********************************************/

    public function getProfileImageAttribute()
    {
        return $this->file()->where('type', File::TYPES['PROFILE_IMAGE'])->first();
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    /**********************************************
     *
     *          RELATIONS
     *
     *********************************************/
    public function password_reset()
    {
        return $this->hasOne(PasswordReset::class);
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function made_reports()
    {
        return $this->hasMany(Report::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function file()
    {
        return $this->morphOne(File::class, 'fileable');
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }

    public function modified_costs()
    {
        return $this->hasMany(Cost::class, 'modifier_user_id');
    }

    public function modified_advices()
    {
        return $this->hasMany(Advice::class, 'modifier_user_id');
    }

    /**********************************************
     *
     *          SCOPES
     *
     *********************************************/
    public function scopeUser($query)
    {
        return $query->where('type', User::TYPES['user']);
    }

    public function scopeAdmin($query)
    {
        return $query->where('type', User::TYPES['admin']);
    }

    public function scopeActive($query)
    {
        return $query->where('status', User::STATUS['ACTIVE']);
    }

    public function scopeBanned($query)
    {
        return $query->where('status', User::STATUS['BANNED']);
    }

    /**
     * Search and return a user by username or email
     *
     * @param Request $data
     * @param bool $admin search only admins
     * @return User
     */
    public static function get_by_username_or_email(Request $request, bool $admin = false): User
    {
        return User::when($request->username, function ($query, $username) {
            $query->orWhere('users.username', $username);
        })
            ->when($request->email, function ($query, $email) {
                $query->join('profiles', 'users.id', 'profiles.user_id')->orWhere('profiles.email', $email)->select("users.*");
            })
            ->when($admin, function ($query) {
                $query->where('type', User::TYPES['ADMIN']);
            })
            ->first();
    }
}
