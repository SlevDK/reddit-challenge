<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Thread extends Model
{
    use HasFactory, SoftDeletes;

    /** @var int Thread statuses */
    const THREAD_STATUS_OPENED = 1;
    const THREAD_STATUS_CLOSED = 2;

    protected $guarded = ['id'];

    /**
     * Comments in thread.
     *
     * @return HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'thread_id');
    }

    /**
     * Thread author.
     *
     * @return BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Thread board.
     *
     * @return BelongsTo
     */
    public function board()
    {
        return $this->belongsTo(Board::class, 'board_id');
    }

    /**
     * Current thread tags.
     *
     * @return BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'thread_tag');
    }

    /**
     * Mark thread as opened.
     */
    public function openThread(): bool
    {
        return $this->update(['status' => self::THREAD_STATUS_OPENED]);
    }

    /**
     * Mark thread as closed.
     */
    public function closeThread(): bool
    {
        return $this->update(['status' => self::THREAD_STATUS_CLOSED]);
    }

    /**
     * Determine is current thread opened.
     *
     * @return bool
     */
    public function isOpened()
    {
        return $this->status == self::THREAD_STATUS_OPENED;
    }

    /**
     *
     * Determine is provided user thread author.
     * @param User $user
     * @return bool
     */
    public function isAuthor(User $user)
    {
        return $this->author_id == $user->id;
    }

    /**
     * Add new tag to this thread.
     *
     * @param Tag $tag
     * @return bool
     */
    public function addTag(Tag $tag)
    {
        return DB::table('thread_tag')->updateOrInsert([
            'tag_id' => $tag->id,
            'thread_id' => $this->id,
        ]);
    }

    /**
     * Create new thread.
     *
     * @param array $attributes
     * @param Board $board
     * @param User|null $user
     * @return mixed
     */
    public static function make(array $attributes, Board $board, ?User $user = null)
    {
        $attributes['status'] = self::THREAD_STATUS_OPENED;
        $attributes['board_id'] = $board->id;
        $attributes['author_id'] = ($user) ? $user->id : auth()->id();

        return self::create($attributes);
    }
}
