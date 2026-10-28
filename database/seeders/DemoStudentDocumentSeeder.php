<?php

namespace Database\Seeders;

use App\Models\StudentDocument;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DemoStudentDocumentSeeder extends Seeder
{
    private const DOCUMENTS = [
        'passport' => 'Passport category placeholder',
        'academic_transcript' => 'Academic transcript sample',
        'language_test' => 'Language test sample',
        'cv' => 'CV sample',
        'financial_document' => 'Financial document sample',
        'other' => 'Supporting document sample',
    ];

    public function run(): void
    {
        $database = DB::connection()->getDatabaseName();
        $safeLocalDatabase = in_array($database, ['gen_soft_run', 'genz_crm_lms_demo', 'demo'], true);

        if (app()->environment('production') || (!app()->environment('testing') && (!app()->environment('local') || !$safeLocalDatabase))) {
            $this->command?->error('Demo documents are restricted to the local demo database. No records were changed.');
            return;
        }

        $students = User::query()
            ->where('role', USER_ROLE_STUDENT)
            ->where('email', 'like', 'demo.learner.%@genzconsultants.example')
            ->orderBy('id')
            ->get();

        foreach ($students as $student) {
            $slug = Str::between($student->email, 'demo.learner.', '@genzconsultants.example');

            foreach (self::DOCUMENTS as $category => $title) {
                $fileName = 'DEMO-' . $slug . '-' . $category . '.pdf';
                $path = "student-documents/{$student->id}/demo/" . Str::slug($slug . '-' . $category) . '.enc';
                $document = StudentDocument::firstOrNew(['storage_path' => $path]);

                if (!$document->exists) {
                    $document->fill([
                        'user_id' => $student->id,
                        'category' => $category,
                        'original_name' => $fileName,
                        'mime_type' => 'application/pdf',
                        'size_bytes' => 0,
                        'status' => 'pending',
                        'reviewed_by' => null,
                        'reviewed_at' => null,
                        'review_note' => null,
                    ]);
                }

                if (!$document->exists || !Storage::disk('private')->exists($path)) {
                    $pdf = $this->makeSamplePdf($student->name, $title, $category);
                    Storage::disk('private')->put($path, Crypt::encryptString($pdf));
                    $document->size_bytes = strlen($pdf);
                }

                if (!$document->exists) {
                    $document->save();
                } elseif ($document->isDirty('size_bytes')) {
                    $document->save();
                }
            }
        }

        $this->command?->info('Added or verified ' . ($students->count() * count(self::DOCUMENTS)) . ' fictional sample documents for ' . $students->count() . ' demo students.');
    }

    private function makeSamplePdf(string $studentName, string $title, string $category): string
    {
        $lines = [
            'SAMPLE ONLY - NOT A REAL DOCUMENT',
            $title,
            'Student: ' . $studentName,
            'Category: ' . str_replace('_', ' ', ucfirst($category)),
            'For local software demonstration and workflow testing only.',
            'Contains no real identity, test, academic, or financial evidence.',
            'Do not submit this file to a school, government, bank, or testing body.',
        ];

        $stream = "BT\n/F1 18 Tf\n50 790 Td\n(" . $this->escapePdfText($lines[0]) . ") Tj\n/F1 13 Tf\n0 -40 Td\n";
        foreach (array_slice($lines, 1) as $line) {
            $stream .= '(' . $this->escapePdfText($line) . ") Tj\n0 -28 Td\n";
        }
        $stream .= "ET\n";

        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>',
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
            '<< /Length ' . strlen($stream) . ">>\nstream\n" . $stream . 'endstream',
        ];

        $pdf = "%PDF-1.4\n% Demo sample document\n";
        $offsets = [0];
        foreach ($objects as $index => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($index + 1) . " 0 obj\n" . $object . "\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n0000000000 65535 f \n";
        foreach (array_slice($offsets, 1) as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }
        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n" . $xrefOffset . "\n%%EOF\n";

        return $pdf;
    }

    private function escapePdfText(string $text): string
    {
        $ascii = Str::ascii($text);
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $ascii);
    }
}
