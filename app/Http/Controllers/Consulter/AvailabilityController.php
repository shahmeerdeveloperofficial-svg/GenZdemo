<?php

namespace App\Http\Controllers\Consulter;

use App\Http\Controllers\Controller;
use App\Models\ConsultationBlackoutDate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AvailabilityController extends Controller
{
    public function index()
    {
        abort_unless(Schema::hasTable('consultation_blackout_dates'), 503, 'Consultation availability migration is not installed.');
        $dates = ConsultationBlackoutDate::where('consultant_id', auth()->id())->orderBy('date')->paginate(30);
        return view('consultant.consultations.blackouts', compact('dates'));
    }

    public function store(Request $request)
    {
        abort_unless(Schema::hasTable('consultation_blackout_dates'), 503, 'Consultation availability migration is not installed.');
        $data = $request->validate(['date' => ['required','date_format:Y-m-d','after_or_equal:today'], 'reason' => ['nullable','string','max:500']]);
        ConsultationBlackoutDate::updateOrCreate(['consultant_id' => auth()->id(), 'date' => $data['date']], ['reason' => $data['reason'] ?? null, 'created_by' => auth()->id()]);
        return back()->with('success', 'Unavailable date saved.');
    }

    public function destroy(ConsultationBlackoutDate $blackout)
    {
        abort_unless((int) $blackout->consultant_id === (int) auth()->id(), 404);
        $blackout->delete();
        return back()->with('success', 'Unavailable date removed.');
    }
}
