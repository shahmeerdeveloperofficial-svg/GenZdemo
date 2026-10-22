<?php

namespace Tests\Feature;

use App\Models\StudentDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ListCriteriaTest extends TestCase
{
    use RefreshDatabase;

    private function user(int $role, ?string $branch = null): User
    {
        return User::factory()->create(['role' => $role, 'status' => STATUS_ACTIVE, 'google_auth_status' => 0, 'branch' => $branch]);
    }

    private function columns(array $names): array
    {
        return array_map(fn ($name) => ['data' => $name, 'name' => $name, 'searchable' => 'false', 'orderable' => 'false'], $names);
    }

    public function test_student_filters_apply_before_pagination_and_options_respect_the_student_scope(): void
    {
        $admin = $this->user(USER_ROLE_ADMIN, 'Islamabad');
        $this->user(USER_ROLE_STUDENT, 'Karachi');
        $first = $this->user(USER_ROLE_STUDENT, 'Lahore');
        $this->user(USER_ROLE_STUDENT, 'Lahore');
        $response = $this->actingAs($admin)->getJson(route('admin.students.index', [
            'draw' => 1, 'start' => 0, 'length' => 1,
            'columns' => $this->columns(['name', 'status', 'branch']),
            'criteria' => ['branch' => 'Lahore', 'status' => (string) STATUS_ACTIVE],
        ]), ['X-Requested-With' => 'XMLHttpRequest'])->assertOk();
        $response->assertJsonPath('recordsTotal', 3)->assertJsonPath('recordsFiltered', 2)->assertJsonCount(1, 'data');
        $this->assertSame('Lahore', $response->json('data.0.branch'));
        $branch = collect($response->json('criteriaOptions'))->firstWhere('key', 'branch');
        $this->assertEqualsCanonicalizing(['Karachi', 'Lahore'], array_column($branch['options'], 'value'));
        $response->assertDontSee('Islamabad');
    }

    public function test_collection_filters_use_visible_status_labels_and_preserve_full_counts(): void
    {
        Route::get('/_test/list-criteria', fn () => datatables(collect([
            ['id' => 1, 'status' => 0], ['id' => 2, 'status' => 1], ['id' => 3, 'status' => 1],
        ]))->editColumn('status', fn ($row) => '<span>' . ($row['status'] ? 'Active' : 'Pending') . '</span>')->rawColumns(['status'])->make(true));
        $this->getJson('/_test/list-criteria?' . http_build_query([
            'draw' => 1, 'start' => 1, 'length' => 1,
            'columns' => $this->columns(['status']), 'criteria' => ['status' => 'Active'],
        ]))->assertOk()->assertJsonPath('recordsTotal', 3)->assertJsonPath('recordsFiltered', 2)
            ->assertJsonPath('data.0.id', 3)->assertJsonCount(1, 'data');
    }

    public function test_upload_appears_in_matching_branch_document_queue_and_can_be_reviewed(): void
    {
        Storage::fake('private');
        $student = $this->user(USER_ROLE_STUDENT, 'Lahore');
        $admin = $this->user(USER_ROLE_ADMIN);
        $this->actingAs($student)->post(route('student.crm.documents.store'), [
            'category' => 'academic_transcript',
            'document' => UploadedFile::fake()->create('criteria-transcript.pdf', 12, 'application/pdf'),
        ])->assertRedirect()->assertSessionHasNoErrors();
        $document = StudentDocument::where('user_id', $student->id)->firstOrFail();
        Storage::disk('private')->assertExists($document->storage_path);

        $this->actingAs($admin)->get(route('admin.student_documents.index', ['branch' => 'Karachi']))->assertOk()->assertDontSee('criteria-transcript.pdf');
        $this->get(route('admin.student_documents.index', ['branch' => 'Lahore', 'category' => 'academic_transcript']))
            ->assertOk()->assertSee('criteria-transcript.pdf');
        $this->post(route('admin.student_documents.review', $document), ['status' => 'approved'])->assertRedirect();
        $this->get(route('admin.student_documents.index'))->assertOk()->assertDontSee('criteria-transcript.pdf');
        $this->get(route('admin.student_documents.index', ['status' => 'approved', 'branch' => 'Lahore']))->assertOk()->assertSee('criteria-transcript.pdf');
    }

    public function test_consultant_cannot_expand_document_scope_using_filters(): void
    {
        $consultant = $this->user(USER_ROLE_CONSULTANT);
        $student = $this->user(USER_ROLE_STUDENT, 'Lahore');
        StudentDocument::create([
            'user_id' => $student->id, 'category' => 'cv', 'storage_path' => 'private-test.enc',
            'original_name' => 'other-student.pdf', 'mime_type' => 'application/pdf', 'size_bytes' => 100, 'status' => 'pending',
        ]);
        $this->actingAs($consultant)->get(route('consultant.student_documents.index', ['status' => '', 'student_id' => $student->id, 'branch' => 'Lahore']))
            ->assertOk()->assertDontSee('other-student.pdf')->assertDontSee($student->email);
    }

    public function test_student_can_save_branch_and_invalid_branch_is_rejected(): void
    {
        $student = $this->user(USER_ROLE_STUDENT);
        $this->actingAs($student)->put(route('student.crm.profile.update'), ['branch' => 'Karachi'])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame('Karachi', $student->fresh()->branch);
        $this->put(route('student.crm.profile.update'), ['branch' => 'Unknown'])->assertSessionHasErrors('branch');
        $this->assertSame('Karachi', $student->fresh()->branch);
    }
}
