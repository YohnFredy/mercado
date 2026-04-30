<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'name',
        'division_term_1',
        'division_term_2',
        'division_term_3',
    ];
}
