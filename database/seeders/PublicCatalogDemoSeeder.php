<?php

namespace Database\Seeders;

use App\Models\AcademicProgram;
use App\Models\Course;
use App\Models\Event;
use App\Models\FileManager;
use App\Models\Program;
use App\Models\Scholarship;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PublicCatalogDemoSeeder extends Seeder
{
    /** Add clearly marked sample listings without changing accounts or roles. */
    public function run(): void
    {
        $countries = DB::table('countries')->where('status', STATUS_ACTIVE)->pluck('id', 'name');
        $universities = DB::table('universities')->where('status', STATUS_ACTIVE)->pluck('id', 'name');
        $subjects = DB::table('subjects')->where('status', STATUS_ACTIVE)->pluck('id', 'name');
        $levels = DB::table('study_levels')->where('status', STATUS_ACTIVE)->pluck('id', 'name');
        $scholarshipImageId = $this->storeDemoImage('destination-img-1.png');
        $courseImageId = $this->storeDemoImage('course-image-1.png');
        $eventImageId = $this->storeDemoImage('event-detail-1.png');

        // The earlier sample catalog rows were intentionally drafts; publish only those demo rows.
        AcademicProgram::query()->where('status', 'draft')->where('title', 'like', '%(Sample)%')
            ->update(['status' => 'published']);
        Subject::query()->where('name', 'like', '%(Sample)%')->update(['intake_time' => '2027-01-01']);

        $examples = [
            ['title' => 'Sample merit award for international students', 'country' => 'Canada', 'university' => 'University of Toronto', 'subject' => 'Computer Science (Sample)', 'level' => 'Undergraduate'],
            ['title' => 'Sample postgraduate study grant', 'country' => 'Australia', 'university' => 'University of Melbourne', 'subject' => 'Data Science (Sample)', 'level' => 'Postgraduate'],
            ['title' => 'Sample engineering bursary', 'country' => 'United Kingdom', 'university' => 'University of Manchester', 'subject' => 'Mechanical Engineering (Sample)', 'level' => 'Undergraduate'],
        ];

        foreach ($examples as $index => $example) {
            Scholarship::firstOrCreate(['slug' => Str::slug($example['title'])], [
                'title' => $example['title'],
                'details' => 'DEMO LISTING — illustrative sample only. Confirm current availability, eligibility, and deadlines with the university before applying.',
                'banner_image' => $scholarshipImageId,
                'country_id' => $countries[$example['country']] ?? null,
                'university_id' => $universities[$example['university']] ?? null,
                'subject_id' => $subjects[$example['subject']] ?? null,
                'study_level_id' => $levels[$example['level']] ?? null,
                'funding_type' => 1,
                'application_start_date' => now()->addDays(5 + ($index * 7)),
                'application_end_date' => now()->addMonths(5 + $index),
                'available_award_number' => 10,
                'eligibility_conditions' => 'Sample eligibility details; verify with the provider.',
                'status' => STATUS_ACTIVE,
            ]);
        }

        $courseProgram = Program::firstOrCreate(['slug' => 'genz-demo-learning'], [
            'title' => 'GenZ Sample Learning',
            'top_section' => [],
            'step_section' => [],
            'status' => STATUS_ACTIVE,
            'created_by' => DB::table('users')->where('role', USER_ROLE_ADMIN)->value('id'),
        ]);

        $adminId = DB::table('users')->where('role', USER_ROLE_ADMIN)->value('id');
        if ($adminId) {
            foreach ([
                ['title' => 'Study Abroad Application Basics (Demo)', 'duration' => 4],
                ['title' => 'Scholarship Search Essentials (Demo)', 'duration' => 3],
                ['title' => 'Pre-departure Planning (Demo)', 'duration' => 2],
            ] as $index => $item) {
                $slug = Str::slug($item['title']);
                Course::firstOrCreate(['slug' => $slug], [
                    'program_id' => $courseProgram->id,
                    'title' => $item['title'],
                    'subtitle' => 'Sample course content for local website demonstration.',
                    'thumbnail' => $courseImageId,
                    'duration' => $item['duration'],
                    'price' => 0,
                    'description' => 'DEMO CONTENT — this sample course is for preview only and is not an official course offer.',
                    'faqs' => '[]',
                    'instructors' => '[]',
                    'created_by' => $adminId,
                    'status' => STATUS_ACTIVE,
                    'publication_state' => 'published',
                    'difficulty' => 'beginner',
                    'language' => 'English',
                    'estimated_minutes' => [120, 90, 60][$index],
                ]);
            }
        }

        foreach ([
            ['slug' => 'genz-demo-open-day', 'title' => 'GenZ Study Abroad Open Day (Demo)', 'days' => 30],
            ['slug' => 'genz-demo-scholarship-workshop', 'title' => 'Scholarship Planning Workshop (Demo)', 'days' => 45],
            ['slug' => 'genz-demo-application-clinic', 'title' => 'University Application Clinic (Demo)', 'days' => 60],
        ] as $item) {
            Event::firstOrCreate(['slug' => $item['slug']], [
                'title' => $item['title'],
                'type' => EVENT_TYPE_VIRTUAL,
                'date_time' => now()->addDays($item['days']),
                'location' => null,
                'link' => url('/contact-us'),
                'description' => 'DEMO EVENT — sample listing for preview. Confirm the event details with GenZ before attending.',
                'price' => 0,
                'country_ids' => json_encode([]),
                'university_ids' => json_encode([]),
                'study_levels' => json_encode([]),
                'image' => $eventImageId,
                'status' => STATUS_ACTIVE,
            ]);
        }
    }

    private function publicImageId(): ?int
    {
        return FileManager::query()->orderBy('id')->get()->first(function (FileManager $file) {
            $disk = $file->storage_type === 'local' ? 'public' : $file->storage_type;
            return config("filesystems.disks.$disk") && Storage::disk($disk)->exists($file->path);
        })?->id;
    }

    private function storeDemoImage(string $fileName): ?int
    {
        $source = public_path('assets/images/' . $fileName);
        if (!is_file($source)) return $this->publicImageId();

        $path = 'uploads/demo-catalog/' . $fileName;
        Storage::disk('public')->put($path, file_get_contents($source));

        return FileManager::updateOrCreate(['path' => $path], [
            'file_type' => mime_content_type($source) ?: 'image/jpeg',
            'storage_type' => 'public',
            'original_name' => $fileName,
            'file_name' => $fileName,
            'user_id' => null,
            'extension' => pathinfo($fileName, PATHINFO_EXTENSION),
            'size' => filesize($source),
        ])->id;
    }
}
