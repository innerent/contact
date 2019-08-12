<?php

namespace Innerent\Contact\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Email extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'description',
        'address',
        'type',
        'provider',
    ];
}
