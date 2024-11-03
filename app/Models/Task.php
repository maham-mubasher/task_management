<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'priority_id', 'title', 'description', 'due_date', 'completed_at', 'archived_at',
    ];

    protected $dates = [
        'due_date', 'completed_at', 'archived_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'task_tag');
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function markAsCompleted()
    {
        $this->completed_at = now();
        $this->save();
    }

    public function archive()
    {
        $this->archived_at = now();
        $this->save();
    }
}
