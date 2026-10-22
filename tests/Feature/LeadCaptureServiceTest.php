<?php

namespace Tests\Feature;

use App\Services\LeadCaptureService;
use App\Models\User;
use App\Http\Controllers\Admin\LeadController;
use App\Models\StudentProfile;
use App\Models\StudentDocument;
use App\Http\Controllers\Student\StudentDocumentController;
use App\Http\Controllers\Student\OnboardingController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Models\FileManager;
use App\Models\Meta;
use App\Models\Course;
use App\Services\LmsEnrollmentService;
use App\Services\LmsCertificateService;
use App\Models\LmsCertificate;
use App\Services\NotificationDeliveryService;
use Tests\TestCase;

class LeadCaptureServiceTest extends TestCase
{
    private string $originalConnection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalConnection = config('database.default');
        config(['database.connections.sqlite.database' => ':memory:', 'database.default' => 'sqlite']);
        DB::purge('sqlite');
        Schema::create('users', function ($table) {
            $table->id(); $table->string('first_name'); $table->string('last_name')->nullable();
            $table->string('email')->nullable(); $table->unsignedInteger('role'); $table->unsignedInteger('status')->default(1);
            $table->timestamps(); $table->softDeletes();
        });
        Schema::create('consultation_slots', function ($table) {
            $table->id(); $table->string('start_time'); $table->string('end_time'); $table->tinyInteger('status')->default(1); $table->timestamps(); $table->softDeletes();
        });
        Schema::create('consultation_slot_user', function ($table) { $table->unsignedBigInteger('consultation_slot_id'); $table->unsignedBigInteger('user_id'); });
        Schema::create('appointments', function ($table) {
            $table->id(); $table->string('appointment_ID')->nullable(); $table->unsignedBigInteger('user_id'); $table->unsignedBigInteger('consulter_id');
            $table->unsignedBigInteger('consultation_slot_id'); $table->date('date'); $table->tinyInteger('consultation_type'); $table->string('consultation_link')->nullable();
            $table->tinyInteger('status')->default(1); $table->tinyInteger('payment_status')->default(0); $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps(); $table->softDeletes();
        });
        foreach ([
            '2022_06_25_104329_create_countries_table.php',
            '2024_10_15_092535_create_study_levels_table.php',
            '2024_10_15_104329_create_universities_table.php',
            '2024_10_22_060625_create_scholarships_table.php',
            '2024_10_22_105941_create_subjects_table.php',
        ] as $legacyMigration) {
            $migration = require database_path('migrations/'.$legacyMigration);
            $migration->up();
        }
        foreach (['2024_10_17_124115_create_programs_table.php','2024_10_19_092617_create_courses_table.php'] as $courseMigration) {
            $migration = require database_path('migrations/'.$courseMigration);
            $migration->up();
        }
        foreach (['2024_10_19_092636_create_course_lessons_table.php','2024_10_19_092641_create_course_lectures_table.php','2024_10_19_092642_create_course_enrollments_table.php','2024_10_21_094227_create_course_resources_table.php','2024_10_19_092735_create_course_lecture_finishes_table.php'] as $lmsMigration) {
            $migration = require database_path('migrations/'.$lmsMigration);
            $migration->up();
        }
        $migration = require database_path('migrations/2026_10_13_000001_create_crm_lead_tables.php');
        $migration->up();
        $registrationMigration = require database_path('migrations/2026_10_13_000015_create_study_abroad_registrations_table.php');
        $registrationMigration->up();
        $studentMigration = require database_path('migrations/2026_10_13_000002_create_student_crm_profile_documents.php');
        $studentMigration->up();
        $fileMigration = require database_path('migrations/2023_01_05_092212_create_file_managers_table.php');
        $fileMigration->up();
        $basicInfoMigration = require database_path('migrations/2024_11_09_061819_create_student_basic_infos_table.php');
        $basicInfoMigration->up();
        $metaMigration = require database_path('migrations/2023_08_26_075204_create_metas_table.php');
        $metaMigration->up();
        $catalogMigration = require database_path('migrations/2026_10_13_000003_create_academic_catalog_extensions.php');
        $catalogMigration->up();
        $admissionsMigration = require database_path('migrations/2026_10_13_000004_create_admissions_pipeline.php');
        $admissionsMigration->up();
        $settingsMigration = require database_path('migrations/2022_06_23_121213_create_settings_table.php');
        $settingsMigration->up();
        $consultationMigration = require database_path('migrations/2026_10_13_000005_extend_consultation_booking_safety.php');
        $consultationMigration->up();
        $lmsMigration = require database_path('migrations/2026_10_13_000006_add_lms_course_catalog_fields.php');
        $lmsMigration->up();
        $curriculumMigration = require database_path('migrations/2026_10_13_000007_extend_lms_curriculum_security.php');
        $curriculumMigration->up();
        $notificationMigration = require database_path('migrations/2023_07_09_100721_create_notifications_table.php');
        $notificationMigration->up();
        $taskMigration = require database_path('migrations/2026_10_13_000008_extend_crm_follow_up_collaboration.php');
        $taskMigration->up();
        $enrollmentMigration = require database_path('migrations/2026_10_13_000009_create_lms_enrollment_progress_batches.php');
        $enrollmentMigration->up();
        $liveMigration = require database_path('migrations/2026_10_13_000010_create_lms_live_sessions_attendance.php');
        $liveMigration->up();
        $quizMigration = require database_path('migrations/2026_10_13_000011_create_lms_quiz_engine.php');
        $quizMigration->up();
        $assignmentMigration = require database_path('migrations/2026_10_13_000012_create_lms_assignments_grading.php');
        $assignmentMigration->up();
        $certificateMigration = require database_path('migrations/2026_10_13_000013_create_lms_certificates.php');
        $certificateMigration->up();
        $notificationPreferenceMigration = require database_path('migrations/2026_10_13_000014_create_notification_delivery_preferences.php');
        $notificationPreferenceMigration->up();
    }

    protected function tearDown(): void
    {
        DB::purge('sqlite');
        config(['database.default' => $this->originalConnection]);
        parent::tearDown();
    }

    public function test_repeat_website_enquiry_merges_by_normalized_email_without_resetting_stage(): void
    {
        $capture = app(LeadCaptureService::class);
        $lead = $capture->capture([
            'first_name' => 'Ayesha', 'last_name' => 'Khan', 'email' => ' AYESHA@example.com ', 'phone' => '+92 300 123 4567',
        ], 'website_contact', 'form one');
        $lead->update(['status' => 'contacted', 'assigned_to' => 45]);

        $repeat = $capture->capture([
            'first_name' => 'Ayesha', 'last_name' => 'Khan', 'email' => 'ayesha@example.com', 'phone' => '+92 300 123 4567',
        ], 'free_consultation', 'form two', ['country_ids' => [1, 2]]);

        $this->assertSame($lead->id, $repeat->id);
        $this->assertSame('contacted', $repeat->fresh()->status);
        $this->assertSame(45, $repeat->fresh()->assigned_to);
        $this->assertSame([1, 2], $repeat->fresh()->preferences['country_ids']);
        $this->assertDatabaseCount('crm_leads', 1);
        $this->assertDatabaseCount('crm_lead_activities', 2);
        $this->assertSame('repeat_intake', $repeat->activities()->first()->type);
    }

    public function test_staff_can_only_see_leads_assigned_to_their_account(): void
    {
        DB::table('users')->insert([
            ['id' => 10, 'first_name' => 'Sam', 'last_name' => 'Staff', 'email' => 'sam@example.test', 'role' => USER_ROLE_STAFF, 'status' => STATUS_ACTIVE],
            ['id' => 11, 'first_name' => 'Other', 'last_name' => 'Staff', 'email' => 'other@example.test', 'role' => USER_ROLE_STAFF, 'status' => STATUS_ACTIVE],
        ]);
        $mine = \App\Models\CrmLead::create(['first_name' => 'Visible', 'email' => 'visible@example.test', 'status' => 'new', 'source' => 'manual', 'assigned_to' => 10]);
        $other = \App\Models\CrmLead::create(['first_name' => 'Hidden', 'email' => 'hidden@example.test', 'status' => 'new', 'source' => 'manual', 'assigned_to' => 11]);

        $this->actingAs(User::findOrFail(10));
        $controller = new LeadController();
        $scope = new \ReflectionMethod($controller, 'visibleLeads');
        $scope->setAccessible(true);
        $visibleIds = $scope->invoke($controller)->pluck('id')->all();
        $this->assertSame([$mine->id], $visibleIds);
        $this->assertNotContains($other->id, $visibleIds);
    }

    public function test_student_passport_number_is_encrypted_at_rest(): void
    {
        $profile = StudentProfile::create(['user_id' => 10, 'passport_number' => 'AB1234567']);

        $storedValue = DB::table('student_profiles')->where('id', $profile->id)->value('passport_number');
        $this->assertNotSame('AB1234567', $storedValue);
        $this->assertSame('AB1234567', $profile->fresh()->passport_number);

        $basicInfo = \App\Models\StudentBasicInfo::create(['user_id' => 10, 'student_service_order_id' => 20, 'passport_number' => 'XY7654321']);
        $legacyStoredValue = DB::table('student_basic_infos')->where('id', $basicInfo->id)->value('passport_number');
        $this->assertNotSame('XY7654321', $legacyStoredValue);
        $this->assertSame('XY7654321', $basicInfo->fresh()->passport_number);
    }

    public function test_student_documents_are_encrypted_private_and_owner_scoped(): void
    {
        DB::table('users')->insert([
            ['id' => 10, 'first_name' => 'Student', 'last_name' => 'One', 'email' => 'student@example.test', 'role' => USER_ROLE_STUDENT, 'status' => STATUS_ACTIVE],
            ['id' => 11, 'first_name' => 'Student', 'last_name' => 'Two', 'email' => 'other@example.test', 'role' => USER_ROLE_STUDENT, 'status' => STATUS_ACTIVE],
        ]);
        Storage::fake('private');
        $contents = "%PDF-1.4\\nPrivate record\\n";
        $file = UploadedFile::fake()->createWithContent('passport.pdf', $contents);
        $this->actingAs(User::findOrFail(10));
        $request = Request::create('/student/my-study-profile/documents', 'POST', [], [], ['document' => $file]);
        $request->merge(['category' => 'passport']);
        (new StudentDocumentController())->store($request);

        $document = StudentDocument::firstOrFail();
        $encrypted = Storage::disk('private')->get($document->storage_path);
        $this->assertNotSame($contents, $encrypted);
        $this->assertDatabaseHas('student_documents', ['user_id' => 10, 'status' => 'pending']);
        $download = (new StudentDocumentController())->download($document);
        $this->assertSame($contents, $download->getContent());

        $this->actingAs(User::findOrFail(11));
        try {
            (new StudentDocumentController())->download($document);
            $this->fail('A different student must not download this file.');
        } catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $exception) {
            $this->assertTrue(true);
        }
    }

    public function test_legacy_onboarding_uploads_use_encrypted_private_file_manager_storage(): void
    {
        DB::table('users')->insert(['id' => 10, 'first_name' => 'Student', 'last_name' => 'One', 'email' => 'student@example.test', 'role' => USER_ROLE_STUDENT, 'status' => STATUS_ACTIVE]);
        Storage::fake('private');
        $contents = "%PDF-1.4" . chr(10) . 'Legacy private file';
        $file = UploadedFile::fake()->createWithContent('transcript.pdf', $contents);
        $controller = new OnboardingController();
        $store = new \ReflectionMethod($controller, 'storePrivateOnboardingFile');
        $store->setAccessible(true);
        $fileRecord = $store->invoke($controller, $file, 10);

        $this->assertInstanceOf(FileManager::class, $fileRecord);
        $this->assertSame('private', $fileRecord->storage_type);
        $this->assertNotSame($contents, Storage::disk('private')->get($fileRecord->path));
        $this->actingAs(User::findOrFail(10));
        $response = (new StudentDocumentController())->downloadLegacyFile($fileRecord);
        $this->assertSame($contents, $response->getContent());
    }

    public function test_public_meta_lookup_uses_the_requested_page_record(): void
    {
        Meta::create(['slug' => 'universities', 'page_name' => 'Universities', 'meta_title' => 'Study options worldwide', 'meta_description' => 'Explore university options.']);

        $meta = getMeta('universities');

        $this->assertSame('Study options worldwide', $meta['meta_title']);
        $this->assertSame('Explore university options.', $meta['meta_description']);
        $this->assertSame('home', getMetaSlugForCurrentPage());
    }

    public function test_academic_catalog_migration_creates_separate_program_and_intake_domain(): void
    {
        $this->assertTrue(Schema::hasTable('academic_programs'));
        $this->assertTrue(Schema::hasTable('program_intakes'));
        $this->assertTrue(Schema::hasTable('student_saved_options'));
        $this->assertTrue(Schema::hasColumn('universities', 'last_verified_at'));
        $this->assertTrue(Schema::hasColumn('scholarships', 'eligibility_conditions'));

        DB::table('countries')->insert(['name' => 'Demo Destination', 'slug' => 'demo-destination', 'status' => STATUS_ACTIVE, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('study_levels')->insert(['name' => 'Masters', 'status' => STATUS_ACTIVE, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('universities')->insert(['name' => 'Demo University', 'slug' => 'demo-university', 'country_id' => '1', 'avg_cost' => 0, 'status' => STATUS_ACTIVE, 'created_at' => now(), 'updated_at' => now()]);
        $program = \App\Models\AcademicProgram::create([
            'university_id' => 1, 'country_id' => 1, 'study_level_id' => 1,
            'title' => 'Demo Masters Program', 'slug' => 'demo-masters-program',
            'tuition_amount' => 25000, 'tuition_currency' => 'USD', 'source_url' => 'https://university.example/program',
            'last_verified_at' => now(), 'status' => 'published',
        ]);
        $program->intakes()->create(['label' => 'Fall Demo', 'status' => 'open']);

        $this->assertSame('Demo Masters Program', $program->fresh()->title);
        $this->assertSame('Fall Demo', $program->fresh()->intakes->first()->label);
        $this->assertSame('25000.00', $program->fresh()->tuition_amount);
    }

    public function test_admissions_workflow_records_valid_transitions_and_prevents_active_duplicates(): void
    {
        DB::table('countries')->insert(['name' => 'Destination', 'slug' => 'destination', 'status' => STATUS_ACTIVE, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('universities')->insert(['name' => 'University', 'slug' => 'university', 'country_id' => '1', 'avg_cost' => 0, 'status' => STATUS_ACTIVE, 'created_at' => now(), 'updated_at' => now()]);
        $program = \App\Models\AcademicProgram::create(['university_id' => 1, 'country_id' => 1, 'title' => 'Program', 'slug' => 'program', 'status' => 'published', 'source_url' => 'https://university.example/program', 'last_verified_at' => now()]);
        DB::table('users')->insert(['id' => 31, 'first_name' => 'Student', 'last_name' => 'Demo', 'email' => 'student31@example.test', 'role' => USER_ROLE_STUDENT, 'status' => STATUS_ACTIVE]);
        $student = User::findOrFail(31);
        $workflow = app(\App\Services\AdmissionsWorkflow::class);
        $application = $workflow->createForStudent($student, $program, null);
        $this->assertStringStartsWith('GZ-'.now()->format('Y').'-', $application->reference_code);
        $this->assertDatabaseHas('application_status_history', ['admission_application_id' => $application->id, 'to_status' => 'draft']);

        try {
            $workflow->createForStudent($student, $program, null);
            $this->fail('A duplicate active program application must be rejected.');
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->assertArrayHasKey('program_id', $exception->errors());
        }

        $workflow->transition($application, 'document_collection', $student);
        $workflow->transition($application, 'internal_review', $student);
        $workflow->transition($application, 'ready_to_submit', $student);
        $submitted = $workflow->transition($application, 'submitted', $student);
        $this->assertSame('submitted', $submitted->status);
        $this->assertNotNull($submitted->submitted_at);
        $this->assertDatabaseCount('application_status_history', 5);
    }

    public function test_consultation_booking_is_slot_scoped_atomic_and_cancellable_with_notice(): void
    {
        DB::table('users')->insert([
            ['id' => 40, 'first_name' => 'Consultant', 'last_name' => 'Demo', 'email' => 'consultant40@example.test', 'role' => USER_ROLE_CONSULTANT, 'status' => STATUS_ACTIVE, 'consultation_timezone' => 'Asia/Karachi'],
            ['id' => 41, 'first_name' => 'Student', 'last_name' => 'One', 'email' => 'student41@example.test', 'role' => USER_ROLE_STUDENT, 'status' => STATUS_ACTIVE, 'consultation_timezone' => null],
            ['id' => 42, 'first_name' => 'Student', 'last_name' => 'Two', 'email' => 'student42@example.test', 'role' => USER_ROLE_STUDENT, 'status' => STATUS_ACTIVE, 'consultation_timezone' => null],
        ]);
        $slot = \App\Models\ConsultationSlot::create(['start_time' => '10:00', 'end_time' => '10:30', 'status' => STATUS_ACTIVE]);
        DB::table('consultation_slot_user')->insert(['consultation_slot_id' => $slot->id, 'user_id' => 40]);
        $date = now('Asia/Karachi')->addDays(3)->toDateString();
        $service = app(\App\Services\ConsultationBookingService::class);
        $appointment = $service->book(41, 40, $slot->id, $date, CONSULTATION_TYPE_VIRTUAL, PAYMENT_STATUS_PAID);

        $this->assertSame('Asia/Karachi', $appointment->consultation_timezone);
        $this->assertDatabaseHas('consultation_booking_events', ['appointment_id' => $appointment->id, 'event_type' => 'booked']);
        try {
            $service->checkAvailability(40, $slot->id, $date, CONSULTATION_TYPE_VIRTUAL);
            $this->fail('A booked consultant slot must no longer be available.');
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->assertArrayHasKey('consultation_slot_id', $exception->errors());
        }

        $service->cancel($appointment, User::findOrFail(41), 'Schedule changed');
        $this->assertSame(STATUS_REJECT, $appointment->fresh()->status);
        $second = $service->book(42, 40, $slot->id, $date, CONSULTATION_TYPE_VIRTUAL, PAYMENT_STATUS_PAID);
        $this->assertNotSame($appointment->id, $second->id);
    }

    public function test_lms_catalog_fields_keep_free_courses_and_separate_publication_from_legacy_status(): void
    {
        DB::table('programs')->insert(['id' => 1, 'title' => 'Online Learning', 'slug' => 'online-learning', 'top_section' => '{}', 'step_section' => '{}', 'created_by' => 1, 'status' => STATUS_ACTIVE, 'created_at' => now(), 'updated_at' => now()]);
        $course = \App\Models\Course::create([
            'program_id' => 1, 'title' => 'Free English Preparation', 'slug' => 'free-english-preparation',
            'duration' => 12, 'price' => 0, 'faqs' => [], 'instructors' => [], 'created_by' => 1,
            'status' => STATUS_DEACTIVATE, 'publication_state' => 'review', 'difficulty' => 'beginner', 'language' => 'English',
            'estimated_minutes' => 720, 'prerequisites' => [],
        ]);
        $this->assertSame('0.00', $course->fresh()->price);
        $this->assertSame('review', $course->fresh()->publication_state);
        $this->assertSame([], $course->fresh()->prerequisites);
        $this->assertNotSame(STATUS_ACTIVE, $course->fresh()->status);
    }

    public function test_public_course_scope_only_returns_active_published_courses(): void
    {
        DB::table('programs')->insert(['id' => 1, 'title' => 'Online Learning', 'slug' => 'online-learning', 'top_section' => '{}', 'step_section' => '{}', 'created_by' => 1, 'status' => STATUS_ACTIVE, 'created_at' => now(), 'updated_at' => now()]);
        $base = ['program_id' => 1, 'duration' => 4, 'price' => 0, 'faqs' => [], 'instructors' => [], 'created_by' => 1];
        \App\Models\Course::create($base + ['title' => 'Published course', 'slug' => 'published-course', 'status' => STATUS_ACTIVE, 'publication_state' => 'published']);
        \App\Models\Course::create($base + ['title' => 'Draft course', 'slug' => 'draft-course', 'status' => STATUS_ACTIVE, 'publication_state' => 'draft']);
        \App\Models\Course::create($base + ['title' => 'Review course', 'slug' => 'review-course', 'status' => STATUS_DEACTIVATE, 'publication_state' => 'review']);

        $this->assertSame(['Published course'], \App\Models\Course::publiclyVisible()->pluck('title')->all());
    }

    public function test_lms_student_content_is_enrollment_scoped_and_get_does_not_mark_progress(): void
    {
        DB::table('programs')->insert(['id' => 1, 'title' => 'Online Learning', 'slug' => 'online-learning', 'top_section' => '{}', 'step_section' => '{}', 'created_by' => 1, 'status' => STATUS_ACTIVE, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('users')->insert(['id' => 50, 'first_name' => 'Learner', 'last_name' => 'Demo', 'email' => 'learner@example.test', 'role' => USER_ROLE_STUDENT, 'status' => STATUS_ACTIVE]);
        $course = \App\Models\Course::create(['program_id' => 1, 'title' => 'Secure learning', 'slug' => 'secure-learning', 'duration' => 3, 'price' => 0, 'faqs' => [], 'instructors' => [], 'created_by' => 1, 'status' => STATUS_DEACTIVATE, 'publication_state' => 'draft']);
        $enrollment = \App\Models\CourseEnrollment::create(['user_id' => 50, 'course_id' => $course->id, 'status' => STATUS_ACTIVE]);
        $lesson = \App\Models\CourseLesson::create(['course_id' => $course->id, 'title' => 'Week one', 'description' => 'A sample lesson description.', 'sort_order' => 1, 'publication_state' => 'published']);
        $lecture = \App\Models\CourseLecture::create(['course_id' => $course->id, 'course_lesson_id' => $lesson->id, 'title' => 'Welcome', 'sort_order' => 1, 'publication_state' => 'published']);
        $resource = \App\Models\CourseResource::create(['course_id' => $course->id, 'course_lesson_id' => $lesson->id, 'course_lecture_id' => $lecture->id, 'title' => 'Introduction', 'resource_type' => RESOURCE_TYPE_YOUTUBE_ID, 'duration' => 60, 'resource' => 'example123', 'publication_state' => 'published']);
        \App\Models\CourseResource::create(['course_id' => $course->id, 'course_lesson_id' => $lesson->id, 'course_lecture_id' => $lecture->id, 'title' => 'Hidden draft', 'resource_type' => RESOURCE_TYPE_YOUTUBE_ID, 'duration' => 30, 'resource' => 'draft123', 'publication_state' => 'draft']);
        $this->actingAs(User::findOrFail(50));

        $page = (new \App\Http\Controllers\Student\MyCourseController())->view(Request::create('/student/my-courses/view'), encodeId($enrollment->id));
        $this->assertSame([$resource->id], $page->getData()['course']->lessons->first()->lectures->first()->resources->pluck('id')->all());
        $this->assertDatabaseCount('course_lecture_finishes', 0, 'sqlite');

        $controller = new \App\Http\Controllers\Student\MyCourseController();
        $controller->completeResource(Request::create('/', 'POST'), encodeId($enrollment->id), encodeId($resource->id));
        $controller->completeResource(Request::create('/', 'POST'), encodeId($enrollment->id), encodeId($resource->id));
        $this->assertDatabaseCount('course_lecture_finishes', 1, 'sqlite');
        $this->assertSame(100, app(\App\Services\LmsEnrollmentService::class)->completionPercent($enrollment));
        $this->assertDatabaseHas('lms_lesson_progress', ['course_enrollment_id'=>$enrollment->id,'course_resource_id'=>$resource->id]);

        try {
            $controller->view(Request::create('/student/my-courses/view', 'GET', ['resource_id' => encodeId($resource->id + 999)]), encodeId($enrollment->id));
            $this->fail('A resource outside the enrollment course must not be selectable.');
        } catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $exception) {
            $this->assertTrue(true);
        }
    }

    public function test_lms_curriculum_updates_keep_an_auditable_revision(): void
    {
        DB::table('programs')->insert(['id' => 1, 'title' => 'Online Learning', 'slug' => 'online-learning', 'top_section' => '{}', 'step_section' => '{}', 'created_by' => 1, 'status' => STATUS_ACTIVE, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('users')->insert(['id' => 60, 'first_name' => 'Admin', 'last_name' => 'Demo', 'email' => 'admin@example.test', 'role' => USER_ROLE_ADMIN, 'status' => STATUS_ACTIVE]);
        $course = \App\Models\Course::create(['program_id' => 1, 'title' => 'Revision course', 'slug' => 'revision-course', 'duration' => 3, 'price' => 0, 'faqs' => [], 'instructors' => [], 'created_by' => 60, 'status' => STATUS_ACTIVE, 'publication_state' => 'published']);
        $this->actingAs(User::findOrFail(60));
        $controller = new \App\Http\Controllers\Admin\Course\CourseLessonController();
        $create = Request::create('/', 'POST', ['title' => 'First section', 'description' => 'This is a sufficiently long description.', 'sort_order' => 2, 'lesson_type' => 'reading', 'publication_state' => 'published']);
        $controller->store($create, $course->id);
        $lesson = \App\Models\CourseLesson::firstOrFail();
        $update = Request::create('/', 'POST', ['id' => $lesson->id, 'title' => 'Renamed section', 'description' => 'This updated description remains long enough.', 'sort_order' => 1, 'lesson_type' => 'text', 'publication_state' => 'published']);
        $controller->store($update, $course->id);

        $this->assertSame('Renamed section', $lesson->fresh()->title);
        $this->assertSame(1, $lesson->fresh()->sort_order);
        $this->assertDatabaseHas('course_content_revisions', ['content_type' => 'lesson', 'content_id' => $lesson->id, 'revision_number' => 1]);
    }

    public function test_crm_task_notification_outbox_is_deduplicated_and_retry_safe(): void
    {
        $outbox = app(\App\Services\CrmTaskNotificationOutbox::class);
        $outbox->enqueue('crm-follow-up-created:77:10', 10, 'Assigned task', 'Call the applicant', '/admin/crm/leads/1');
        $outbox->enqueue('crm-follow-up-created:77:10', 10, 'Assigned task', 'Call the applicant', '/admin/crm/leads/1');

        $this->assertDatabaseCount('crm_task_notification_outbox', 1);
        $this->assertSame(1, $outbox->deliverPending());
        $this->assertSame(0, $outbox->deliverPending());
        $this->assertDatabaseCount('notifications', 1);
        $this->assertDatabaseHas('notifications', ['dedupe_key' => 'crm-follow-up-created:77:10', 'user_id' => 10]);
    }

    public function test_free_lms_enrollment_is_idempotent_capacity_limited_and_expiry_scoped(): void
    {
        DB::table('programs')->insert(['id' => 1, 'title' => 'Online Learning', 'slug' => 'online-learning', 'top_section' => '{}', 'step_section' => '{}', 'created_by' => 1, 'status' => STATUS_ACTIVE, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('users')->insert([
            ['id'=>70,'first_name'=>'One','last_name'=>'Demo','email'=>'one@example.test','role'=>USER_ROLE_STUDENT,'status'=>STATUS_ACTIVE],
            ['id'=>71,'first_name'=>'Two','last_name'=>'Demo','email'=>'two@example.test','role'=>USER_ROLE_STUDENT,'status'=>STATUS_ACTIVE],
        ]);
        $course = \App\Models\Course::create(['program_id'=>1,'title'=>'Free course','slug'=>'free-course','duration'=>5,'price'=>0,'faqs'=>[],'instructors'=>[],'created_by'=>1,'status'=>STATUS_ACTIVE,'publication_state'=>'published','capacity'=>1]);
        $service = app(\App\Services\LmsEnrollmentService::class);
        $first = $service->enroll(70,$course->id,'free');
        $again = $service->enroll(70,$course->id,'free');
        $this->assertSame($first->id,$again->id);
        $this->assertSame('free',$again->fresh()->enrollment_source);
        $this->assertDatabaseCount('course_enrollments',1);
        try {
            $service->enroll(71,$course->id,'free');
            $this->fail('Course enrollment capacity must be enforced.');
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->assertArrayHasKey('course',$exception->errors());
        }
        $first->update(['access_expires_at'=>now()->subMinute()]);
        try {
            $service->activeEnrollment($first->id,70);
            $this->fail('Expired enrollment must not grant course access.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            $this->assertTrue(true);
        }
    }

    public function test_live_class_links_are_encrypted_and_join_requires_active_matching_enrollment(): void
    {
        DB::table('programs')->insert(['id'=>1,'title'=>'Online Learning','slug'=>'online-learning','top_section'=>'{}','step_section'=>'{}','created_by'=>1,'status'=>STATUS_ACTIVE,'created_at'=>now(),'updated_at'=>now()]);
        DB::table('users')->insert(['id'=>80,'first_name'=>'Learner','last_name'=>'One','email'=>'live@example.test','role'=>USER_ROLE_STUDENT,'status'=>STATUS_ACTIVE]);
        $course = \App\Models\Course::create(['program_id'=>1,'title'=>'Live course','slug'=>'live-course','duration'=>3,'price'=>0,'faqs'=>[],'instructors'=>[],'created_by'=>1,'status'=>STATUS_ACTIVE,'publication_state'=>'published']);
        $enrollment = app(\App\Services\LmsEnrollmentService::class)->enroll(80,$course->id,'free');
        $session = \App\Models\CourseLiveSession::create(['course_id'=>$course->id,'title'=>'Orientation','provider'=>'google_meet','meeting_url'=>'https://meet.google.com/example-room','starts_at'=>now()->addMinutes(5),'duration_minutes'=>30,'timezone'=>'Asia/Karachi','status'=>'scheduled']);
        $storedUrl = DB::table('course_live_sessions')->where('id',$session->id)->value('meeting_url');
        $this->assertNotSame('https://meet.google.com/example-room',$storedUrl);
        $this->assertSame('https://meet.google.com/example-room',$session->fresh()->meeting_url);
        $attendance = app(\App\Services\CourseLiveSessionService::class)->join($session,$enrollment);
        $this->assertSame('joined',$attendance->attendance_status);
        $this->assertDatabaseHas('course_live_attendance',['course_live_session_id'=>$session->id,'course_enrollment_id'=>$enrollment->id]);
        $clock = now();
        \Illuminate\Support\Carbon::setTestNow($clock->copy());
        $updatedAttendance = $attendance;
        for ($minute = 1; $minute <= 20; $minute++) {
            \Illuminate\Support\Carbon::setTestNow($clock->copy()->addMinutes($minute));
            $updatedAttendance = app(\App\Services\CourseLiveSessionService::class)->heartbeat($session,$enrollment);
        }
        \Illuminate\Support\Carbon::setTestNow();
        $this->assertSame('attended',$updatedAttendance->attendance_status);
        $this->assertGreaterThanOrEqual(900,$updatedAttendance->attended_seconds);

        $otherCourse = \App\Models\Course::create(['program_id'=>1,'title'=>'Other course','slug'=>'other-live-course','duration'=>3,'price'=>0,'faqs'=>[],'instructors'=>[],'created_by'=>1,'status'=>STATUS_ACTIVE,'publication_state'=>'published']);
        $otherSession = \App\Models\CourseLiveSession::create(['course_id'=>$otherCourse->id,'title'=>'Private class','provider'=>'external','meeting_url'=>'https://example.com/secret','starts_at'=>now()->addMinutes(5),'duration_minutes'=>30,'timezone'=>'UTC','status'=>'scheduled']);
        try {
            app(\App\Services\CourseLiveSessionService::class)->join($otherSession,$enrollment);
            $this->fail('An enrollment in a different course must not join the meeting.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $exception) {
            $this->assertSame(404,$exception->getStatusCode());
        }
    }

    public function test_quiz_attempt_hides_answer_keys_scores_server_side_and_releases_manual_results(): void
    {
        DB::table('programs')->insert(['id'=>1,'title'=>'Online Learning','slug'=>'online-learning','top_section'=>'{}','step_section'=>'{}','created_by'=>1,'status'=>STATUS_ACTIVE,'created_at'=>now(),'updated_at'=>now()]);
        DB::table('users')->insert(['id'=>90,'first_name'=>'Quiz','last_name'=>'Learner','email'=>'quiz@example.test','role'=>USER_ROLE_STUDENT,'status'=>STATUS_ACTIVE]);
        $course = \App\Models\Course::create(['program_id'=>1,'title'=>'Quiz course','slug'=>'quiz-course','duration'=>3,'price'=>0,'faqs'=>[],'instructors'=>[],'created_by'=>1,'status'=>STATUS_ACTIVE,'publication_state'=>'published']);
        $enrollment = app(\App\Services\LmsEnrollmentService::class)->enroll(90,$course->id,'free');
        $quiz = \App\Models\LmsQuiz::create(['course_id'=>$course->id,'title'=>'Safety basics','status'=>'published','time_limit_seconds'=>60,'max_attempts'=>1,'pass_percent'=>60,'shuffle_questions'=>false,'manual_result_release'=>true]);
        $question = \App\Models\LmsQuestion::create(['course_id'=>$course->id,'question_text'=>'Which choice is correct?','question_type'=>'single','points'=>5,'status'=>'published']);
        $correct = $question->options()->create(['option_text'=>'Correct answer','is_correct'=>true,'sort_order'=>1]);
        $question->options()->create(['option_text'=>'Wrong answer','is_correct'=>false,'sort_order'=>2]);
        $quiz->questions()->attach($question->id,['sort_order'=>0]);
        $service = app(\App\Services\LmsQuizService::class);
        $payload = $service->start($quiz,$enrollment->id);
        $this->assertArrayNotHasKey('is_correct',$payload['questions']->first()->options->first()->getAttributes());
        $attempt = $payload['attempt'];
        $submitted = $service->submit($attempt,[$question->id=>[$correct->id]]);
        $this->assertSame(5,$submitted->score);
        $this->assertSame(100.0,(float)$submitted->percentage);
        $this->assertNull($submitted->result_released_at);
        $released = $service->releaseResult($submitted);
        $this->assertNotNull($released->result_released_at);
        $this->assertDatabaseHas('lms_answers',['lms_quiz_attempt_id'=>$attempt->id,'lms_question_id'=>$question->id,'awarded_points'=>5]);
    }

    public function test_quiz_accepts_submit_at_deadline_and_expires_after_deadline(): void
    {
        DB::table('programs')->insert(['id'=>1,'title'=>'Online Learning','slug'=>'online-learning','top_section'=>'{}','step_section'=>'{}','created_by'=>1,'status'=>STATUS_ACTIVE,'created_at'=>now(),'updated_at'=>now()]);
        DB::table('users')->insert(['id'=>91,'first_name'=>'Timed','last_name'=>'Learner','email'=>'timed@example.test','role'=>USER_ROLE_STUDENT,'status'=>STATUS_ACTIVE]);
        $course = \App\Models\Course::create(['program_id'=>1,'title'=>'Timed quiz course','slug'=>'timed-quiz-course','duration'=>3,'price'=>0,'faqs'=>[],'instructors'=>[],'created_by'=>1,'status'=>STATUS_ACTIVE,'publication_state'=>'published']);
        $enrollment = app(\App\Services\LmsEnrollmentService::class)->enroll(91,$course->id,'free');
        $quiz = \App\Models\LmsQuiz::create(['course_id'=>$course->id,'title'=>'Timed','status'=>'published','time_limit_seconds'=>60,'max_attempts'=>2,'pass_percent'=>50,'shuffle_questions'=>false]);
        $question = \App\Models\LmsQuestion::create(['course_id'=>$course->id,'question_text'=>'Question?','question_type'=>'single','points'=>1,'status'=>'published']);
        $option = $question->options()->create(['option_text'=>'Answer','is_correct'=>true]);
        $quiz->questions()->attach($question->id);
        $service = app(\App\Services\LmsQuizService::class);
        $base = now();
        \Illuminate\Support\Carbon::setTestNow($base);
        $first = $service->start($quiz,$enrollment->id)['attempt'];
        \Illuminate\Support\Carbon::setTestNow($first->deadline_at);
        $accepted = $service->submit($first,[$question->id=>[$option->id]]);
        $this->assertSame('submitted',$accepted->status);
        \Illuminate\Support\Carbon::setTestNow($first->deadline_at->copy()->addSecond());
        $second = $service->start($quiz,$enrollment->id)['attempt'];
        \Illuminate\Support\Carbon::setTestNow($second->deadline_at->copy()->addSecond());
        try {
            $service->submit($second,[$question->id=>[$option->id]]);
            $this->fail('A submission after the deadline must be rejected.');
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->assertArrayHasKey('quiz',$exception->errors());
        }
        \Illuminate\Support\Carbon::setTestNow();
        $this->assertSame('expired',$second->fresh()->status);
    }

    public function test_assignment_late_policy_resubmission_penalty_and_grade_audit_are_enforced(): void
    {
        DB::table('programs')->insert(['id'=>1,'title'=>'Online Learning','slug'=>'online-learning','top_section'=>'{}','step_section'=>'{}','created_by'=>1,'status'=>STATUS_ACTIVE,'created_at'=>now(),'updated_at'=>now()]);
        DB::table('users')->insert(['id'=>92,'first_name'=>'Assignment','last_name'=>'Learner','email'=>'assignment@example.test','role'=>USER_ROLE_STUDENT,'status'=>STATUS_ACTIVE]);
        $course = \App\Models\Course::create(['program_id'=>1,'title'=>'Assignment course','slug'=>'assignment-course','duration'=>3,'price'=>0,'faqs'=>[],'instructors'=>[],'created_by'=>1,'status'=>STATUS_ACTIVE,'publication_state'=>'published']);
        $enrollment = app(\App\Services\LmsEnrollmentService::class)->enroll(92,$course->id,'free');
        $assignment = \App\Models\LmsAssignment::create(['course_id'=>$course->id,'title'=>'Essay','due_at'=>now()->subMinute(),'max_points'=>100,'max_submissions'=>2,'late_policy'=>'accept','late_penalty_percent'=>20,'status'=>'published']);
        $service = app(\App\Services\LmsAssignmentService::class);
        $submission = $service->saveSubmission($assignment,$enrollment->id,92,'My submitted work',null,true);
        $this->assertSame('late',$submission->status);
        $this->assertTrue($submission->is_late);
        $grade = $service->grade($submission,1,80,[['criterion'=>'Evidence','score'=>80]],'Good effort',false);
        $this->assertSame(64.0,(float)$grade->score);
        $this->assertNull($grade->released_at);
        $this->assertSame(1,DB::table('lms_grade_history')->count());
        $grade = $service->grade($submission,1,70,[['criterion'=>'Evidence','score'=>70]],'Revised feedback',true);
        $this->assertSame(56.0,(float)$grade->score);
        $this->assertNotNull($grade->released_at);
        $this->assertSame(2,DB::table('lms_grade_history')->count());
        $this->assertNotNull(DB::table('lms_grade_history')->where('lms_grade_id',$grade->id)->orderByDesc('id')->value('before_values'));

        $closed = \App\Models\LmsAssignment::create(['course_id'=>$course->id,'title'=>'Closed task','due_at'=>now()->subDay(),'max_points'=>10,'max_submissions'=>1,'late_policy'=>'reject','status'=>'published']);
        try {
            $service->saveSubmission($closed,$enrollment->id,92,'Too late',null,true);
            $this->fail('Assignments configured to reject late work must reject it.');
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->assertArrayHasKey('submission',$exception->errors());
        }
    }

    public function test_certificates_require_completed_learning_are_idempotent_and_can_be_revoked(): void
    {
        DB::table('programs')->insert(['id'=>1,'title'=>'Online Learning','slug'=>'online-learning','top_section'=>'{}','step_section'=>'{}','created_by'=>1,'status'=>STATUS_ACTIVE,'created_at'=>now(),'updated_at'=>now()]);
        DB::table('users')->insert(['id'=>93,'first_name'=>'Demo','last_name'=>'Learner','email'=>'certificate@example.test','role'=>USER_ROLE_STUDENT,'status'=>STATUS_ACTIVE]);
        $course=Course::create(['program_id'=>1,'title'=>'Certificate course','slug'=>'certificate-course','duration'=>3,'price'=>0,'faqs'=>[],'instructors'=>[],'created_by'=>1,'status'=>STATUS_ACTIVE,'publication_state'=>'published']);
        $enrollment=app(LmsEnrollmentService::class)->enroll(93,$course->id,'free');
        $lesson=DB::table('course_lessons')->insertGetId(['course_id'=>$course->id,'title'=>'Lesson','description'=>'Lesson description','created_at'=>now(),'updated_at'=>now()]);
        $lecture=DB::table('course_lectures')->insertGetId(['course_id'=>$course->id,'course_lesson_id'=>$lesson,'title'=>'Lecture','created_at'=>now(),'updated_at'=>now()]);
        $resource=DB::table('course_resources')->insertGetId(['course_id'=>$course->id,'course_lesson_id'=>$lesson,'course_lecture_id'=>$lecture,'title'=>'Reading','resource_type'=>1,'duration'=>1,'resource'=>'A reading resource','created_at'=>now(),'updated_at'=>now()]);
        $service=app(LmsCertificateService::class);
        $this->assertFalse($service->eligibility($enrollment)['eligible']);
        DB::table('lms_lesson_progress')->insert(['course_enrollment_id'=>$enrollment->id,'course_resource_id'=>$resource,'user_id'=>93,'last_position_seconds'=>0,'time_spent_seconds'=>30,'completed_at'=>now(),'created_at'=>now(),'updated_at'=>now()]);
        $this->assertTrue($service->eligibility($enrollment)['eligible']);
        $certificate=$service->issue($enrollment,1);
        $this->assertSame($certificate->id,$service->issue($enrollment,1)->id);
        $this->assertDatabaseCount('lms_certificates',1);
        $this->assertSame($certificate->certificate_number,LmsCertificate::where('verification_token',$certificate->verification_token)->firstOrFail()->certificate_number);
        $revoked=$service->revoke($certificate,1,'Issued in error');
        $this->assertNotNull($revoked->revoked_at);
        $this->assertSame('Issued in error',$revoked->revocation_reason);
    }

    public function test_generic_notification_outbox_deduplicates_delivery_and_respects_opt_out(): void
    {
        DB::table('users')->insert(['id'=>94,'first_name'=>'Notify','last_name'=>'Learner','email'=>'notify@example.test','role'=>USER_ROLE_STUDENT,'status'=>STATUS_ACTIVE]);
        $service=app(NotificationDeliveryService::class);
        $service->enqueue('test-event-94',94,'test.event','Test update','A demo notification');
        $service->enqueue('test-event-94',94,'test.event','Test update','A demo notification');
        $this->assertSame(1,$service->deliverPending());
        $this->assertSame(0,$service->deliverPending());
        $this->assertDatabaseCount('notifications',1);
        DB::table('notification_preferences')->insert(['user_id'=>94,'event_key'=>'test.optout','channel'=>'in_app','enabled'=>false,'created_at'=>now(),'updated_at'=>now()]);
        $service->enqueue('test-event-optout',94,'test.optout','Should not arrive','Opted out');
        $this->assertDatabaseMissing('notification_outbox',['dedupe_key'=>'test-event-optout']);
    }

    public function test_public_student_registration_persists_private_signature_and_creates_crm_lead(): void
    {
        Storage::fake('local');
        $this->withoutMiddleware();
        $response = $this->post(route('study-abroad-registration.store'), [
            'full_name' => 'Amina Khan', 'father_name' => 'Imran Khan', 'identity_number' => '12345-1234567-1',
            'date_of_birth' => '2002-04-12', 'gender' => 'Female', 'marital_status' => 'Single',
            'phone' => '+923001112233', 'whatsapp' => '+923001112233', 'email' => 'amina-registration@example.test',
            'residential_address' => 'Lahore, Pakistan', 'last_qualification' => 'Intermediate', 'field_of_study' => 'Computer Science',
            'institute_name' => 'Demo College', 'passing_year' => now()->year - 1, 'grade' => '82%',
            'destination' => 'UK', 'branch' => 'Lahore', 'program_type' => 'standard',
            'paid_amount' => '0', 'paid_currency' => 'PKR', 'signed_at' => now()->toDateString(),
            'signature' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+aZioAAAAASUVORK5CYII=',
            'terms_accepted' => '1',
        ]);

        $response->assertRedirect(route('study-abroad-registration.thanks'));
        $this->assertDatabaseCount('study_abroad_registrations', 1);
        $registration = \App\Models\StudyAbroadRegistration::firstOrFail();
        $this->assertSame('12345-1234567-1', $registration->identity_number);
        $this->assertSame('Amina', $registration->lead->first_name);
        $this->assertSame('new', $registration->lead->status);
        $this->assertTrue(Storage::disk('local')->exists($registration->signature_path));
        $this->assertNotSame('12345-1234567-1', DB::table('study_abroad_registrations')->value('identity_number'));
    }

    public function test_registration_rejects_destination_not_in_selected_program_type(): void
    {
        Storage::fake('local');
        $this->withoutMiddleware();
        $response = $this->from(route('study-abroad-registration.create'))->post(route('study-abroad-registration.store'), [
            'full_name' => 'Amina Khan', 'father_name' => 'Imran Khan', 'identity_number' => '12345',
            'date_of_birth' => '2002-04-12', 'gender' => 'Female', 'marital_status' => 'Single',
            'phone' => '+923001112233', 'whatsapp' => '+923001112233', 'email' => 'invalid-program@example.test',
            'residential_address' => 'Lahore', 'last_qualification' => 'Intermediate', 'field_of_study' => 'IT',
            'institute_name' => 'Demo College', 'passing_year' => now()->year - 1, 'grade' => '82%',
            'destination' => 'UK', 'branch' => 'Lahore', 'program_type' => 'scholarship',
            'paid_amount' => 0, 'paid_currency' => 'PKR', 'signed_at' => now()->toDateString(),
            'signature' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+aZioAAAAASUVORK5CYII=',
            'terms_accepted' => '1',
        ]);
        $response->assertSessionHasErrors('destination');
        $this->assertDatabaseCount('study_abroad_registrations', 0);
        $this->assertDatabaseCount('crm_leads', 0);
    }
}
