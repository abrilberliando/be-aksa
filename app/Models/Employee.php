<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasUuids;

    protected $fillable = ['division_id', 'image', 'name', 'phone', 'position'];

    public function division()
    {
        return $this->belongsTo(Division::class);
    }
}
