<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = ['task_id', 'file_path', 'file_type'];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
