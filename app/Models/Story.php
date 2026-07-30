<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'location_name',
        'emoji',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function frames()
    {
        return $this->hasMany(StoryFrame::class);
    }
}
