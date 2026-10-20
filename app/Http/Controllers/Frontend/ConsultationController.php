<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\ConsulterFeedback;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use App\Services\ConsultationBookingService;
use Illuminate\Validation\Rule;

class ConsultationController extends Controller
{
    use ResponseTrait;

    public function list()
    {
        $data['pageTitle'] = __('Consultation');
        $data['activeConsultation'] = 'active';
        $data['consultants'] = User::where(['role' => USER_ROLE_CONSULTANT, 'status' => STATUS_ACTIVE])->paginate(6);

        return view('frontend.consultations.list', $data);
    }

    public function details($id)
    {
        $data['consultant'] = User::where(['role' => USER_ROLE_CONSULTANT, 'status' => STATUS_ACTIVE, 'id' => decodeId($id)])->firstOrFail();
        $data['consultant']->completed_consultation = Appointment::where('consulter_id', $data['consultant']->id)->where('appointments.payment_status', '!=', PAYMENT_STATUS_NOT_INIT)->where('status', STATUS_ACTIVE)->count();
        $data['reviews'] = ConsulterFeedback::where(['consulter_id' => decodeId($id), 'status' => STATUS_ACTIVE])->with('reviewer')->get();
        $data['pageTitle'] = $data['consultant']->name;
        $data['activeConsultation'] = 'active';
        $data['bodyClass'] = 'bg-white';

        return view('frontend.consultations.details', $data);
    }

    public function booking($id)
    {
        if(auth()->check() && auth()->user()->role == USER_ROLE_ADMIN){
            return back()->with('error', __('Admin can not perform this action'));
        }
        $data['consultant'] = User::where(['role' => USER_ROLE_CONSULTANT, 'status' => STATUS_ACTIVE, 'id' => decodeId($id)])->firstOrFail();

        // Only fetch active slots associated with the consulter
        $data['slots'] = $data['consultant']->slots()->where('status', STATUS_ACTIVE)->get();
        $data['moreConsultants'] = User::where(['role' => USER_ROLE_CONSULTANT, 'status' => STATUS_ACTIVE])->where('id', '!=', decodeId($id))->limit(3)->get();

        $data['pageTitle'] = __('Consultation Booking');
        $data['activeConsultation'] = 'active';

        return view('frontend.consultations.booking', $data);
    }

    public function bookingValidation(Request $request, $id, ConsultationBookingService $bookingService)
    {
        $data = $request->validate([
            'date' => ['required','date_format:Y-m-d'],
            'consultation_slot_id' => ['required','integer','min:1'],
            'consultationType' => ['required','integer',Rule::in([CONSULTATION_TYPE_PHYSICAL, CONSULTATION_TYPE_VIRTUAL])],
        ]);
        $bookingService->checkAvailability((int) decodeId($id), (int) $data['consultation_slot_id'], $data['date'], (int) $data['consultationType']);
        return $this->success([], __('Validated'));
    }

    public function bookingSlot(Request $request, $id)
    {
        // Get the selected date from the request
        $data = $request->validate(['date' => ['required','date_format:Y-m-d']]);
        $selectedDate = $data['date'];
        $consultant = User::where(['role' => USER_ROLE_CONSULTANT, 'status' => STATUS_ACTIVE, 'id' => $id])->firstOrFail();
        $timezone = $consultant->consultation_timezone ?: config('app.timezone','UTC');
        if (in_array(\Carbon\Carbon::parse($selectedDate, $timezone)->dayOfWeek + 1, array_map('intval', $consultant->day_off ?? []), true)
            || (\Illuminate\Support\Facades\Schema::hasTable('consultation_blackout_dates') && $consultant->consultationBlackoutDates()->whereDate('date', $selectedDate)->exists())) {
            $data['slots'] = [];
            return response()->view('frontend.consultations.slots', ['slots' => $data['slots'], 'consultant' => $consultant]);
        }
        $oldSlotId = $request->input('old_slot_id'); // Retrieve old_slot_id from the request

        // Check if the selected date is in the past
        if (strtotime($selectedDate) < strtotime(date('Y-m-d'))) {
            // If the date is in the past, return an empty array of slots
            $data['slots'] = [];
        } else {
            // Decode and find the consultant
            $consultant = User::where([
                'role' => USER_ROLE_CONSULTANT,
                'status' => STATUS_ACTIVE,
                'id' => $id
            ])->firstOrFail();

            // Retrieve only active slots for this consultant that are available on the selected date
            $data['slots'] = $consultant->slots()
                ->where('status', STATUS_ACTIVE)
                ->where(function ($query) use ($selectedDate, $consultant, $oldSlotId) {
                    // Include slots without conflicting appointments or include the old slot
                    $query->whereDoesntHave('appointments', function ($subQuery) use ($selectedDate, $consultant) {
                        $subQuery->where('date', $selectedDate)
                            ->where('consulter_id', $consultant->id)
                            ->where('status', '!=', STATUS_REJECT)
                            ->where(fn ($busy) => $busy->whereIn('payment_status', [PAYMENT_STATUS_PAID, PAYMENT_STATUS_PENDING])->orWhere(fn ($hold) => $hold->where('payment_status', PAYMENT_STATUS_NOT_INIT)->where('hold_expires_at', '>', now())));
                    })->orWhere('id', $oldSlotId); // Include old slot
                })
                ->get();
        }

        $timezone = $consultant->consultation_timezone ?: config('app.timezone', 'UTC');
        $data['slots'] = collect($data['slots'])->filter(fn ($slot) => \Carbon\Carbon::parse($selectedDate.' '.$slot->start_time, $timezone)->isFuture())->values();

        $data['consultant'] = $consultant;

        return view('frontend.consultations.slots', $data);
    }


}
