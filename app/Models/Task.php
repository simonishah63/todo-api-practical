<?php

namespace App\Models;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 *  @OA\Schema(
 *      required={"subject", "start_date", "due_date", "status", "priority"},
 *
 *      @OA\Xml(name="Task"),
 *
 *      @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 *      @OA\Property(property="subject", type="string", maxLength=255, description="Task Subject", example="Task 1"),
 *      @OA\Property(property="description", type="text", maxLength=5000, description="Task Description", example="Description of the task"),
 * 		@OA\Property(property="start_date", type="date", format="date", description="Date of task start", example="2023-05-14"),
 * 		@OA\Property(property="due_date", type="date", format="date", description="Date of task end", example="2023-05-30"),
 * 		@OA\Property(property="status", type="string", enum=\App\Enums\TaskStatus::class, description="Task status", example="New"),
 * 		@OA\Property(property="priority", type="string", enum=\App\Enums\TaskPriority::class, description="Task priority", example="High"),
 * 		@OA\Property(property="created_at", type="string", format="date-time", description="Initial creation timestamp", readOnly="true"),
 * 		@OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp", readOnly="true"),
 * )
 */
class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = Str::uuid()->toString();
        });
    }

    protected $fillable = [
        'uuid',
        'subject',
        'description',
        'start_date',
        'due_date',
        'status',
        'priority',
    ];

    protected $casts = [
        'status' => TaskStatus::class,
        'priority' => TaskPriority::class,
    ];

    public function notes()
    {
        return $this->hasMany(Note::class, 'task_uuid', 'uuid');
    }

    public function hasNotes(): bool
    {
        return $this->notes()->exists();
    }
}
