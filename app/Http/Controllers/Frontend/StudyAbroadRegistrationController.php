<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\StudyAbroadRegistration;
use App\Services\LeadCaptureService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class StudyAbroadRegistrationController extends Controller
{
    public function create()
    {
        return view('frontend.study-abroad-registration', [
            'branches' => config('genz.registration.branches', []),
            'currencies' => config('genz.registration.currencies', []),
            'feeSchedules' => config('genz.registration.fee_schedules', []),
        ]);
    }

    public function store(Request $request, LeadCaptureService $leadCapture)
    {
        // A simple honeypot helps discard automated form submissions.
        if ($request->filled('website')) {
            return redirect()->route('study-abroad-registration.thanks');
        }

        $schedules = config('genz.registration.fee_schedules', []);
        $destinations = collect($schedules)->flatMap(fn ($schedule) => collect($schedule['fees'] ?? [])->pluck('destination'))->unique()->values()->all();
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:180'],
            'father_name' => ['required', 'string', 'max:180'],
            'identity_number' => ['required', 'string', 'max:80'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender' => ['required', Rule::in(['Male', 'Female', 'Other'])],
            'marital_status' => ['required', Rule::in(['Single', 'Married'])],
            'phone' => ['required', 'string', 'max:60'],
            'whatsapp' => ['required', 'string', 'max:60'],
            'email' => ['required', 'email:rfc', 'max:254'],
            'residential_address' => ['required', 'string', 'max:2000'],
            'last_qualification' => ['required', 'string', 'max:180'],
            'field_of_study' => ['required', 'string', 'max:180'],
            'institute_name' => ['required', 'string', 'max:180'],
            'passing_year' => ['required', 'integer', 'between:1950,' . (now()->year + 1)],
            'grade' => ['required', 'string', 'max:60'],
            'destination' => ['required', Rule::in($destinations)],
            'branch' => ['required', Rule::in(config('genz.registration.branches', []))],
            'program_type' => ['required', Rule::in(array_keys($schedules))],
            'paid_amount' => ['required', 'numeric', 'min:0', 'max:9999999999'],
            'paid_currency' => ['required', Rule::in(array_keys(config('genz.registration.currencies', [])))],
            'signed_at' => ['required', 'date', 'before_or_equal:today'],
            'signature' => ['required', 'string', 'max:450000', 'regex:/^data:image\/png;base64,[A-Za-z0-9+\/=]+$/'],
            'terms_accepted' => ['accepted'],
            'website' => ['nullable', 'max:0'],
        ], [
            'terms_accepted.accepted' => 'Please accept the declaration and terms to submit your registration.',
            'signature.regex' => 'Please add your signature in the signature box.',
        ]);

        // Ensure the destination actually belongs to the chosen program category.
        $validDestinations = collect($schedules[$data['program_type']]['fees'] ?? [])->pluck('destination')->all();
        if (!in_array($data['destination'], $validDestinations, true)) {
            return back()->withErrors(['destination' => 'Choose a destination available for this program type.'])->withInput();
        }

        $signatureBytes = base64_decode(substr($data['signature'], strlen('data:image/png;base64,')), true);
        if (!$signatureBytes || strlen($signatureBytes) > 300000 || substr($signatureBytes, 0, 8) !== "\x89PNG\r\n\x1a\n") {
            return back()->withErrors(['signature' => 'The signature image is invalid. Please sign again.'])->withInput();
        }

        $signaturePath = 'study-registration-signatures/' . bin2hex(random_bytes(20)) . '.png';
        Storage::disk('local')->put($signaturePath, $signatureBytes);

        try {
            $lead = $leadCapture->capture([
                'first_name' => trim(explode(' ', $data['full_name'], 2)[0]),
                'last_name' => trim(explode(' ', $data['full_name'], 2)[1] ?? ''),
                'email' => $data['email'],
                'phone' => $data['phone'],
            ], 'student_registration', 'Public study-abroad registration', [
                'destination' => $data['destination'],
                'program_type' => $data['program_type'],
            ]);

            DB::transaction(function () use ($data, $lead, $signaturePath) {
                unset($data['signature'], $data['website']);
                StudyAbroadRegistration::create(array_merge($data, [
                    'lead_id' => $lead->id,
                    'signature_path' => $signaturePath,
                    'terms_accepted' => true,
                    'status' => 'submitted',
                ]));
            });
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($signaturePath);
            report($exception);
            return back()->withInput()->with('error', 'We could not save your registration just now. Please try again.');
        }

        return redirect()->route('study-abroad-registration.thanks');
    }

    public function thanks()
    {
        return view('frontend.study-abroad-registration-thanks');
    }
}
