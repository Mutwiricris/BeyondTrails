<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ActivityReport extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['activity_id', 'reporter_id', 'reason', 'status'];
}
