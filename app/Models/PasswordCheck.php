<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordCheck extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip_address',
        'breached',
        'breach_count',
    ];
}
