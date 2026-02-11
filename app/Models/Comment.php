<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'post_id',
        'author_name',
        'body',
    ];

    /**
     * @return BelongsTo<Post, Comment>
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
