<?php

namespace App\Http\Controllers\Admin\Course;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseBatch;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CourseBatchController extends Controller
{
    use ResponseTrait;

    public function store(Request $request, $courseId)
    {
        abort_unless(Schema::hasTable('course_batches'), 503, 'Apply LMS enrollment migrations first.');
        $query = Course::whereKey((int) $courseId);
        if ((int) auth()->user()->role === USER_ROLE_INSTRUCTOR) $query->where('created_by',auth()->id());
        $course = $query->firstOrFail();
        $data = $request->validate([
            'id'=>['nullable','integer'],'title'=>['required','string','max:180'],
            'starts_at'=>['nullable','date'],'ends_at'=>['nullable','date','after:starts_at'],
            'timezone'=>['required','timezone'],'capacity'=>['nullable','integer','min:1','max:100000'],
            'status'=>['nullable','in:draft,active,cancelled'],
        ]);
        $batch = !empty($data['id']) ? CourseBatch::where('course_id',$course->id)->whereKey($data['id'])->firstOrFail() : new CourseBatch();
        $batch->course_id = $course->id; $batch->title = $data['title']; $batch->starts_at = $data['starts_at'] ?? null;
        $batch->ends_at = $data['ends_at'] ?? null; $batch->timezone = $data['timezone']; $batch->capacity = $data['capacity'] ?? null;
        $batch->status = (int) auth()->user()->role === USER_ROLE_INSTRUCTOR ? 'draft' : ($data['status'] ?? 'draft');
        $batch->created_by ??= auth()->id(); $batch->save();
        return $this->success([], __('Course batch saved.'));
    }

    public function delete($courseId, $batchId)
    {
        $query = Course::whereKey((int) $courseId);
        if ((int) auth()->user()->role === USER_ROLE_INSTRUCTOR) $query->where('created_by',auth()->id());
        $course = $query->firstOrFail();
        CourseBatch::where('course_id',$course->id)->whereKey($batchId)->firstOrFail()->update(['status'=>'cancelled']);
        return $this->success([], __('Batch cancelled. Existing enrollment records are preserved.'));
    }
}
