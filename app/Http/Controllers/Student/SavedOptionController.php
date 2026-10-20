<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicProgram;
use App\Models\Country;
use App\Models\Scholarship;
use App\Models\StudentSavedOption;
use App\Models\University;
use Illuminate\Http\Request;

class SavedOptionController extends Controller
{
    private const MODELS = [
        'country' => Country::class,
        'university' => University::class,
        'academic_program' => AcademicProgram::class,
        'scholarship' => Scholarship::class,
    ];

    public function index(Request $request)
    {
        $saved = StudentSavedOption::where('user_id', $request->user()->id)->latest()->get()
            ->map(function ($option) {
                $model = self::MODELS[$option->item_type] ?? null;
                $status = $option->item_type === 'academic_program' ? 'published' : STATUS_ACTIVE;
                $option->item = $model ? $model::where('status', $status)->find($option->item_id) : null;
                return $option;
            })->filter(fn ($option) => $option->item !== null)->values();
        return view('student.crm.saved-options', compact('saved'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['item_type' => ['required', 'in:country,university,academic_program,scholarship'], 'item_id' => ['required', 'integer', 'min:1']]);
        $model = self::MODELS[$data['item_type']];
        $status = $data['item_type'] === 'academic_program' ? 'published' : STATUS_ACTIVE;
        abort_unless($model::where('status', $status)->whereKey($data['item_id'])->exists(), 404);
        StudentSavedOption::firstOrCreate(['user_id' => $request->user()->id, 'item_type' => $data['item_type'], 'item_id' => $data['item_id']]);
        return back()->with('success', 'Added to your saved options.');
    }

    public function destroy(Request $request, StudentSavedOption $savedOption)
    {
        abort_unless((int) $savedOption->user_id === (int) $request->user()->id, 404);
        $savedOption->delete();
        return back()->with('success', 'Saved option removed.');
    }
}
