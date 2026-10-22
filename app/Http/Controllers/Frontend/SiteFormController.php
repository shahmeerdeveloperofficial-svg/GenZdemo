<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use App\Models\CrmLeadNote;
use App\Models\NewsletterSubscriber;
use App\Services\LeadCaptureService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SiteFormController extends Controller
{
    public function inquiry(Request $request, LeadCaptureService $leads)
    {
        $data = $request->validate([
            'student_name' => ['required', 'string', 'max:180'],
            'parent_name' => ['nullable', 'string', 'max:180'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'mobile' => ['required', 'string', 'min:6', 'max:60'],
            'program' => ['nullable', 'string', 'max:180'],
            'branch' => ['nullable', Rule::in(config('genz.registration.branches', []))],
            'message' => ['nullable', 'string', 'max:5000'],
            'event' => ['nullable', 'string', 'max:180'],
            'website' => ['nullable', 'string', 'max:0'],
        ]);
        $this->verifyCaptcha($request, 'site_inquiry');
        $name = preg_split('/\s+/', trim($data['student_name']), 2);
        $email = Str::lower(trim($data['email']));
        $message = collect([
            __('Student') . ': ' . $data['student_name'],
            filled($data['parent_name'] ?? null) ? __('Parent / guardian') . ': ' . $data['parent_name'] : null,
            filled($data['program'] ?? null) ? __('Program') . ': ' . $data['program'] : null,
            filled($data['branch'] ?? null) ? __('Branch') . ': ' . $data['branch'] : null,
            filled($data['event'] ?? null) ? __('Event') . ': ' . $data['event'] : null,
            $data['message'] ?? null,
        ])->filter()->implode("\n");

        DB::transaction(function () use ($leads, $data, $name, $email, $message) {
            $contact = ContactUs::create([
                'first_name' => $name[0], 'last_name' => $name[1] ?? '',
                'email' => $email, 'mobile' => $data['mobile'], 'message' => $message,
            ]);
            $lead = $leads->capture([
                'first_name' => $name[0], 'last_name' => $name[1] ?? null,
                'email' => $email, 'phone' => $data['mobile'],
            ], 'website_inquiry', 'Website inquiry #' . $contact->id, array_filter([
                'program' => $data['program'] ?? null, 'branch' => $data['branch'] ?? null,
                'parent_name' => $data['parent_name'] ?? null, 'event' => $data['event'] ?? null,
            ]));
            CrmLeadNote::create(['lead_id' => $lead->id, 'author_id' => auth()->id(), 'body' => $message]);
        });

        return response()->json(['status' => true, 'message' => __('Your inquiry has been received. Our team will contact you.')], 201);
    }

    public function newsletter(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email:rfc', 'max:190'],
            'consent' => ['required', 'accepted'],
            'website' => ['nullable', 'string', 'max:0'],
        ]);
        $this->verifyCaptcha($request, 'newsletter');
        $now = now();
        NewsletterSubscriber::upsert([[
            'email' => Str::lower(trim($data['email'])), 'status' => 'subscribed',
            'consent_at' => $now, 'subscribed_at' => $now, 'unsubscribed_at' => null,
            'created_at' => $now, 'updated_at' => $now,
        ]], ['email'], ['status', 'consent_at', 'subscribed_at', 'unsubscribed_at', 'updated_at']);

        return response()->json(['status' => true, 'message' => __('Your subscription has been saved.')], 201);
    }

    private function verifyCaptcha(Request $request, string $action): void
    {
        if ((int) getOption('recapture_in_contact_us', 0) !== 1) return;
        $request->validate(['recaptcha_token' => ['required', 'string', 'max:4096']]);
        try {
            $result = Http::asForm()->timeout(8)->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => getOption('google_recaptcha_secret_key'),
                'response' => $request->input('recaptcha_token'), 'remoteip' => $request->ip(),
            ])->json();
        } catch (\Throwable $exception) {
            report($exception);
            $result = [];
        }
        if (empty($result['success']) || ($result['score'] ?? 0) < 0.5 || ($result['action'] ?? '') !== $action) {
            throw ValidationException::withMessages(['recaptcha_token' => __('Please retry the security check before submitting.')]);
        }
    }
}
