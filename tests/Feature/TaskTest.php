<?php

use App\Models\Note;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;

uses(WithFaker::class);

beforeEach(function () {
    $this->headerOptions = [
        'Accept' => 'application/json',
    ];
});

it('should return 401 if user not authenticated for create', function () {
    $task = Task::factory()
        ->hasNotes(3)
        ->raw();
    $this->postJson(route('task.store'), [$task], $this->headerOptions)->assertUnauthorized();
});

it('should return 422 if data is missing', function () {
    Sanctum::actingAs(
        User::factory()->create(),
        ['task.store']
    );
    $this->postJson(route('task.store'), [], $this->headerOptions)
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonMissingValidationErrors('subject', 'start_date', 'due_date', 'status', 'priority')
        ->assertJsonStructure([
            'message',
            'errors',
        ]);
});

it('should return task created succssfully', function () {
    Sanctum::actingAs(
        User::factory()->create(),
        ['task.store']
    );
    $task = Task::factory()->raw();
    $task['notes'] = Note::factory()->state([
        'attachment' => [UploadedFile::fake()->image(uniqid().'.jpg')],
    ])->count(3)->raw();

    $response = $this->postJson(route('task.store'), $task, $this->headerOptions)
        ->assertCreated()
        ->assertJsonStructure([
            'message',
            'data',
        ]);
});

it('should return 401 if user not authenticated for list', function () {
    $this->get(route('task.index'), $this->headerOptions)->assertUnauthorized();
});

it('should return list of tasks', function () {
    Sanctum::actingAs(
        User::factory()->create(),
        ['task.store']
    );
    $this->get(route('task.index'), $this->headerOptions)
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure([
            'message',
            'data',
        ]);
});
