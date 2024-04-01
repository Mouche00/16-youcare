<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'date',
        'location',
        'competences',
        'organizer_id'
    ];
    protected $casts = [
        'competences' => 'json'
    ];
    public function organizer() {
        return $this->belongsTo(Organizer::class , 'organizer_id');
    }
    public function volunteer(){
        return $this->belongsToMany(Volunteer::class , 'applications');
    } 
}
