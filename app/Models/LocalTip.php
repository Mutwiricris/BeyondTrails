<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class LocalTip extends Model
{
    use HasUuids;

    protected $fillable = [
        'tippable_type', 'tippable_id',
        'title', 'description', 'icon', 'is_important', 'sort_order',
    ];

    protected $casts = ['is_important' => 'boolean'];
}
