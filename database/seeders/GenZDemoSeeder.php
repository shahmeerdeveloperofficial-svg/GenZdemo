<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Appointment;
use App\Models\CrmLead;
use App\Models\CrmFollowUp;
use App\Models\CourseLiveSession;
use App\Models\User;
use App\Services\ConsultationBookingService;
use App\Services\LmsEnrollmentService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class GenZDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production') || !(app()->environment('local') || config('genz.demo_seed_enabled'))) {
            $this->command?->error('Demo seeding is disabled outside an explicitly enabled non-production environment.');
            return;
        }
        if (!in_array(DB::connection()->getDatabaseName(), ['genz_crm_lms_demo', 'demo'], true)) {
            $this->command?->error('Demo seeding is restricted to the isolated genz_crm_lms_demo or demo database. No records were changed.');
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
        $this->seedRemainingDemoModules();
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
        if(Schema::hasTable('settings')) foreach ([
            'build_version'=>(string)config('app.build_version'),'current_version'=>(string)config('app.current_version'),
            'app_name'=>'GenZ Consultants','app_email'=>config('genz.contact.email'),'app_contact_number'=>config('genz.contact.phone'),
            'app_location'=>config('genz.contact.address'),'app_timezone'=>'Asia/Karachi','app_footer_text'=>config('genz.tagline'),
            'hero_banner_status'=>'1','banner_title'=>'Your study abroad journey starts here.','banner_description'=>'Explore destinations, prepare your application, and learn with GenZ Consultants.',
            'service_status'=>'0','about_us_status'=>'0','how_we_work_status'=>'0','country_status'=>'1','top_university_status'=>'1',
            'upcoming_event_status'=>'0','why_choose_us_status'=>'0','blog_article_status'=>'0','testimonial_status'=>'0','faq_status'=>'0','cta_footer_status'=>'0',
            'cookie_status'=>'0','app_debug'=>'0','google_login_status'=>'0','facebook_login_status'=>'0','google_analytics_status'=>'0',
        ] as $key=>$value) DB::table('settings')->updateOrInsert(['option_key'=>$key],['option_value'=>$value,'updated_at'=>now(),'created_at'=>now()]);
        if(Schema::hasTable('currencies')) foreach ([['USD','$','before',0],['PKR','Rs','before',1]] as [$code,$symbol,$placement,$current]) DB::table('currencies')->updateOrInsert(['currency_code'=>$code],['symbol'=>$symbol,'currency_placement'=>$placement,'current_currency'=>$current,'updated_at'=>now(),'created_at'=>now()]);
        if(Schema::hasTable('languages')) DB::table('languages')->updateOrInsert(['iso_code'=>'en'],['language'=>'English','rtl'=>0,'status'=>1,'default'=>1,'updated_at'=>now(),'created_at'=>now()]);
        if (Schema::hasTable('users')) {
            User::updateOrCreate(['email' => 'demo.admin@genzconsultants.example'], ['first_name' => 'GenZ', 'last_name' => 'Demo Admin', 'password' => Hash::make('GenZLocal!2026'), 'role' => USER_ROLE_ADMIN, 'status' => STATUS_ACTIVE]);
            foreach ([
                ['Demo', 'Counselor', 'demo.counselor@genzconsultants.example', USER_ROLE_CONSULTANT],
                ['Demo', 'Student Advisor', 'demo.advisor@genzconsultants.example', USER_ROLE_STAFF],
                ['Demo', 'Affiliate', 'demo.affiliate@genzconsultants.example', USER_ROLE_AFFILIATE],
                ['Demo', 'Finance', 'demo.finance@genzconsultants.example', config('genz.roles.finance', USER_ROLE_FINANCE)],
                ['Demo', 'Content Editor', 'demo.editor@genzconsultants.example', config('genz.roles.cms_editor', USER_ROLE_CMS_EDITOR)],
            ] as [$first, $last, $email, $role]) {
                User::updateOrCreate(['email' => $email], [
                    'first_name' => $first, 'last_name' => $last, 'password' => Hash::make('GenZLocal!2026'),
                    'role' => $role, 'status' => STATUS_ACTIVE,
                ]);
            }
            User::where('email', 'demo.counselor@genzconsultants.example')->update(['fee' => 0]);
            if (Schema::hasColumn('users', 'consultation_timezone')) User::where('email', 'demo.counselor@genzconsultants.example')->update(['consultation_timezone' => 'Asia/Karachi']);
        }
    }

    private function seedLearningScenarios(): void
    {
        $instructor = User::updateOrCreate(['email' => 'demo.instructor@genzconsultants.example'], [
            'first_name' => 'Demo', 'last_name' => 'Instructor', 'password' => Hash::make('GenZLocal!2026'),
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
            $student = User::updateOrCreate(['email' => $email], [
                'first_name' => $first, 'last_name' => $last, 'password' => Hash::make('GenZLocal!2026'),
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

        if (!Schema::hasTable('payments') || !Schema::hasTable('gateways')) return;
        DB::table('gateways')->updateOrInsert(['slug' => 'cash'], [
            'title' => 'Cash', 'status' => STATUS_ACTIVE, 'updated_at' => now(), 'created_at' => now(),
        ]);
        $cashGatewayId = DB::table('gateways')->where('slug', 'cash')->value('id');
        if (!$cashGatewayId) return;

        foreach ([
            ['demo.learner.amina@genzconsultants.example', '10:00 AM', '10:30 AM', 7],
            ['demo.learner.omar@genzconsultants.example', '11:00 AM', '11:30 AM', 10],
            ['demo.learner.noor@genzconsultants.example', '12:00 PM', '12:30 PM', 14],
        ] as [$email, $start, $end, $daysAhead]) {
            if (!isset($learners[$email])) continue;
            $student = $learners[$email];
            $appointment = Appointment::where('user_id', $student->id)->where('consulter_id', $consultant->id)->where('is_free', 1)->first();

            if (!$appointment) {
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

            $transactionId = 'GZ-DEMO-CONSULT-' . $appointment->id;
            DB::table('payments')->updateOrInsert(['tnxId' => $transactionId], [
                'paymentable_id' => (string) $appointment->id,
                'paymentable_type' => Appointment::class,
                'gateway_id' => $cashGatewayId,
                'paymentId' => $transactionId,
                'user_id' => $student->id,
                'sub_total' => 0,
                'tax' => 0,
                'conversion_rate' => 1,
                'grand_total' => 0,
                'grand_total_with_conversation_rate' => 0,
                'subscription_type' => 1,
                'payment_status' => PAYMENT_STATUS_PAID,
                'paid_by' => $student->id,
                'deleted_at' => null,
                'updated_at' => now(),
                'created_at' => now(),
            ]);

            if (Schema::hasTable('consulter_feedback') && !DB::table('consulter_feedback')->where('appointment_id', $appointment->id)->exists()) {
                $demoReviews = [
                    'Demo review: The counselor explained study options clearly and outlined practical next steps.',
                    'Demo review: The consultation was helpful and answered my questions about the application timeline.',
                    'Demo review: I received useful guidance about choosing a suitable program and preparing documents.',
                ];
                $reviewIndex = array_search($email, [
                    'demo.learner.amina@genzconsultants.example',
                    'demo.learner.omar@genzconsultants.example',
                    'demo.learner.noor@genzconsultants.example',
                ], true);
                DB::table('consulter_feedback')->insert([
                    'consulter_id' => $consultant->id,
                    'student_id' => $student->id,
                    'appointment_id' => $appointment->id,
                    'comment' => $demoReviews[$reviewIndex === false ? 0 : $reviewIndex],
                    'status' => $reviewIndex === 1 ? STATUS_PENDING : STATUS_ACTIVE,
                    'deleted_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
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

    private function seedRemainingDemoModules(): void
    {
        $this->call(PermissionSeeder::class);
        $this->seedDemoRoles();
        $this->seedDemoCmsAndSubjects();
        $this->seedDemoStudentProfileAndDocument();
        $this->seedDemoLmsAssessments();
    }

    private function seedDemoRoles(): void
    {
        $rolePermissions = [
            'Demo Counselor' => ['Manage Leads', 'Manage Student Documents', 'Manage Admissions', 'Manage Consultations', 'Manage Free Consultations'],
            'Demo Operations' => ['Manage Students', 'Manage Service', 'Manage Consultations', 'Manage Free Consultations', 'Manage Leads', 'Manage Admissions', 'Manage Student Documents'],
            'Demo Instructor' => ['Manage Courses'],
            'Demo Finance' => ['Manage Transaction'],
            'Demo CMS Editor' => ['Manage Cms Settings', 'Manage Events'],
        ];

        foreach ($rolePermissions as $name => $permissions) {
            $role = Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
            $role->status = STATUS_ACTIVE;
            $role->save();
            $role->syncPermissions($permissions);
        }

        foreach ([
            'demo.counselor@genzconsultants.example' => 'Demo Counselor',
            'demo.advisor@genzconsultants.example' => 'Demo Operations',
            'demo.instructor@genzconsultants.example' => 'Demo Instructor',
            'demo.finance@genzconsultants.example' => 'Demo Finance',
            'demo.editor@genzconsultants.example' => 'Demo CMS Editor',
        ] as $email => $roleName) {
            $user = User::where('email', $email)->first();
            if ($user) $user->syncRoles([$roleName]);
        }
    }

    private function seedDemoCmsAndSubjects(): void
    {
        if (Schema::hasTable('faqs')) foreach ([
            ['How does a demo consultation work?', 'A sample request is reviewed by the team, then a counselor can arrange a suitable consultation.'],
            ['What documents might an applicant prepare?', 'The checklist depends on the program. A counselor reviews the applicant’s individual requirements.'],
            ['Are the programs and awards shown here real offers?', 'No. Records marked Demo are fictional samples for local product evaluation.'],
        ] as [$question, $answer]) {
            DB::table('faqs')->updateOrInsert(['question' => $question], [
                'answer' => $answer, 'status' => STATUS_ACTIVE, 'deleted_at' => null,
                'updated_at' => now(), 'created_at' => now(),
            ]);
        }

        if (Schema::hasTable('contact_us')) foreach ([
            ['Demo Visitor', 'One', 'demo.enquiry.1@genzconsultants.example', '+92 300 000 0101', 'Fictional website enquiry for presentation use.'],
            ['Demo Visitor', 'Two', 'demo.enquiry.2@genzconsultants.example', '+92 300 000 0102', 'Sample question about preparing for a study-abroad consultation.'],
        ] as [$first, $last, $email, $mobile, $message]) {
            DB::table('contact_us')->updateOrInsert(['email' => $email], [
                'first_name' => $first, 'last_name' => $last, 'mobile' => $mobile,
                'message' => $message, 'deleted_at' => null, 'updated_at' => now(), 'created_at' => now(),
            ]);
        }

        if (!Schema::hasTable('subjects')) return;
        $countryId = DB::table('countries')->where('slug', 'demo-canada')->value('id');
        $universityId = DB::table('universities')->where('slug', 'demo-north-university')->value('id');
        $categoryId = DB::table('subject_categories')->where('name', 'Digital Skills')->value('id');
        $levelId = DB::table('study_levels')->where('name', 'Demo Postgraduate')->value('id');
        foreach ([
            ['Demo Data Science', 'demo-data-science-subject', 'Fictional sample subject for demonstrating the study-abroad catalogue.'],
            ['Demo Global Business', 'demo-global-business-subject', 'Fictional sample subject for demonstrating the study-abroad catalogue.'],
            ['Demo English Preparation', 'demo-english-preparation-subject', 'Fictional sample subject for demonstrating the study-abroad catalogue.'],
        ] as [$name, $slug, $details]) {
            DB::table('subjects')->updateOrInsert(['slug' => $slug], [
                'name' => $name, 'details' => $details, 'university_id' => $universityId,
                'subject_category_id' => $categoryId, 'country_id' => $countryId, 'study_level_id' => $levelId,
                'intake_time' => 'Demo Fall Intake', 'duration' => '2 years', 'requirement_program' => 'Demo entry requirement',
                'requirement_score' => 'See fictional demo program details', 'amount' => null,
                'status' => STATUS_ACTIVE, 'deleted_at' => null, 'updated_at' => now(), 'created_at' => now(),
            ]);
        }
    }

    private function seedDemoStudentProfileAndDocument(): void
    {
        $student = User::where('email', 'demo.learner.amina@genzconsultants.example')->first();
        if (!$student || !Schema::hasTable('student_profiles') || !Schema::hasTable('student_documents')) return;

        $countryId = DB::table('countries')->where('slug', 'demo-canada')->value('id');
        $universityId = DB::table('universities')->where('slug', 'demo-north-university')->value('id');
        $subjectId = DB::table('subjects')->where('slug', 'demo-data-science-subject')->value('id');
        DB::table('student_profiles')->updateOrInsert(['user_id' => $student->id], [
            'date_of_birth' => '2002-04-12', 'gender' => 'not_specified', 'nationality' => 'Demo / fictional',
            'passport_number' => null, 'present_address' => 'Fictional demo address, Lahore',
            'permanent_address' => 'Fictional demo address, Lahore',
            'preferred_country_ids' => json_encode(array_values(array_filter([$countryId]))),
            'preferred_university_ids' => json_encode(array_values(array_filter([$universityId]))),
            'preferred_subject_ids' => json_encode(array_values(array_filter([$subjectId]))),
            'target_intake' => 'Demo Fall 2027', 'updated_at' => now(), 'created_at' => now(),
        ]);

        $path = 'demo/students/amina-khan/sample-transcript.txt';
        $contents = "FICTIONAL DEMO DOCUMENT\nThis sample transcript is placeholder content for local presentation only.\nNo real academic or personal information is included.\n";
        Storage::disk('private')->put($path, Crypt::encryptString($contents));
        DB::table('student_documents')->updateOrInsert(['storage_path' => $path], [
            'user_id' => $student->id, 'replaces_document_id' => null, 'category' => 'academic_transcript',
            'original_name' => 'demo-sample-transcript.txt', 'mime_type' => 'text/plain',
            'size_bytes' => strlen($contents), 'status' => 'pending', 'reviewed_by' => null,
            'reviewed_at' => null, 'review_note' => null, 'updated_at' => now(), 'created_at' => now(),
        ]);
    }

    private function seedDemoLmsAssessments(): void
    {
        $course = Course::where('slug', 'demo-ielts-speaking-listening')->first();
        $instructor = User::where('email', 'demo.instructor@genzconsultants.example')->first();
        if (!$course || !$instructor) return;

        $lessonId = DB::table('course_lessons')->where('course_id', $course->id)->where('title', 'Demo: Getting started')->value('id');
        $now = now();
        $quizId = DB::table('lms_quizzes')->updateOrInsert(['course_id' => $course->id, 'title' => 'Demo: Listening Check'], [
            'course_lesson_id' => $lessonId, 'description' => 'Fictional practice quiz for local demonstration.',
            'status' => 'published', 'opens_at' => $now->copy()->subDay(), 'closes_at' => $now->copy()->addDays(90),
            'time_limit_seconds' => 900, 'max_attempts' => 2, 'pass_percent' => 60, 'shuffle_questions' => true,
            'manual_result_release' => false, 'created_by' => $instructor->id, 'updated_at' => $now, 'created_at' => $now,
        ]);
        $quizId = DB::table('lms_quizzes')->where('course_id', $course->id)->where('title', 'Demo: Listening Check')->value('id');

        $bankId = DB::table('lms_question_banks')->updateOrInsert(['course_id' => $course->id, 'title' => 'Demo: Listening Questions'], [
            'created_by' => $instructor->id, 'updated_at' => $now, 'created_at' => $now,
        ]);
        $bankId = DB::table('lms_question_banks')->where('course_id', $course->id)->where('title', 'Demo: Listening Questions')->value('id');
        DB::table('lms_questions')->updateOrInsert(['course_id' => $course->id, 'question_text' => 'Which skill helps identify the speaker’s main idea?'], [
            'question_bank_id' => $bankId, 'question_type' => 'single_choice', 'explanation' => 'Listen for repeated key points and the speaker’s purpose.',
            'points' => 1, 'status' => 'published', 'created_by' => $instructor->id, 'deleted_at' => null,
            'updated_at' => $now, 'created_at' => $now,
        ]);
        $questionId = DB::table('lms_questions')->where('course_id', $course->id)->where('question_text', 'Which skill helps identify the speaker’s main idea?')->value('id');
        foreach ([['Listen for repeated key points', true], ['Focus only on the final word', false], ['Ignore changes in tone', false]] as $order => [$text, $correct]) {
            DB::table('lms_question_options')->updateOrInsert(['lms_question_id' => $questionId, 'option_text' => $text], [
                'is_correct' => $correct, 'sort_order' => $order + 1, 'updated_at' => $now, 'created_at' => $now,
            ]);
        }
        DB::table('lms_quiz_questions')->updateOrInsert(['lms_quiz_id' => $quizId, 'lms_question_id' => $questionId], [
            'sort_order' => 1, 'updated_at' => $now, 'created_at' => $now,
        ]);

        $assignmentId = DB::table('lms_assignments')->updateOrInsert(['course_id' => $course->id, 'title' => 'Demo: Listening Reflection'], [
            'course_lesson_id' => $lessonId, 'instructions' => 'Write a short reflection on the main idea in a sample listening passage.',
            'due_at' => $now->copy()->addDays(14), 'max_points' => 100, 'max_submissions' => 2,
            'late_policy' => 'accept', 'late_penalty_percent' => 0, 'status' => 'published',
            'created_by' => $instructor->id, 'deleted_at' => null, 'updated_at' => $now, 'created_at' => $now,
        ]);
        $assignmentId = DB::table('lms_assignments')->where('course_id', $course->id)->where('title', 'Demo: Listening Reflection')->value('id');

        $learner = User::where('email', 'demo.learner.amina@genzconsultants.example')->first();
        $enrollment = $learner ? DB::table('course_enrollments')->where('user_id', $learner->id)->where('course_id', $course->id)->first() : null;
        if ($enrollment) {
            DB::table('lms_quiz_attempts')->updateOrInsert(['lms_quiz_id' => $quizId, 'course_enrollment_id' => $enrollment->id, 'attempt_number' => 1], [
                'status' => 'submitted', 'question_order' => json_encode([$questionId]), 'started_at' => $now->copy()->subHours(2),
                'deadline_at' => null, 'submitted_at' => $now->copy()->subHour(), 'score' => 1, 'total_points' => 1,
                'percentage' => 100, 'passed' => true, 'result_released_at' => $now->copy()->subHour(),
                'updated_at' => $now, 'created_at' => $now,
            ]);
            $attemptId = DB::table('lms_quiz_attempts')->where('lms_quiz_id', $quizId)->where('course_enrollment_id', $enrollment->id)->where('attempt_number', 1)->value('id');
            $correctOptionId = DB::table('lms_question_options')->where('lms_question_id', $questionId)->where('is_correct', true)->value('id');
            DB::table('lms_answers')->updateOrInsert(['lms_quiz_attempt_id' => $attemptId, 'lms_question_id' => $questionId], [
                'selected_option_ids' => json_encode(array_values(array_filter([$correctOptionId]))), 'awarded_points' => 1,
                'updated_at' => $now, 'created_at' => $now,
            ]);
            DB::table('lms_assignment_submissions')->updateOrInsert(['lms_assignment_id' => $assignmentId, 'course_enrollment_id' => $enrollment->id, 'version' => 1], [
                'user_id' => $learner->id, 'status' => 'submitted', 'text_response' => 'Demo response: I listened for repeated ideas and summarized the speaker’s purpose.',
                'file_manager_id' => null, 'is_late' => false, 'submitted_at' => $now->copy()->subHours(3),
                'updated_at' => $now, 'created_at' => $now,
            ]);
        }

        CourseLiveSession::updateOrCreate(['course_id' => $course->id, 'title' => 'Demo: Live Study Skills Q&A'], [
            'course_batch_id' => null, 'description' => 'Fictional scheduled session. No external meeting provider is connected.',
            'provider' => 'external', 'meeting_url' => null, 'recording_url' => null,
            'starts_at' => $now->copy()->addDays(5)->setTime(15, 0), 'duration_minutes' => 45,
            'timezone' => 'Asia/Karachi', 'status' => 'scheduled', 'schedule_revision' => 1,
            'created_by' => $instructor->id,
        ]);
    }
}
