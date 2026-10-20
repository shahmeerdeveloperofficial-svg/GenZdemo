<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Appointment;
use App\Models\CrmLead;
use App\Models\CrmFollowUp;
use App\Models\StudyAbroadRegistration;
use App\Models\User;
use App\Services\ConsultationBookingService;
use App\Services\LmsEnrollmentService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GenZDemoSeeder extends Seeder
{
    private array $demoCredentials = [];

    public function run(): void
    {
        if (!(app()->environment('local') || config('genz.demo_seed_enabled'))) {
            $this->command?->error('Demo seeding requires an explicit non-production environment or GENZ_DEMO_SEEDING opt-in.');
            return;
        }
        if (DB::connection()->getDatabaseName() !== config('genz.demo_database')) {
            $this->command?->error('Demo seeding is restricted to the isolated configured demo database. No records were changed.');
            return;
        }
        $this->seedDemoBootstrap();

        $countries=[];
        if (Schema::hasTable('countries')) foreach ([['Demo Canada','demo-canada','CA'],['Demo United Kingdom','demo-united-kingdom','GB'],['Demo Australia','demo-australia','AU']] as [$name,$slug,$code]) {
            $values=['name'=>$name,'status'=>STATUS_ACTIVE,'updated_at'=>now()];
            if(Schema::hasColumn('countries','country_code')) $values['country_code']=$code;
            if(Schema::hasColumn('countries','currency_code')) $values['currency_code']=['CA'=>'CAD','GB'=>'GBP','AU'=>'AUD'][$code];
            DB::table('countries')->updateOrInsert(['slug'=>$slug],$values+['created_at'=>now()]);
            $countries[$slug]=DB::table('countries')->where('slug',$slug)->value('id');
        }

        $universities=[];
        if(Schema::hasTable('universities')) foreach ([['Demo North University','demo-north-university','demo-canada'],['Demo Thames Institute','demo-thames-institute','demo-united-kingdom'],['Demo Southern Coast University','demo-southern-coast-university','demo-australia']] as [$name,$slug,$countrySlug]) {
            $values=['name'=>$name,'country_id'=>(string)($countries[$countrySlug]??''),'avg_cost'=>0,'status'=>STATUS_ACTIVE,'updated_at'=>now()];
            if(Schema::hasColumn('universities','city')) $values['city']='Demo city';
            if(Schema::hasColumn('universities','tuition_currency')) $values['tuition_currency']=null;
            DB::table('universities')->updateOrInsert(['slug'=>$slug],$values+['created_at'=>now()]);
            $universities[$slug]=DB::table('universities')->where('slug',$slug)->value('id');
        }

        if (Schema::hasTable('crm_leads')) foreach ([
            ['Ayesha Khan', 'new', 'website'], ['Bilal Ahmed', 'contacted', 'referral'],
            ['Zara Malik', 'qualified', 'consultation'], ['Hassan Raza', 'consultation', 'social'],
            ['Sara Noor', 'converted', 'website'], ['Zain Qureshi', 'lost', 'event'],
            ['Maryam Ali', 'new', 'whatsapp'], ['Omar Farooq', 'contacted', 'partner'],
        ] as $index => [$name, $status, $source]) {
            [$first, $last] = array_pad(explode(' ', $name, 2), 2, 'Demo');
            $email = 'demo.lead.'.($index + 1).'@genzconsultants.example';
            CrmLead::updateOrCreate(['email' => $email], [
                'first_name' => $first, 'last_name' => $last, 'status' => $status, 'source' => $source,
                'assigned_to' => User::where('email', 'demo.counselor@genzconsultants.example')->value('id'),
            ]);
        }

        if(Schema::hasTable('academic_programs') && count($universities)>=3) foreach ([
            ['Demo Data Science MSc','demo-data-science-msc','demo-north-university','demo-canada'],
            ['Demo Global Business BA','demo-global-business-ba','demo-thames-institute','demo-united-kingdom'],
            ['Demo Sustainable Engineering MSc','demo-sustainable-engineering-msc','demo-southern-coast-university','demo-australia'],
        ] as [$title,$slug,$universitySlug,$countrySlug]) {
            DB::table('academic_programs')->updateOrInsert(['slug'=>$slug],['university_id'=>$universities[$universitySlug],'country_id'=>$countries[$countrySlug],'title'=>$title,'status'=>'published','description'=>'Fictional demo catalog record for local evaluation. Verify all real admissions details with the institution.','updated_at'=>now(),'created_at'=>now()]);
        }
        if(Schema::hasTable('scholarships')) foreach ([['Demo Merit Award','demo-merit-award'],['Demo Global Learner Grant','demo-global-learner-grant'],['Demo Pathways Award','demo-pathways-award']] as [$title,$slug]) DB::table('scholarships')->updateOrInsert(['slug'=>$slug],['title'=>$title,'details'=>'Fictional sample scholarship record. Not a real funding offer. Confirm eligibility and availability with the provider.','status'=>STATUS_ACTIVE,'updated_at'=>now(),'created_at'=>now()]);

        $this->seedDemoPublicContent();

        if(Schema::hasTable('users') && Schema::hasTable('courses') && Schema::hasTable('programs')) $this->seedLearningScenarios();
        $this->seedDashboardWorkflowScenarios();
        $this->seedExpandedWorkspaceDemos();
        $this->persistDemoCredentials();
    }

    private function demoUser(string $email, array $attributes): User
    {
        $password = Str::random(48);
        $user = User::firstOrCreate(['email' => $email], $attributes + ['password' => Hash::make($password)]);

        if ($user->wasRecentlyCreated) {
            $this->demoCredentials[$email] = $password;
        }

        return $user;
    }

    private function persistDemoCredentials(): void
    {
        if (!$this->demoCredentials || app()->environment('testing')) {
            return;
        }

        $path = storage_path('app/private/genz-demo-credentials.json');
        File::ensureDirectoryExists(dirname($path), 0700, true);
        $existing = File::exists($path) ? json_decode(File::get($path), true) : [];
        $accounts = is_array($existing['accounts'] ?? null) ? $existing['accounts'] : [];
        File::put($path, json_encode([
            'warning' => 'One-time synthetic demo credentials. Change them before handoff and keep this file private.',
            'accounts' => array_merge($accounts, $this->demoCredentials),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        @chmod($path, 0600);
        @chmod(dirname($path), 0700);
        $this->command?->info('One-time demo credentials were saved outside the public web root to storage/app/private/genz-demo-credentials.json.');
    }

    private function seedDemoPublicContent(): void
    {
        $studyLevels = [];
        if (Schema::hasTable('study_levels')) {
            foreach (['Demo Undergraduate', 'Demo Postgraduate', 'Demo English Preparation'] as $name) {
                $values = ['status' => STATUS_ACTIVE, 'updated_at' => now()];
                if (Schema::hasColumn('study_levels', 'deleted_at')) $values['deleted_at'] = null;
                DB::table('study_levels')->updateOrInsert(['name' => $name], $values + ['created_at' => now()]);
                $studyLevels[$name] = DB::table('study_levels')->where('name', $name)->value('id');
            }
        }

        if (Schema::hasTable('subject_categories')) {
            foreach (['Language Preparation', 'Academic Readiness', 'Digital Skills', 'Career Development'] as $name) {
                $values = ['status' => STATUS_ACTIVE, 'updated_at' => now()];
                if (Schema::hasColumn('subject_categories', 'deleted_at')) $values['deleted_at'] = null;
                DB::table('subject_categories')->updateOrInsert(['name' => $name], $values + ['created_at' => now()]);
            }
        }

        if (Schema::hasTable('events')) {
            foreach ([
                ['GenZ Demo: Study in Canada Q&A', 'demo-study-in-canada-qa', 14],
                ['GenZ Demo: IELTS Planning Workshop', 'demo-ielts-planning-workshop', 24],
                ['GenZ Demo: Scholarship Readiness Session', 'demo-scholarship-readiness-session', 35],
            ] as [$title, $slug, $daysAhead]) {
                DB::table('events')->updateOrInsert(['slug' => $slug], [
                    'title' => $title, 'type' => EVENT_TYPE_VIRTUAL,
                    'date_time' => now()->addDays($daysAhead)->setTime(15, 0),
                    'location' => 'Online — fictional demo event', 'link' => null,
                    'description' => 'Synthetic local demonstration event. This is not a real booking or official university event.',
                    'price' => 0, 'status' => STATUS_ACTIVE, 'updated_at' => now(), 'created_at' => now(),
                ]);
            }
        }

        if (Schema::hasTable('services')) {
            foreach ([
                ['Demo Profile Review', 'demo-profile-review', 'A fictional sample service for testing the GenZ workspace.', 8000],
                ['Demo Application Planning', 'demo-application-planning', 'A sample study-abroad application planning service. Not a live offer.', 45000],
                ['Demo English Test Guidance', 'demo-english-test-guidance', 'A synthetic IELTS preparation guidance service for local evaluation.', 12000],
            ] as [$title, $slug, $description, $demoPrice]) {
                $values = [
                    'title' => $title, 'description' => $description, 'faq' => null, 'feature' => json_encode([]),
                    'price' => $demoPrice, 'icon' => 'graduation-cap', 'image' => null, 'status' => STATUS_ACTIVE,
                    'updated_at' => now(), 'created_at' => now(),
                ];
                if (Schema::hasColumn('services', 'deleted_at')) $values['deleted_at'] = null;
                DB::table('services')->updateOrInsert(['slug' => $slug], $values);
            }
        }

        if (Schema::hasTable('free_consultations') && $studyLevels && Schema::hasTable('countries')) {
            $countryId = DB::table('countries')->where('slug', 'demo-canada')->value('id');
            foreach ([
                ['Demo Intake', 'Ayesha Khan', 'demo.request.1@genzconsultants.example', 'Demo Undergraduate'],
                ['Demo Intake', 'Bilal Ahmed', 'demo.request.2@genzconsultants.example', 'Demo Postgraduate'],
                ['Demo Intake', 'Sara Noor', 'demo.request.3@genzconsultants.example', 'Demo English Preparation'],
            ] as $index => [$firstName, $lastName, $email, $levelName]) {
                DB::table('free_consultations')->updateOrInsert(['email' => $email], [
                    'user_id' => null, 'first_name' => $firstName, 'last_name' => $lastName,
                    'mobile' => '+92 300 000 00'.str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                    'fund_type' => FUND_TYPE_SELF, 'consultation_type' => CONSULTATION_TYPE_VIRTUAL,
                    'country_ids' => json_encode([$countryId]), 'study_level_id' => $studyLevels[$levelName],
                    'status' => STATUS_PENDING, 'deleted_at' => null, 'updated_at' => now(), 'created_at' => now(),
                ]);
            }
        }
    }

    private function seedDemoBootstrap(): void
    {
        if (Schema::hasTable('settings')) foreach ([
            'build_version'=>(string)config('app.build_version'),'current_version'=>(string)config('app.current_version'),
            'app_name'=>'GenZ Consultants','app_email'=>config('genz.contact.email'),'app_contact_number'=>config('genz.contact.phone'),
            'app_location'=>config('genz.contact.address'),'app_timezone'=>'Asia/Karachi','app_footer_text'=>config('genz.tagline'),
            'hero_banner_status'=>'1','banner_title'=>'Your study abroad journey starts here.','banner_description'=>'Explore destinations, prepare your application, and learn with GenZ Consultants.',
            'service_status'=>'0','about_us_status'=>'0','how_we_work_status'=>'0','country_status'=>'1','top_university_status'=>'1',
            'upcoming_event_status'=>'0','why_choose_us_status'=>'0','blog_article_status'=>'0','testimonial_status'=>'0','faq_status'=>'0','cta_footer_status'=>'0',
            'cookie_status'=>'0','app_debug'=>'0','google_login_status'=>'0','facebook_login_status'=>'0','google_analytics_status'=>'0',
        ] as $key => $value) {
            if (!DB::table('settings')->where('option_key', $key)->exists()) {
                DB::table('settings')->insert(['option_key' => $key, 'option_value' => $value, 'created_at' => now(), 'updated_at' => now()]);
            }
        }
        if (Schema::hasTable('currencies')) foreach ([['USD','$','before',0],['PKR','Rs','before',1]] as [$code,$symbol,$placement,$current]) {
            if (!DB::table('currencies')->where('currency_code', $code)->exists()) {
                DB::table('currencies')->insert(['currency_code' => $code, 'symbol' => $symbol, 'currency_placement' => $placement, 'current_currency' => $current, 'created_at' => now(), 'updated_at' => now()]);
            }
        }
        if (Schema::hasTable('languages') && !DB::table('languages')->where('iso_code', 'en')->exists()) {
            DB::table('languages')->insert(['iso_code' => 'en', 'language' => 'English', 'rtl' => 0, 'status' => 1, 'default' => 1, 'created_at' => now(), 'updated_at' => now()]);
        }
        if (Schema::hasTable('users')) {
            $this->demoUser('demo.admin@genzconsultants.example', ['first_name' => 'GenZ', 'last_name' => 'Demo Admin', 'role' => USER_ROLE_ADMIN, 'status' => STATUS_ACTIVE]);
            foreach ([
                ['Demo', 'Counselor', 'demo.counselor@genzconsultants.example', USER_ROLE_CONSULTANT],
                ['Demo', 'Student Advisor', 'demo.advisor@genzconsultants.example', USER_ROLE_STAFF],
                ['Demo', 'Affiliate', 'demo.affiliate@genzconsultants.example', USER_ROLE_AFFILIATE],
                ['Demo', 'Finance', 'demo.finance@genzconsultants.example', USER_ROLE_FINANCE],
                ['Demo', 'Content Editor', 'demo.editor@genzconsultants.example', USER_ROLE_CMS_EDITOR],
            ] as [$first, $last, $email, $role]) {
                $this->demoUser($email, [
                    'first_name' => $first, 'last_name' => $last,
                    'role' => $role, 'status' => STATUS_ACTIVE,
                    ...($role === USER_ROLE_CONSULTANT ? ['fee' => 0] : []),
                ]);
            }
        }
    }

    private function seedLearningScenarios(): void
    {
        $instructor = $this->demoUser('demo.instructor@genzconsultants.example', [
            'first_name' => 'Demo', 'last_name' => 'Instructor',
            'role' => config('genz.roles.instructor', USER_ROLE_INSTRUCTOR), 'status' => STATUS_ACTIVE,
        ]);

        $tracks = [
            ['Demo Language Preparation', 'demo-language-preparation'],
            ['Demo Academic Readiness', 'demo-academic-readiness'],
            ['Demo Digital Skills', 'demo-digital-skills'],
            ['Demo Career Development', 'demo-career-development'],
        ];
        $trackIds = [];
        foreach ($tracks as [$title, $slug]) {
            DB::table('programs')->updateOrInsert(['slug' => $slug], [
                'title' => $title, 'top_section' => '{}', 'step_section' => '{}', 'created_by' => $instructor->id,
                'status' => STATUS_ACTIVE, 'updated_at' => now(), 'created_at' => now(),
            ]);
            $trackIds[$slug] = DB::table('programs')->where('slug', $slug)->value('id');
        }

        $courseScenarios = [
            ['IELTS Foundations: Speaking & Listening', 'demo-ielts-speaking-listening', 'demo-language-preparation', 6],
            ['Academic Writing for University', 'demo-academic-writing', 'demo-academic-readiness', 5],
            ['Study Skills: Research & Referencing', 'demo-research-referencing', 'demo-academic-readiness', 4],
            ['English Test Preparation: Reading', 'demo-english-reading', 'demo-language-preparation', 5],
            ['Digital Essentials for Students', 'demo-digital-essentials', 'demo-digital-skills', 3],
            ['Data Literacy: First Steps', 'demo-data-literacy', 'demo-digital-skills', 4],
            ['Career Planning for Global Graduates', 'demo-global-career-planning', 'demo-career-development', 3],
            ['Scholarship & Application Readiness', 'demo-scholarship-readiness', 'demo-career-development', 2],
        ];
        $courses = [];
        foreach ($courseScenarios as [$title, $slug, $trackSlug, $weeks]) {
            $fields = [
                'program_id' => $trackIds[$trackSlug], 'title' => $title, 'subtitle' => 'Fictional GenZ demo learning path',
                'description' => 'Synthetic course content for local evaluation. It is not an accredited course or a real university offer.',
                'duration' => $weeks, 'start_date' => now()->subDay()->toDateString(), 'price' => 0,
                'course_benefits' => ['Self-paced sample lessons', 'Practice activities', 'Progress tracking'],
                'description_point' => ['Demo curriculum', 'Fictional sample data'], 'faqs' => [],
                'instructors' => [['name' => $instructor->name, 'professional_title' => 'GenZ Demo Instructor', 'photo' => null]],
                'created_by' => $instructor->id, 'status' => STATUS_ACTIVE,
            ];
            if (Schema::hasColumn('courses', 'publication_state')) $fields['publication_state'] = 'published';
            if (Schema::hasColumn('courses', 'difficulty')) $fields['difficulty'] = $weeks > 4 ? 'beginner' : 'beginner';
            if (Schema::hasColumn('courses', 'language')) $fields['language'] = 'English';
            if (Schema::hasColumn('courses', 'estimated_minutes')) $fields['estimated_minutes'] = $weeks * 90;
            if (Schema::hasColumn('courses', 'capacity')) $fields['capacity'] = 100;
            $courses[] = Course::updateOrCreate(['slug' => $slug], $fields);
        }

        $learnerScenarios = [
            ['Amina', 'Khan', 'demo.learner.amina@genzconsultants.example'],
            ['Omar', 'Farooq', 'demo.learner.omar@genzconsultants.example'],
            ['Noor', 'Ahmed', 'demo.learner.noor@genzconsultants.example'],
            ['Hassan', 'Raza', 'demo.learner.hassan@genzconsultants.example'],
            ['Sara', 'Malik', 'demo.learner.sara@genzconsultants.example'],
            ['Zain', 'Qureshi', 'demo.learner.zain@genzconsultants.example'],
            ['Maryam', 'Ali', 'demo.learner.maryam@genzconsultants.example'],
            ['Bilal', 'Siddiqui', 'demo.learner.bilal@genzconsultants.example'],
        ];
        $courseAssignments = [[0, 1], [0, 2, 4], [1, 3], [2, 5], [0, 4, 6], [3, 5, 7], [1, 6], [2, 5, 7]];
        $learners = [];
        foreach ($learnerScenarios as $index => [$first, $last, $email]) {
            $student = $this->demoUser($email, [
                'first_name' => $first, 'last_name' => $last,
                'role' => USER_ROLE_STUDENT, 'status' => STATUS_ACTIVE,
            ]);
            $learners[$email] = $student;
            foreach ($courseAssignments[$index] as $courseIndex) {
                $course = $courses[$courseIndex];
                $enrollment = app(LmsEnrollmentService::class)->enroll($student->id, $course->id, 'demo');
                $offset = ($index + ($courseIndex * 2)) % 12;
                $createdAt = now()->startOfMonth()->subMonths($offset)->addDays(2 + ($index % 9));
                DB::table('course_enrollments')->where('id', $enrollment->id)->update(['created_at' => $createdAt]);
            }
        }
        $this->seedDemoConsultations($learners);

        if (Schema::hasTable('course_lessons') && Schema::hasTable('course_lectures') && Schema::hasTable('course_resources')) foreach ($courses as $course) {
            $lessonId = DB::table('course_lessons')->where('course_id', $course->id)->where('title', 'Demo: Getting started')->value('id');
            if (!$lessonId) $lessonId = DB::table('course_lessons')->insertGetId(['course_id' => $course->id, 'title' => 'Demo: Getting started', 'description' => 'A short fictional introduction to this sample course.', 'created_at' => now(), 'updated_at' => now()]);
            $lectureId = DB::table('course_lectures')->where('course_id', $course->id)->where('course_lesson_id', $lessonId)->where('title', 'Demo: Welcome')->value('id');
            if (!$lectureId) $lectureId = DB::table('course_lectures')->insertGetId(['course_id' => $course->id, 'course_lesson_id' => $lessonId, 'title' => 'Demo: Welcome', 'created_at' => now(), 'updated_at' => now()]);
            $resource = ['course_id' => $course->id, 'course_lesson_id' => $lessonId, 'course_lecture_id' => $lectureId, 'title' => 'Demo: Course guide', 'resource_type' => 1, 'duration' => 5, 'resource' => 'Fictional instructional text for the local demonstration environment.', 'updated_at' => now(), 'created_at' => now()];
            if (Schema::hasColumn('course_resources', 'publication_state')) $resource['publication_state'] = 'published';
            if (Schema::hasColumn('course_resources', 'sort_order')) $resource['sort_order'] = 1;
            if (Schema::hasColumn('course_resources', 'completion_required')) $resource['completion_required'] = true;
            DB::table('course_resources')->updateOrInsert(['course_id' => $course->id, 'title' => 'Demo: Course guide'], $resource);
        }

        if (Schema::hasTable('admission_applications') && Schema::hasTable('academic_programs')) {
            $programs = DB::table('academic_programs')->whereIn('slug', ['demo-data-science-msc', 'demo-global-business-ba', 'demo-sustainable-engineering-msc'])->get()->keyBy('slug');
            foreach ([
                ['GZ-DEMO-APP-001', 'demo.learner.amina@genzconsultants.example', 'demo-data-science-msc', 'document_collection'],
                ['GZ-DEMO-APP-002', 'demo.learner.omar@genzconsultants.example', 'demo-global-business-ba', 'submitted'],
                ['GZ-DEMO-APP-003', 'demo.learner.noor@genzconsultants.example', 'demo-sustainable-engineering-msc', 'conditional_offer'],
                ['GZ-DEMO-APP-004', 'demo.learner.hassan@genzconsultants.example', 'demo-data-science-msc', 'university_review'],
            ] as [$reference, $email, $slug, $status]) {
                if (isset($learners[$email], $programs[$slug])) {
                    DB::table('admission_applications')->updateOrInsert(['reference_code' => $reference], [
                        'student_id' => $learners[$email]->id, 'university_id' => $programs[$slug]->university_id,
                        'academic_program_id' => $programs[$slug]->id, 'status' => $status, 'visa_status' => 'not_started',
                        'deleted_at' => null, 'updated_at' => now(), 'created_at' => now(),
                    ]);
                }
            }
        }
    }

    private function seedDemoConsultations(array $learners): void
    {
        if (!Schema::hasTable('consultation_slots') || !Schema::hasTable('consultation_slot_user') || !Schema::hasTable('appointments')) return;
        if (!Schema::hasColumn('users', 'consultation_timezone') || !Schema::hasColumn('appointments', 'is_free')) return;

        $consultant = User::where('email', 'demo.counselor@genzconsultants.example')->first();
        $adminId = User::where('email', 'demo.admin@genzconsultants.example')->value('id');
        if (!$consultant || !$adminId) return;

        foreach ([
            ['demo.learner.amina@genzconsultants.example', '10:00 AM', '10:30 AM', 7],
            ['demo.learner.omar@genzconsultants.example', '11:00 AM', '11:30 AM', 10],
            ['demo.learner.noor@genzconsultants.example', '12:00 PM', '12:30 PM', 14],
        ] as [$email, $start, $end, $daysAhead]) {
            if (!isset($learners[$email])) continue;
            $student = $learners[$email];
            if (Appointment::where('user_id', $student->id)->where('consulter_id', $consultant->id)->where('is_free', 1)->exists()) continue;

            DB::table('consultation_slots')->updateOrInsert(['start_time' => $start, 'end_time' => $end], [
                'status' => STATUS_ACTIVE, 'deleted_at' => null, 'updated_at' => now(), 'created_at' => now(),
            ]);
            $slotId = DB::table('consultation_slots')->where('start_time', $start)->where('end_time', $end)->value('id');
            $pivotExists = DB::table('consultation_slot_user')->where('consultation_slot_id', $slotId)->where('user_id', $consultant->id)->exists();
            if (!$pivotExists) DB::table('consultation_slot_user')->insert(['consultation_slot_id' => $slotId, 'user_id' => $consultant->id]);

            $appointment = app(ConsultationBookingService::class)->book(
                (int) $student->id, (int) $consultant->id, (int) $slotId,
                now('Asia/Karachi')->addDays($daysAhead)->toDateString(), CONSULTATION_TYPE_VIRTUAL,
                PAYMENT_STATUS_PAID, null, (int) $adminId
            );
            $appointment->update(['is_free' => 1]);
        }
    }

    private function seedExpandedWorkspaceDemos(): void
    {
        $learners = User::whereIn('email', [
            'demo.learner.amina@genzconsultants.example',
            'demo.learner.omar@genzconsultants.example',
            'demo.learner.noor@genzconsultants.example',
            'demo.learner.hassan@genzconsultants.example',
        ])->get()->keyBy('email');
        $admin = User::where('email', 'demo.admin@genzconsultants.example')->first()
            ?? User::where('role', USER_ROLE_ADMIN)->orderBy('id')->first();
        $counselor = User::where('email', 'demo.counselor@genzconsultants.example')->first();

        if (!$admin || $learners->isEmpty()) {
            return;
        }

        $countryIds = DB::table('countries')->whereIn('slug', ['demo-canada', 'demo-united-kingdom', 'demo-australia'])->pluck('id', 'slug')->all();
        $universityIds = DB::table('universities')->whereIn('slug', ['demo-north-university', 'demo-thames-institute', 'demo-southern-coast-university'])->pluck('id', 'slug')->all();
        $programs = DB::table('academic_programs')->whereIn('slug', ['demo-data-science-msc', 'demo-global-business-ba', 'demo-sustainable-engineering-msc'])->get()->keyBy('slug');
        $documentIds = [];

        foreach ($learners->values() as $index => $student) {
            $email = $student->email;
            $preferredCountry = array_values($countryIds)[($index % max(count($countryIds), 1))] ?? null;
            $preferredUniversity = array_values($universityIds)[($index % max(count($universityIds), 1))] ?? null;
            DB::table('student_profiles')->updateOrInsert(['user_id' => $student->id], [
                'date_of_birth' => now()->subYears(20 + $index)->subDays($index * 17)->toDateString(),
                'gender' => $index % 2 === 0 ? 'female' : 'male',
                'nationality' => 'Demo nationality',
                'passport_number' => null,
                'present_address' => 'Fictional demo address; replace with the student-provided address.',
                'permanent_address' => null,
                'preferred_country_ids' => json_encode($preferredCountry ? [$preferredCountry] : []),
                'preferred_university_ids' => json_encode($preferredUniversity ? [$preferredUniversity] : []),
                'preferred_subject_ids' => json_encode([]),
                'target_intake' => 'Demo intake 2027',
                'updated_at' => now(),
                'created_at' => now(),
            ]);
        }

        foreach ([
            ['demo.learner.amina@genzconsultants.example', 'academic_transcript', 'pending', null],
            ['demo.learner.omar@genzconsultants.example', 'passport', 'approved', 'Synthetic sample reviewed for interface demonstration only.'],
            ['demo.learner.noor@genzconsultants.example', 'language_test', 'rejected', 'Synthetic sample: please upload a clearer copy.'],
        ] as [$email, $category, $status, $reviewNote]) {
            $student = $learners->get($email);
            if (!$student) {
                continue;
            }

            $path = 'demo/student-documents/' . str_replace(['@', '.'], '-', $email) . '-' . $category . '.enc';
            $contents = "FICTIONAL GENZ DEMONSTRATION DOCUMENT\nThis file contains no real student or passport information.\n";
            Storage::disk('private')->put($path, Crypt::encryptString($contents));
            DB::table('student_documents')->updateOrInsert(['storage_path' => $path], [
                'user_id' => $student->id,
                'replaces_document_id' => null,
                'category' => $category,
                'original_name' => 'fictional-demo-document.txt',
                'mime_type' => 'text/plain',
                'size_bytes' => strlen($contents),
                'status' => $status,
                'reviewed_by' => $status === 'pending' ? null : $admin->id,
                'reviewed_at' => $status === 'pending' ? null : now(),
                'review_note' => $reviewNote,
                'updated_at' => now(),
                'created_at' => now(),
            ]);
            $documentIds[$email] = DB::table('student_documents')->where('storage_path', $path)->value('id');
        }

        foreach ($programs as $program) {
            $label = 'Demo intake ' . now()->addYear()->year;
            DB::table('program_intakes')->updateOrInsert(
                ['academic_program_id' => $program->id, 'label' => $label],
                [
                    'start_date' => now()->addMonths(4)->startOfMonth()->toDateString(),
                    'application_deadline' => now()->addMonths(3)->endOfMonth()->toDateString(),
                    'status' => 'open',
                    'source_url' => null,
                    'last_verified_at' => null,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        if (isset($programs['demo-data-science-msc'])) {
            $program = $programs['demo-data-science-msc'];
            foreach ($learners->take(3) as $student) {
                DB::table('student_saved_options')->updateOrInsert(
                    ['user_id' => $student->id, 'item_type' => 'academic_program', 'item_id' => $program->id],
                    ['updated_at' => now(), 'created_at' => now()]
                );
            }
        }

        foreach ([
            'GZ-DEMO-APP-001' => 'demo.learner.amina@genzconsultants.example',
            'GZ-DEMO-APP-002' => 'demo.learner.omar@genzconsultants.example',
            'GZ-DEMO-APP-003' => 'demo.learner.noor@genzconsultants.example',
            'GZ-DEMO-APP-004' => 'demo.learner.hassan@genzconsultants.example',
        ] as $reference => $email) {
            $application = DB::table('admission_applications')->where('reference_code', $reference)->first();
            $student = $learners->get($email);
            if (!$application || !$student) {
                continue;
            }

            $intake = DB::table('program_intakes')->where('academic_program_id', $application->academic_program_id)->first();
            DB::table('admission_applications')->where('id', $application->id)->update([
                'program_intake_id' => $intake?->id,
                'counselor_id' => $counselor?->id,
                'application_deadline' => now()->addMonths(3)->toDateString(),
                'updated_at' => now(),
            ]);
            DB::table('application_status_history')->updateOrInsert(
                ['admission_application_id' => $application->id, 'to_status' => $application->status, 'reason' => 'Fictional demo lifecycle event.'],
                ['from_status' => null, 'actor_id' => $admin->id, 'metadata' => json_encode(['demo' => true]), 'updated_at' => now(), 'created_at' => now()]
            );
            DB::table('application_notes')->updateOrInsert(
                ['admission_application_id' => $application->id, 'author_id' => $admin->id, 'body' => 'Fictional demo note: verify all admissions information directly with the institution.'],
                ['visibility' => 'internal', 'updated_at' => now(), 'created_at' => now()]
            );
            DB::table('application_tasks')->updateOrInsert(
                ['admission_application_id' => $application->id, 'title' => 'Demo: review application checklist'],
                ['assignee_id' => $counselor?->id, 'created_by' => $admin->id, 'description' => 'Sample workflow task; no real applicant action is required.', 'due_at' => now()->addDays(5), 'completed_at' => null, 'updated_at' => now(), 'created_at' => now()]
            );
            if (isset($documentIds[$email])) {
                DB::table('application_documents')->updateOrInsert(
                    ['admission_application_id' => $application->id, 'student_document_id' => $documentIds[$email]],
                    ['linked_by' => $admin->id, 'updated_at' => now(), 'created_at' => now()]
                );
            }
        }

        $lead = DB::table('crm_leads')->where('email', 'demo.lead.1@genzconsultants.example')->first();
        if ($lead) {
            DB::table('crm_lead_activities')->updateOrInsert(
                ['lead_id' => $lead->id, 'type' => 'demo', 'description' => 'Synthetic demo lead activity. No real applicant is involved.'],
                ['actor_id' => $admin->id, 'from_value' => null, 'to_value' => 'new', 'metadata' => json_encode(['demo' => true]), 'updated_at' => now(), 'created_at' => now()]
            );
            DB::table('crm_lead_notes')->updateOrInsert(
                ['lead_id' => $lead->id, 'author_id' => $admin->id, 'body' => 'Fictional demo note for the CRM notes panel.'],
                ['is_private' => true, 'updated_at' => now(), 'created_at' => now()]
            );
            $followUp = DB::table('crm_follow_ups')->where('lead_id', $lead->id)->first();
            if ($followUp) {
                DB::table('crm_follow_up_comments')->updateOrInsert(
                    ['follow_up_id' => $followUp->id, 'author_id' => $admin->id, 'body' => 'Demo comment: confirm the applicant’s preferred intake.'],
                    ['updated_at' => now(), 'created_at' => now()]
                );
                DB::table('crm_task_notification_outbox')->updateOrInsert(
                    ['dedupe_key' => 'genz-demo-follow-up-' . $followUp->id],
                    ['recipient_id' => $counselor?->id ?? $admin->id, 'title' => 'Demo follow-up reminder', 'body' => 'Synthetic reminder record; no external email or message was sent.', 'link' => null, 'attempts' => 1, 'available_at' => now()->subMinute(), 'sent_at' => now()->subMinute(), 'last_error' => null, 'updated_at' => now(), 'created_at' => now()]
                );
            }
        }

        if ($counselor) {
            DB::table('consultation_blackout_dates')->updateOrInsert(
                ['consultant_id' => $counselor->id, 'date' => now('Asia/Karachi')->addDays(45)->toDateString()],
                ['reason' => 'Synthetic demo blackout date; this is not a real staff schedule.', 'created_by' => $admin->id, 'updated_at' => now(), 'created_at' => now()]
            );
            $appointment = Appointment::where('consulter_id', $counselor->id)->orderBy('id')->first();
            if ($appointment) {
                DB::table('consultation_booking_events')->updateOrInsert(
                    ['appointment_id' => $appointment->id, 'event_type' => 'demo_created', 'reason' => 'Synthetic demo booking history.'],
                    ['actor_id' => $admin->id, 'old_values' => null, 'new_values' => json_encode(['demo' => true]), 'updated_at' => now(), 'created_at' => now()]
                );
            }
        }

        $course = Course::where('slug', 'demo-ielts-speaking-listening')->first();
        $amina = $learners->get('demo.learner.amina@genzconsultants.example');
        $omar = $learners->get('demo.learner.omar@genzconsultants.example');
        if ($course && $amina) {
            $enrollment = DB::table('course_enrollments')->where('course_id', $course->id)->where('user_id', $amina->id)->first();
            $omarEnrollment = $omar ? DB::table('course_enrollments')->where('course_id', $course->id)->where('user_id', $omar->id)->first() : null;
            $resource = DB::table('course_resources')->where('course_id', $course->id)->where('title', 'Demo: Course guide')->first();
            $lessonId = $resource?->course_lesson_id;

            $batchTitle = 'Demo IELTS cohort ' . now()->addYear()->year;
            DB::table('course_batches')->updateOrInsert(
                ['course_id' => $course->id, 'title' => $batchTitle],
                ['starts_at' => now()->subDays(7), 'ends_at' => now()->addWeeks(5), 'timezone' => 'Asia/Karachi', 'capacity' => 20, 'status' => 'active', 'created_by' => $admin->id, 'updated_at' => now(), 'created_at' => now()]
            );
            $batchId = DB::table('course_batches')->where('course_id', $course->id)->where('title', $batchTitle)->value('id');
            foreach ([$enrollment, $omarEnrollment] as $memberEnrollment) {
                if ($memberEnrollment) {
                    DB::table('course_batch_memberships')->updateOrInsert(
                        ['course_enrollment_id' => $memberEnrollment->id],
                        ['course_batch_id' => $batchId, 'joined_at' => now()->subDays(6), 'updated_at' => now(), 'created_at' => now()]
                    );
                }
            }

            if ($resource && $enrollment) {
                foreach ([[$enrollment, $amina, now()->subHours(3)], [$omarEnrollment, $omar, null]] as [$progressEnrollment, $student, $completedAt]) {
                    if (!$progressEnrollment || !$student) {
                        continue;
                    }
                    DB::table('lms_lesson_progress')->updateOrInsert(
                        ['course_enrollment_id' => $progressEnrollment->id, 'course_resource_id' => $resource->id],
                        ['user_id' => $student->id, 'last_position_seconds' => $completedAt ? 300 : 90, 'time_spent_seconds' => $completedAt ? 480 : 120, 'completed_at' => $completedAt, 'updated_at' => now(), 'created_at' => now()]
                    );
                }
                DB::table('course_content_revisions')->updateOrInsert(
                    ['content_type' => 'resource', 'content_id' => $resource->id, 'revision_number' => 1],
                    ['course_id' => $course->id, 'snapshot' => json_encode(['title' => $resource->title, 'resource' => 'Fictional demo learning content.']), 'edited_by' => $admin->id, 'updated_at' => now(), 'created_at' => now()]
                );
            }

            $sessionTitle = 'Demo IELTS live Q&A';
            DB::table('course_live_sessions')->updateOrInsert(
                ['course_id' => $course->id, 'title' => $sessionTitle],
                ['course_batch_id' => $batchId, 'description' => 'Fictional completed session for interface testing. No meeting was held.', 'provider' => 'external', 'meeting_url' => null, 'recording_url' => null, 'starts_at' => now()->subDays(2), 'duration_minutes' => 45, 'timezone' => 'Asia/Karachi', 'status' => 'completed', 'schedule_revision' => 1, 'created_by' => $admin->id, 'updated_at' => now(), 'created_at' => now()]
            );
            $sessionId = DB::table('course_live_sessions')->where('course_id', $course->id)->where('title', $sessionTitle)->value('id');
            foreach ([[$enrollment, $amina, 'attended'], [$omarEnrollment, $omar, 'absent']] as [$attendEnrollment, $student, $attendanceStatus]) {
                if ($attendEnrollment && $student) {
                    DB::table('course_live_attendance')->updateOrInsert(
                        ['course_live_session_id' => $sessionId, 'course_enrollment_id' => $attendEnrollment->id],
                        ['user_id' => $student->id, 'joined_at' => $attendanceStatus === 'absent' ? null : now()->subDays(2), 'last_seen_at' => now()->subDays(2), 'left_at' => $attendanceStatus === 'absent' ? null : now()->subDays(2)->addMinutes(40), 'attended_seconds' => $attendanceStatus === 'absent' ? 0 : 2400, 'attendance_status' => $attendanceStatus, 'updated_at' => now(), 'created_at' => now()]
                    );
                }
            }

            if ($lessonId && $enrollment) {
                $bankTitle = 'Demo IELTS question bank';
                DB::table('lms_question_banks')->updateOrInsert(
                    ['course_id' => $course->id, 'title' => $bankTitle],
                    ['created_by' => $admin->id, 'updated_at' => now(), 'created_at' => now()]
                );
                $bankId = DB::table('lms_question_banks')->where('course_id', $course->id)->where('title', $bankTitle)->value('id');
                $quizTitle = 'Demo Listening Checkpoint';
                DB::table('lms_quizzes')->updateOrInsert(
                    ['course_id' => $course->id, 'title' => $quizTitle],
                    ['course_lesson_id' => $lessonId, 'description' => 'Fictional sample quiz; results are for demonstration only.', 'status' => 'published', 'opens_at' => now()->subDays(30), 'closes_at' => null, 'time_limit_seconds' => 900, 'max_attempts' => 3, 'pass_percent' => 60, 'shuffle_questions' => false, 'manual_result_release' => false, 'created_by' => $admin->id, 'updated_at' => now(), 'created_at' => now()]
                );
                $quizId = DB::table('lms_quizzes')->where('course_id', $course->id)->where('title', $quizTitle)->value('id');
                $questionIds = [];
                foreach ([
                    ['Demo question: Which skill helps identify the main idea?', ['Listening for keywords', 'Ignoring context', 'Skipping instructions'], 1],
                    ['Demo question: What should a learner do before an application?', ['Check official requirements', 'Assume every course is identical', 'Send blank documents'], 1],
                ] as $questionIndex => [$questionText, $options, $correctOption]) {
                    DB::table('lms_questions')->updateOrInsert(
                        ['course_id' => $course->id, 'question_text' => $questionText],
                        ['question_bank_id' => $bankId, 'question_type' => 'single', 'explanation' => 'Fictional sample explanation.', 'points' => 1, 'status' => 'published', 'created_by' => $admin->id, 'updated_at' => now(), 'created_at' => now()]
                    );
                    $questionId = DB::table('lms_questions')->where('course_id', $course->id)->where('question_text', $questionText)->value('id');
                    $questionIds[] = $questionId;
                    foreach ($options as $optionIndex => $optionText) {
                        DB::table('lms_question_options')->updateOrInsert(
                            ['lms_question_id' => $questionId, 'sort_order' => $optionIndex + 1],
                            ['option_text' => $optionText, 'is_correct' => ($optionIndex + 1) === $correctOption, 'updated_at' => now(), 'created_at' => now()]
                        );
                    }
                    DB::table('lms_quiz_questions')->updateOrInsert(
                        ['lms_quiz_id' => $quizId, 'lms_question_id' => $questionId],
                        ['sort_order' => $questionIndex + 1, 'updated_at' => now(), 'created_at' => now()]
                    );
                }

                $attemptNumber = 1;
                DB::table('lms_quiz_attempts')->updateOrInsert(
                    ['lms_quiz_id' => $quizId, 'course_enrollment_id' => $enrollment->id, 'attempt_number' => $attemptNumber],
                    ['status' => 'submitted', 'question_order' => json_encode($questionIds), 'started_at' => now()->subDays(2), 'deadline_at' => now()->subDays(2)->addMinutes(15), 'submitted_at' => now()->subDays(2)->addMinutes(8), 'score' => 2, 'total_points' => 2, 'percentage' => 100, 'passed' => true, 'result_released_at' => now()->subDays(2), 'updated_at' => now(), 'created_at' => now()]
                );
                $attemptId = DB::table('lms_quiz_attempts')->where('lms_quiz_id', $quizId)->where('course_enrollment_id', $enrollment->id)->where('attempt_number', $attemptNumber)->value('id');
                foreach ($questionIds as $questionId) {
                    $correctId = DB::table('lms_question_options')->where('lms_question_id', $questionId)->where('is_correct', true)->value('id');
                    DB::table('lms_answers')->updateOrInsert(
                        ['lms_quiz_attempt_id' => $attemptId, 'lms_question_id' => $questionId],
                        ['selected_option_ids' => json_encode([$correctId]), 'awarded_points' => 1, 'updated_at' => now(), 'created_at' => now()]
                    );
                }

                $assignmentTitle = 'Demo: IELTS listening reflection';
                DB::table('lms_assignments')->updateOrInsert(
                    ['course_id' => $course->id, 'title' => $assignmentTitle],
                    ['course_lesson_id' => $lessonId, 'instructions' => 'Write a short fictional reflection on the sample lesson.', 'due_at' => now()->addDays(10), 'max_points' => 100, 'max_submissions' => 2, 'late_policy' => 'accept', 'late_penalty_percent' => 0, 'status' => 'published', 'created_by' => $admin->id, 'updated_at' => now(), 'created_at' => now()]
                );
                $assignmentId = DB::table('lms_assignments')->where('course_id', $course->id)->where('title', $assignmentTitle)->value('id');
                DB::table('lms_assignment_submissions')->updateOrInsert(
                    ['lms_assignment_id' => $assignmentId, 'course_enrollment_id' => $enrollment->id, 'version' => 1],
                    ['user_id' => $amina->id, 'status' => 'submitted', 'text_response' => 'Fictional learner response for grading workflow demonstration.', 'file_manager_id' => null, 'is_late' => false, 'submitted_at' => now()->subDays(1), 'updated_at' => now(), 'created_at' => now()]
                );
                $submissionId = DB::table('lms_assignment_submissions')->where('lms_assignment_id', $assignmentId)->where('course_enrollment_id', $enrollment->id)->where('version', 1)->value('id');
                DB::table('lms_grades')->updateOrInsert(
                    ['lms_assignment_submission_id' => $submissionId],
                    ['grader_id' => $admin->id, 'raw_score' => 88, 'score' => 88, 'max_points' => 100, 'rubric' => json_encode(['clarity' => 4, 'evidence' => 4]), 'feedback' => 'Synthetic example feedback; not a real academic assessment.', 'released_at' => now(), 'updated_at' => now(), 'created_at' => now()]
                );
                $gradeId = DB::table('lms_grades')->where('lms_assignment_submission_id', $submissionId)->value('id');
                DB::table('lms_grade_history')->updateOrInsert(
                    ['lms_grade_id' => $gradeId, 'actor_id' => $admin->id],
                    ['before_values' => null, 'after_values' => json_encode(['score' => 88, 'max_points' => 100, 'demo' => true]), 'updated_at' => now(), 'created_at' => now()]
                );

                $certificateNumber = 'GZ-DEMO-CERT-001';
                $existingCertificate = DB::table('lms_certificates')->where('course_enrollment_id', $enrollment->id)->first();
                DB::table('lms_certificates')->updateOrInsert(
                    ['course_enrollment_id' => $enrollment->id],
                    ['course_id' => $course->id, 'user_id' => $amina->id, 'certificate_number' => $certificateNumber, 'verification_token' => $existingCertificate?->verification_token ?? Str::random(64), 'issued_at' => now()->subDays(1), 'revoked_at' => now(), 'issued_by' => $admin->id, 'revoked_by' => $admin->id, 'revocation_reason' => 'Fictional demo record only; not an accredited certificate.', 'updated_at' => now(), 'created_at' => now()]
                );
            }
        }

        if ($amina = $learners->get('demo.learner.amina@genzconsultants.example')) {
            foreach ([['course_announcement', 'in_app', true], ['admission_update', 'email', false]] as [$eventKey, $channel, $enabled]) {
                DB::table('notification_preferences')->updateOrInsert(
                    ['user_id' => $amina->id, 'event_key' => $eventKey, 'channel' => $channel],
                    ['enabled' => $enabled, 'updated_at' => now(), 'created_at' => now()]
                );
            }
            if (Schema::hasColumn('notifications', 'dedupe_key')) {
                DB::table('notifications')->updateOrInsert(
                    ['dedupe_key' => 'genz-demo-course-welcome-' . $amina->id],
                    ['user_id' => $amina->id, 'title' => 'Demo course update', 'body' => 'Fictional notification for the student portal preview.', 'link' => null, 'view_status' => 0, 'status' => STATUS_ACTIVE, 'updated_at' => now(), 'created_at' => now()]
                );
            }
            DB::table('notification_outbox')->updateOrInsert(
                ['dedupe_key' => 'genz-demo-delivered-notice-' . $amina->id],
                ['recipient_id' => $amina->id, 'event_key' => 'course_announcement', 'channel' => 'in_app', 'title' => 'Demo notice delivery history', 'body' => 'Synthetic in-app delivery record; no email or external message was sent.', 'link' => null, 'attempts' => 1, 'available_at' => now()->subMinute(), 'sent_at' => now()->subMinute(), 'last_error' => null, 'updated_at' => now(), 'created_at' => now()]
            );

            $registrationLead = CrmLead::updateOrCreate(
                ['email' => 'demo.registration@genzconsultants.example'],
                ['first_name' => 'Demo', 'last_name' => 'Registration', 'phone' => '+92 300 000 0000', 'status' => 'new', 'source' => 'student_registration', 'source_detail' => 'Synthetic demo registration', 'assigned_to' => $counselor?->id]
            );
            $signaturePath = 'study-registration-signatures/genz-demo-placeholder.png';
            Storage::disk('local')->put($signaturePath, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII='));
            StudyAbroadRegistration::updateOrCreate(
                ['email' => 'demo.registration@genzconsultants.example'],
                [
                    'lead_id' => $registrationLead->id,
                    'full_name' => 'Demo Learner',
                    'father_name' => 'Demo Parent',
                    'identity_number' => 'DEMO-IDENTITY-0001',
                    'date_of_birth' => now()->subYears(21)->toDateString(),
                    'gender' => 'Female',
                    'marital_status' => 'Single',
                    'phone' => '+92 300 000 0000',
                    'whatsapp' => '+92 300 000 0000',
                    'residential_address' => 'Fictional demo address; no real applicant details.',
                    'last_qualification' => 'Demo qualification',
                    'field_of_study' => 'Demo field',
                    'institute_name' => 'Fictional Demo Institute',
                    'passing_year' => now()->year - 1,
                    'grade' => 'Demo grade',
                    'destination' => 'UK',
                    'branch' => 'Lahore',
                    'program_type' => 'standard',
                    'paid_amount' => 0,
                    'paid_currency' => 'PKR',
                    'signature_path' => $signaturePath,
                    'signed_at' => now()->toDateString(),
                    'terms_accepted' => true,
                    'status' => 'submitted',
                ]
            );
        }
    }

    private function seedDashboardWorkflowScenarios(): void
    {
        if (Schema::hasTable('crm_leads') && Schema::hasTable('crm_follow_ups')) {
            $adminId = User::where('email', 'demo.admin@genzconsultants.example')->value('id');
            $counselorId = User::where('email', 'demo.counselor@genzconsultants.example')->value('id');
            $scenarios = [
                ['demo.lead.1@genzconsultants.example', 'Confirm preferred intake and destination', -4, 'high', false],
                ['demo.lead.2@genzconsultants.example', 'Review academic documents with applicant', -2, 'normal', false],
                ['demo.lead.3@genzconsultants.example', 'Share scholarship eligibility checklist', -1, 'high', false],
                ['demo.lead.4@genzconsultants.example', 'Send application timeline and next steps', 2, 'normal', false],
                ['demo.lead.5@genzconsultants.example', 'Completed: initial application planning call', -3, 'low', true],
            ];

            foreach ($scenarios as [$email, $title, $daysOffset, $priority, $completed]) {
                $lead = CrmLead::where('email', $email)->first();
                if (!$lead) continue;

                CrmFollowUp::updateOrCreate(
                    ['lead_id' => $lead->id, 'title' => 'Demo: '.$title],
                    [
                        'assigned_to' => $counselorId,
                        'created_by' => $adminId,
                        'description' => 'Fictional development-only follow-up scenario. No real applicant is involved.',
                        'due_at' => now()->addDays($daysOffset)->setTime(10, 0),
                        'completed_at' => $completed ? now()->subDay() : null,
                        'priority' => $priority,
                        'status' => $completed ? 'completed' : 'open',
                    ]
                );
            }
        }

        if (!Schema::hasTable('student_service_orders') || !Schema::hasTable('student_service_order_invoices')) return;

        $studentId = User::where('email', 'demo.learner.amina@genzconsultants.example')->value('id');
        $adminId = User::where('email', 'demo.admin@genzconsultants.example')->value('id');
        $service = DB::table('services')->where('slug', 'demo-application-planning')->first();
        if (!$studentId || !$adminId || !$service) return;

        DB::transaction(function () use ($studentId, $adminId, $service): void {
            $amount = (float) $service->price;
            $orderKey = 'GZ-DEMO-ORD-001';
            $invoiceKey = 'GZ-DEMO-INV-001';

            DB::table('student_service_orders')->updateOrInsert(['orderID' => $orderKey], [
                'service_id' => $service->id,
                'student_id' => $studentId,
                'amount' => $amount,
                'discount' => 0,
                'subtotal' => $amount,
                'total' => $amount,
                'transaction_amount' => $amount,
                'payment_status' => PAYMENT_STATUS_PENDING,
                'working_status' => 0,
                'onboard_status' => 0,
                'created_by' => $adminId,
                'deleted_at' => null,
                'updated_at' => now(),
                'created_at' => now(),
            ]);
            $orderId = DB::table('student_service_orders')->where('orderID', $orderKey)->value('id');

            DB::table('student_service_order_invoices')->updateOrInsert(['invoiceID' => $invoiceKey], [
                'student_id' => $studentId,
                'student_service_order_id' => $orderId,
                'service_id' => $service->id,
                'due_date' => now()->addDays(10),
                'details' => 'Fictional demo application-planning invoice. No real payment has been requested or collected.',
                'payable_amount' => $amount,
                'total' => $amount,
                'payment_status' => PAYMENT_STATUS_PENDING,
                'created_by' => $adminId,
                'deleted_at' => null,
                'updated_at' => now(),
                'created_at' => now(),
            ]);
            $invoiceId = DB::table('student_service_order_invoices')->where('invoiceID', $invoiceKey)->value('id');
            DB::table('student_service_orders')->where('id', $orderId)->update([
                'student_service_order_invoice_id' => $invoiceId,
                'updated_at' => now(),
            ]);
        });
    }
}
