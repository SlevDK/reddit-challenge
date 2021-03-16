<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Board extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    const ROLE_MODERATOR = 'moderator';
    const ROLE_MEMBER = 'member';
    const ROLE_BANNED = 'banned';

    /**
     * Threads in board.
     *
     * @return HasMany
     */
    public function threads()
    {
        return $this->hasMany(Thread::class, 'board_id');
    }

    /**
     * Board creator.
     *
     * @return BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Users that joined to the board.
     *
     * @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_board_role')
            ->withPivot('role');
    }

    /**
     * Update user's role in current board.
     *
     * @param User $user
     * @param string $role
     * @return bool
     */
    public function updateRole(User $user, string $role)
    {
        $role = in_array($role, [self::ROLE_MODERATOR, self::ROLE_MEMBER, self::ROLE_BANNED])
            ? $role : self::ROLE_MEMBER;

        return DB::table('user_board_role')->updateOrInsert([
            'user_id' => $user->id,
            'board_id' => $this->id,
        ], ['role' => $role]);
    }

    /**
     * Determine is provided user has moderator role in current board.
     *
     * @param User $user
     * @return bool
     */
    public function isModerator(User $user)
    {
        $role = $this->getUserRole($user);

        return $role && $role == self::ROLE_MODERATOR;
    }

    /**
     * Determine is provided user has member role in current board.
     *
     * @param User $user
     * @return bool
     */
    public function isMember(User $user)
    {
        $role = $this->getUserRole($user);

        return $role && $role == self::ROLE_MEMBER;
    }

    /**
     * Determine is provided user was banned in current board.
     *
     * @param User $user
     * @return bool
     */
    public function isBanned(User $user)
    {
        $role = $this->getUserRole($user);

        return $role && $role == self::ROLE_BANNED;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function getUserRole(User $user)
    {
        $pivot = DB::table('user_board_role')->where([
            'user_id' => $user->id,
            'board_id' => $this->id,
        ])->first();

        return ($pivot) ? $pivot->role : null;
    }

    /**
     * Determine how many opened threads in current board
     * was created by provided user.
     *
     * @param User $user
     * @return mixed
     */
    public function openedThreadsCount(User $user)
    {
        return Thread::where('author_id', $user->id)
            ->where('board_id', $this->id)
            ->where('status', Thread::THREAD_STATUS_OPENED)
            ->count();
    }

    /**
     * Create new board and attach creator as moderator.
     *
     * @param array $attributes
     * @param User|null $user
     * @return mixed
     */
    public static function make(array $attributes = [], ?User $user = null)
    {
        $user = ($user) ? $user : auth()->user();
        $attributes['owner_id'] = $user->id;
        $attributes['slug'] = self::makeSlugFromName($attributes['name']);

        $board = self::create($attributes);
        $board->users()->attach($user, ['role' => self::ROLE_MODERATOR]);

        return $board;
    }

    /**
     * Generate slug from provided name string.
     *
     * @param string $name
     * @return false|string
     */
    public static function makeSlugFromName(string $name)
    {
        $name = explode(' ', $name);
        $slug = '';

        foreach($name as $word) {
            $slug .= lcfirst($word).'-';
        }

        return substr($slug, 0, strlen($slug) - 1);
    }
}
