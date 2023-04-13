<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "tasks";
    protected $fillable = ["name", "description", "done", "project_id", "priority"];

    protected $casts = [
        "done" => "bool"
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, "id", "project_id");
    }

}
