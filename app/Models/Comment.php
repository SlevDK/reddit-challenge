<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    /**
     * Comment author.
     *
     * @return BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Comment thread.
     *
     * @return BelongsTo
     */
    public function thread()
    {
        return $this->belongsTo(Thread::class, 'thread_id');
    }

    /**
     * Create new comment.
     *
     * @param array $attributes
     * @param Thread $thread
     * @param User|null $user
     * @return mixed
     */
    public static function make(array $attributes, Thread $thread, ?User $user = null)
    {
        $attributes['thread_id'] = $thread->id;
        $attributes['author_id'] = ($user) ? $user->id : auth()->id();

        return self::create($attributes);
    }
}
