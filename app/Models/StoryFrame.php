<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoryFrame extends Model
{
    use HasFactory;

    protected $fillable = [
        'story_id',
        'image_url',
        'caption',
        'duration_seconds',
    ];

    public function story()
    {
        return $this->belongsTo(Story::class);
    }
}
