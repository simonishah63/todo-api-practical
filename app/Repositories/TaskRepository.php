<?php

namespace App\Repositories;

use App\Enums\TaskPriority;
use App\Interfaces\TaskInterface;
use App\Models\Note;
use App\Models\Task;
use Illuminate\Support\Str;

class TaskRepository implements TaskInterface
{
    /**
     * Get Searchable Task Data
     *
     * @return collections Array of Tasks
     */
    public function search(array $request)
    {
        $query = Task::query();
        if (! empty($request['status'])) {
            $query->where('status', $request['status']);
        }
        if (! empty($request['due_date'])) {
            $query->whereDate('due_date', $request['due_date']);
        }
        if (! empty($request['priority'])) {
            $query->where('priority', $request['priority']);
        }
        if (! empty($request['notes']) && $request['notes']) {
            $query->whereHas('notes');
        }
        $priorityString = implode('","', TaskPriority::values());
        $query->withCount('notes')->orderByRaw('FIELD(priority, "'.$priorityString.'")')->orderBy('notes_count', 'DESC');

        return $query->get();
    }

    /**
     * Create New Task.
     *
     * @return object Task Object
     */
    public function create(array $data): Task
    {
        $task = Task::create($data);
        if (! empty($data['notes'])) {
            $notes = collect($data['notes'])
                ->map(function (array $note) {
                    $attachment = [];
                    $subjectShort = Str::slug(substr($note['subject'], 0, 20));
                    if (! empty($note['attachment'])) {
                        foreach ($note['attachment'] as $image) {
                            if ($image->isValid()) {
                                $fileName = $subjectShort.'-'.time().'.'.$image->getClientOriginalExtension();
                                $attachment[] = $image->storeAs('/images/notes', $fileName, 'public');
                            }
                        }
                    }

                    return new Note([
                        'subject' => $note['subject'],
                        'note' => $note['note'] ?? null,
                        'attachment' => (count($attachment) > 0) ? implode(',', $attachment) : null,
                    ]);
                });

            $task->notes()->saveMany($notes);
        }

        return $task;
    }
}
