<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class Volunteer extends Model
{
    use HasFactory;

    protected $fillable = [
        'skills'
    ];

    protected $casts = [
        'skills' => 'json'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function listing()
    {
        return $this->belongsToMany(Listing::class , 'applications');
    }
}
