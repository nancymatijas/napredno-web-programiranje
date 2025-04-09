<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'naziv_rada',
        'naziv_rada_engleski',
        'zadatak_rada',
        'tip_studija',
        'nastavnik_id',
    ];

    public function students()
    {
        return $this->belongsToMany(User::class, 'student_task', 'task_id', 'student_id')
                    ->withPivot(['status', 'priority']); 
    }

}
