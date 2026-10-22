<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;
use TypeError;

class StudentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_is_created_even_if_welcome_email_transport_throws_a_type_error(): void
    {
        $email = 'student-create-' . Str::uuid() . '@example.test';
        Mail::shouldReceive('to')
            ->once()
            ->with($email)
            ->andThrow(new TypeError('SMTP port must be an integer'));

        $this->actingAs($this->admin())
            ->postJson(route('admin.students.store'), [
                'first_name' => 'Test',
                'last_name' => 'Learner',
                'email' => $email,
                'mobile' => '+923001234567',
                'password' => 'DemoPass!2026',
                'status' => STATUS_ACTIVE,
            ])
            ->assertOk()
            ->assertJsonPath('status', true);

        $student = User::where('email', $email)->firstOrFail();
        $this->assertSame(USER_ROLE_STUDENT, (int) $student->role);
        $this->assertTrue(Hash::check('DemoPass!2026', $student->password));
    }

    public function test_admin_can_open_student_edit_form_and_update_student_without_changing_password(): void
    {
        $student = User::create([
            'first_name' => 'Existing',
            'last_name' => 'Learner',
            'email' => 'student-edit-' . Str::uuid() . '@example.test',
            'mobile' => '+923001112233',
            'password' => Hash::make('OldPass!2026'),
            'role' => USER_ROLE_STUDENT,
            'status' => STATUS_ACTIVE,
            'google_auth_status' => 0,
        ]);
        $originalPassword = $student->password;
        $studentId = encodeId($student->id);

        $this->actingAs($this->admin())
            ->get(route('admin.students.edit', $studentId))
            ->assertOk()
            ->assertSee('Edit Student')
            ->assertSee($student->email);

        $this->actingAs($this->admin())
            ->patchJson(route('admin.students.update', $studentId), [
                'first_name' => 'Updated',
                'last_name' => 'Learner',
                'email' => $student->email,
                'mobile' => '+923009998877',
                'password' => '',
                'status' => STATUS_ACTIVE,
            ])
            ->assertOk()
            ->assertJsonPath('status', true);

        $student->refresh();
        $this->assertSame('Updated', $student->first_name);
        $this->assertSame('+923009998877', $student->mobile);
        $this->assertSame($originalPassword, $student->password);
    }

    public function test_admin_can_toggle_student_status_from_the_list_badge(): void
    {
        $student = User::create([
            'first_name' => 'Status',
            'last_name' => 'Learner',
            'email' => 'student-status-' . Str::uuid() . '@example.test',
            'mobile' => '+923001234567',
            'password' => Hash::make('DemoPass!2026'),
            'role' => USER_ROLE_STUDENT,
            'status' => STATUS_ACTIVE,
            'google_auth_status' => 0,
        ]);
        $admin = $this->admin();
        $encodedId = encodeId($student->id);

        $this->actingAs($admin)
            ->postJson(route('admin.students.status_change', $encodedId), ['status' => STATUS_DEACTIVATE])
            ->assertOk()
            ->assertJsonPath('status', true);
        $this->assertSame((int) STATUS_DEACTIVATE, (int) $student->fresh()->status);

        $this->postJson(route('admin.students.status_change', $encodedId), ['status' => STATUS_ACTIVE])
            ->assertOk()
            ->assertJsonPath('status', true);
        $this->assertSame((int) STATUS_ACTIVE, (int) $student->fresh()->status);

        $listing = $this->getJson(route('admin.students.index', ['draw' => 1, 'start' => 0, 'length' => 10]), [
            'X-Requested-With' => 'XMLHttpRequest',
        ])
            ->assertOk();
        $row = collect($listing->json('data'))->firstWhere('email', $student->email);

        $this->assertNotNull($row);
        $this->assertStringContainsString('data-student-status-toggle', $row['status']);
        $this->assertStringNotContainsString('Change Status', $row['action']);
    }

    private function admin(): User
    {
        return User::factory()->make([
            'role' => USER_ROLE_ADMIN,
            'status' => USER_STATUS_ACTIVE,
            'google_auth_status' => 0,
        ]);
    }
}
