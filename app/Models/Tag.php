<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Get thread list tagged with this tag.
     *
     * @return BelongsToMany
     */
    public function threads()
    {
        return $this->belongsToMany(Thread::class, 'thread_tag');
    }

    /**
     * Get tag by name or create, if not exist.
     *
     * @param string $name
     * @return mixed
     */
    public static function getOrCreate(string $name)
    {
        $tag = self::where('name', $name)->first();
        if (! $tag) {
            $tag = self::create(['name' => $name]);
        }

        return $tag;
    }
}
