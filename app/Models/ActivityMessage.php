<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ActivityMessage extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['activity_id', 'user_id', 'message', 'type', 'media_url'];

    protected $with = ['user'];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
