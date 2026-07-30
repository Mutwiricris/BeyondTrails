<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'description',
        'points_reward',
        'icon',
        'difficulty',
        'target_value',
        'type',
    ];

    public function userChallenges()
    {
        return $this->hasMany(UserChallenge::class);
    }
}
