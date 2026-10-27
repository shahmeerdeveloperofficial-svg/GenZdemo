<?php

namespace Database\Seeders;

use App\Models\FileManager;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DemoStudentPortraitSeeder extends Seeder
{
    /** Attach synthetic portraits only to the fictional .example student accounts. */
    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command?->error('Demo student portraits are disabled in production.');
            return;
        }

        $students = [
            'demo.learner.amina@genzconsultants.example' => 'amina',
            'demo.learner.omar@genzconsultants.example' => 'omar',
            'demo.learner.noor@genzconsultants.example' => 'noor',
            'demo.learner.hassan@genzconsultants.example' => 'hassan',
            'demo.learner.sara@genzconsultants.example' => 'sara',
            'demo.learner.zain@genzconsultants.example' => 'zain',
            'demo.learner.maryam@genzconsultants.example' => 'maryam',
            'demo.learner.bilal@genzconsultants.example' => 'bilal',
        ];

        foreach ($students as $email => $slug) {
            $student = User::query()
                ->where('email', $email)
                ->where('role', USER_ROLE_STUDENT)
                ->first();

            $source = database_path("seeders/assets/students/{$slug}.jpg");
            if (!$student || $student->image || !is_file($source)) {
                continue;
            }

            $fileName = "demo-student-{$slug}.jpg";
            $path = "uploads/demo-students/{$fileName}";
            Storage::disk('public')->put($path, file_get_contents($source));

            $file = FileManager::updateOrCreate(['file_name' => $fileName], [
                'file_type' => 'image/jpeg',
                'storage_type' => 'public',
                'original_name' => "{$slug}.jpg",
                'user_id' => null,
                'path' => $path,
                'extension' => 'jpg',
                'size' => (string) filesize($source),
            ]);

            $student->image = $file->id;
            $student->save();
        }
    }
}
