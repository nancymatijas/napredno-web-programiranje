<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'naziv_projekta',
        'opis_projekta',
        'cijena_projekta',
        'obavljeni_poslovi',
        'datum_pocetka',
        'datum_zavrsetka',
        'voditelj_id',
    ];

    public function voditelj()
    {
        return $this->belongsTo(User::class, 'voditelj_id');
    }
    
    public function members()
    {
        return $this->belongsToMany(User::class, 'project_user');
    }
}
