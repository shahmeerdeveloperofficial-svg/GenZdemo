<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CourseEnrollment;
use App\Models\CourseResource;
use App\Models\FileManager;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class CourseResourceController extends Controller
{
    public function show($enrollmentId, $resourceId)
    {
        $enrollment = app(\App\Services\LmsEnrollmentService::class)->activeEnrollment(decodeId($enrollmentId), (int) auth()->id());
        $resourceQuery = CourseResource::whereKey(decodeId($resourceId))->where('course_id', $enrollment->course_id);
        if (Schema::hasColumn('course_resources', 'publication_state')) $resourceQuery->where('publication_state', 'published');
        $resourceQuery->whereHas('lecture', function ($lecture) use ($enrollment) {
            $lecture->where('course_id', $enrollment->course_id);
            if (Schema::hasColumn('course_lectures', 'publication_state')) $lecture->where('publication_state', 'published');
            $lecture->whereHas('lesson', function ($lesson) use ($enrollment) {
                $lesson->where('course_id', $enrollment->course_id);
                if (Schema::hasColumn('course_lessons', 'publication_state')) $lesson->where('publication_state', 'published');
            });
        });
        $resource = $resourceQuery->firstOrFail();
        abort_unless(in_array((int) $resource->resource_type, [RESOURCE_TYPE_LOCAL, RESOURCE_TYPE_PDF, RESOURCE_TYPE_IMAGE, RESOURCE_TYPE_AUDIO], true), 404);
        $file = FileManager::findOrFail($resource->resource);
        abort_unless(Storage::disk($file->storage_type)->exists($file->path), 404);

        $headers = [
            'Content-Type' => $file->file_type ?: 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="'.str_replace(['"', "\r", "\n"], '', basename($file->original_name)).'"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store, max-age=0',
            'Accept-Ranges' => 'bytes',
        ];
        try {
            return response()->file(Storage::disk($file->storage_type)->path($file->path), $headers);
        } catch (\Throwable $exception) {
            abort_unless(Storage::disk($file->storage_type)->exists($file->path), 404);
            return Storage::disk($file->storage_type)->download($file->path, basename($file->original_name), $headers);
        }
    }
}
