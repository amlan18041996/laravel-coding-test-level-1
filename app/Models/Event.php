<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuids;

class Event extends Model
{
    use Uuids;
    use HasFactory;
    use SoftDeletes;

    public $table = 'events';
    protected $keyType = 'string';
    public $incrementing = false;

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = ['name', 'slug'];
    protected $dates = ['deleted_at'];
    protected $guarded = [];
}
