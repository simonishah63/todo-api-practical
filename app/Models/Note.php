<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 *  @OA\Schema(
 *      required={"subject"},
 *
 *      @OA\Xml(name="Note"),
 *
 *      @OA\Property(property="id", type="integer", readOnly="true", example="1"),
 *      @OA\Property(property="subject", type="string", maxLength=255, description="Note Subject", example="Note 1"),
 *      @OA\Property(property="note", type="text", maxLength=5000, description="Note Description", example="Description of the note"),
 * 		@OA\Property(property="attachment", type="array", description="Note Attachment",
 *
 *          @OA\Items(
 *              type="string", description="Attachment Url", example="image.png"
 *          ),
 *      ),
 *
 * 		@OA\Property(property="created_at", type="string", format="date-time", description="Initial creation timestamp", readOnly="true"),
 * 		@OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp", readOnly="true"),
 * )
 */
class Note extends Model
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
        'task_uuid',
        'subject',
        'note',
        'attachment',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_uuid');
    }
}
