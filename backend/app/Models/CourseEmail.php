<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseEmail extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'course_id',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
