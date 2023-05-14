<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Note>
 */
class NoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $file = UploadedFile::fake()->image(uniqid().'.jpg');
        $path = $file->storeAs('/images/notes', uniqid().'.jpg', 'public');

        return [
            'subject' => fake()->sentence(),
            'note' => fake()->text(500),
            'attachment' => $path,
        ];
    }
}
