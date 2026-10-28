<?php

namespace Tests\Feature;

use App\Models\StudentDocument;
use App\Models\User;
use Database\Seeders\DemoStudentDocumentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DemoStudentDocumentSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_encrypted_fictional_documents_for_demo_students_idempotently(): void
    {
        Storage::fake('private');

        $student = User::create([
            'first_name' => 'Demo',
            'last_name' => 'Learner',
            'email' => 'demo.learner.test@genzconsultants.example',
            'mobile' => '+10000000000',
            'password' => bcrypt('DemoPass!2026'),
            'role' => USER_ROLE_STUDENT,
            'status' => USER_STATUS_ACTIVE,
            'google_auth_status' => 0,
        ]);

        $this->seed(DemoStudentDocumentSeeder::class);

        $documents = StudentDocument::where('user_id', $student->id)->get();
        $this->assertCount(6, $documents);
        $this->assertSame(['academic_transcript', 'cv', 'financial_document', 'language_test', 'other', 'passport'], $documents->pluck('category')->sort()->values()->all());
        $this->assertTrue($documents->every(fn (StudentDocument $document) => $document->status === 'pending' && Storage::disk('private')->exists($document->storage_path)));

        $sampleDocument = $documents->first();
        $decrypted = Crypt::decryptString(Storage::disk('private')->get($sampleDocument->storage_path));
        $this->assertStringStartsWith('%PDF-1.4', $decrypted);
        $this->assertStringContainsString('NOT A REAL DOCUMENT', $decrypted);

        $this->seed(DemoStudentDocumentSeeder::class);
        $this->assertSame(6, StudentDocument::where('user_id', $student->id)->count());
    }
}
