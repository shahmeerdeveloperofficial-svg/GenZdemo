@extends('frontend.layouts.app')

@section('title', 'Student Registration | GenZ Consultants')

@section('content')
<main class="genz-registration">
    <div class="registration-wrap">
        <div class="registration-heading">
            <span class="registration-kicker">GENZ CONSULTANTS · STUDY ABROAD</span>
            <h1>Student Registration Form</h1>
            <p>Share your details and our counselling team will help plan your study-abroad journey.</p>
        </div>

        @if (session('error')) <div class="registration-alert">{{ session('error') }}</div> @endif
        @if ($errors->any())
            <div class="registration-alert" role="alert"><strong>Please review the form:</strong><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif

        <form method="POST" action="{{ route('study-abroad-registration.store') }}" id="study-registration-form">
            @csrf
            <div class="registration-trap" aria-hidden="true"><label>Leave this field empty<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

            <section class="registration-section">
                <h2>1. Personal Information</h2>
                <div class="registration-grid two">
                    <label>Full Name *<input name="full_name" value="{{ old('full_name') }}" maxlength="180" required autocomplete="name"></label>
                    <label>Father's Name *<input name="father_name" value="{{ old('father_name') }}" maxlength="180" required></label>
                    <label>CNIC / Passport No. *<input name="identity_number" value="{{ old('identity_number') }}" maxlength="80" required autocomplete="off"></label>
                    <label>Date of Birth *<input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" max="{{ now()->subDay()->format('Y-m-d') }}" required></label>
                    <fieldset><legend>Gender *</legend><div class="registration-options">@foreach (['Male', 'Female', 'Other'] as $gender)<label><input type="radio" name="gender" value="{{ $gender }}" @checked(old('gender') === $gender) required> {{ $gender }}</label>@endforeach</div></fieldset>
                    <fieldset><legend>Marital Status *</legend><div class="registration-options">@foreach (['Single', 'Married'] as $status)<label><input type="radio" name="marital_status" value="{{ $status }}" @checked(old('marital_status') === $status) required> {{ $status }}</label>@endforeach</div></fieldset>
                    <label>Contact Number *<input type="tel" name="phone" value="{{ old('phone') }}" maxlength="60" required autocomplete="tel"></label>
                    <label>WhatsApp Number *<input type="tel" name="whatsapp" value="{{ old('whatsapp') }}" maxlength="60" required></label>
                    <label>Email Address *<input type="email" name="email" value="{{ old('email') }}" maxlength="254" required autocomplete="email"></label>
                    <label class="full">Residential Address *<textarea name="residential_address" rows="3" maxlength="2000" required>{{ old('residential_address') }}</textarea></label>
                </div>
            </section>

            <section class="registration-section">
                <h2>2. Academic Information</h2>
                <div class="registration-grid two">
                    <label>Last Qualification *<input name="last_qualification" value="{{ old('last_qualification') }}" placeholder="e.g. Intermediate, BS" maxlength="180" required></label>
                    <label>Field of Study *<input name="field_of_study" value="{{ old('field_of_study') }}" placeholder="e.g. Computer Science, Business" maxlength="180" required></label>
                    <label>Institute Name *<input name="institute_name" value="{{ old('institute_name') }}" maxlength="180" required></label>
                    <label>Passing Year *<input type="number" name="passing_year" value="{{ old('passing_year') }}" min="1950" max="{{ now()->year + 1 }}" required></label>
                    <label>Percentage / CGPA *<input name="grade" value="{{ old('grade') }}" placeholder="e.g. 3.4 or 80%" maxlength="60" required></label>
                </div>
            </section>

            <section class="registration-section">
                <h2>3. Consultancy Charges Schedule</h2>
                <div class="registration-fee-note"><strong>Demo fee schedule</strong><span>Illustrative sample amounts only — not official or confirmed GenZ prices. Please confirm charges with our team before making any payment.</span></div>
                <div class="registration-fee-tables">
                    @foreach ($feeSchedules as $key => $schedule)
                    <div><h3>{{ $schedule['label'] }}</h3><div class="registration-table-scroll"><table><thead><tr><th>Destination</th><th>Demo Fee</th><th>Currency</th></tr></thead><tbody>
                        @foreach ($schedule['fees'] as $fee)<tr><td>{{ $fee['destination'] }}</td><td>{{ number_format($fee['amount']) }}</td><td>{{ $fee['currency'] }}</td></tr>@endforeach
                    </tbody></table></div></div>
                    @endforeach
                </div>
                <div class="registration-grid three registration-choice-row">
                    <label>Desired Destination *<select name="destination" id="registration-destination" required><option value="">— Select Destination —</option>@foreach (collect($feeSchedules)->flatMap(fn($schedule) => $schedule['fees'])->pluck('destination')->unique() as $destination)<option value="{{ $destination }}" @selected(old('destination') === $destination)>{{ $destination }}</option>@endforeach</select></label>
                    <label>Branch *<select name="branch" required><option value="">Select Branch</option>@foreach ($branches as $branch)<option value="{{ $branch }}" @selected(old('branch') === $branch)>{{ $branch }}</option>@endforeach</select></label>
                    <label>Program Type *<select name="program_type" id="registration-program" required><option value="">— Select Option —</option>@foreach ($feeSchedules as $key => $schedule)<option value="{{ $key }}" @selected(old('program_type') === $key)>{{ $schedule['label'] }}</option>@endforeach</select></label>
                </div>
            </section>

            <section class="registration-section">
                <h2>4. Terms &amp; Conditions</h2>
                <div class="registration-terms" tabindex="0">
                    <p>These sample terms are provided for this demo registration flow. The final consultancy agreement and fee schedule must be supplied and confirmed by GenZ Consultants before any paid service begins.</p>
                    <ol>
                        <li>Information submitted should be complete and accurate.</li>
                        <li>Submitting this form is an enquiry/registration and does not guarantee admission, scholarship, visa approval, or any outcome.</li>
                        <li>University, application, embassy, test, courier, and other third-party charges may be separate from consultancy charges.</li>
                        <li>Any refund or cancellation will be handled according to the written agreement confirmed with the applicant.</li>
                        <li>Do not make a payment based on the demo amounts shown above; request a written quotation first.</li>
                        <li>Personal information will be used to respond to this study-abroad enquiry and manage the application process.</li>
                    </ol>
                </div>
                <div class="registration-grid two registration-payment-row">
                    <label>Paid Amount *<input type="number" name="paid_amount" value="{{ old('paid_amount', '0') }}" min="0" max="9999999999" step="0.01" required><small>Enter the amount already paid, or 0 if no payment has been made.</small></label>
                    <label>Select Currency *<select name="paid_currency" required>@foreach ($currencies as $code => $currency)<option value="{{ $code }}" @selected(old('paid_currency', 'PKR') === $code)>{{ $currency }}</option>@endforeach</select></label>
                </div>
            </section>

            <section class="registration-section">
                <h2>5. Declaration &amp; E-Signature</h2>
                <p class="registration-declaration">I hereby declare that all information provided is true and correct. I understand that the fee schedule above is demo-only and agree to the terms displayed on this form.</p>
                <div class="registration-grid two">
                    <label>Applicant E-Signature (sign inside the box) *
                        <div class="signature-pad-wrap"><canvas id="registration-signature" width="840" height="250" aria-label="Draw your signature"></canvas><div class="signature-controls"><span>Sign with your finger, stylus or mouse</span><button type="button" id="clear-registration-signature">Clear</button></div></div>
                        <input type="hidden" name="signature" id="registration-signature-data">
                    </label>
                    <label>Date *<input type="date" name="signed_at" value="{{ old('signed_at', now()->toDateString()) }}" max="{{ now()->toDateString() }}" required></label>
                </div>
                <label class="registration-consent"><input type="checkbox" name="terms_accepted" value="1" @checked(old('terms_accepted')) required> I confirm my declaration and accept the terms shown above. *</label>
            </section>

            <button class="registration-submit" type="submit">Submit Registration</button>
        </form>
    </div>
</main>

<style>
.genz-registration{background:#f4f9ff;padding:42px 16px 72px;color:#142945}.registration-wrap{max-width:920px;margin:auto;background:#fff;border:1px solid #e3eaf3;border-radius:16px;box-shadow:0 14px 38px #07357912;padding:clamp(20px,4vw,48px)}.registration-heading{padding-bottom:22px;border-bottom:1px solid #e3eaf3;margin-bottom:28px}.registration-kicker{font-size:12px;font-weight:800;letter-spacing:.12em;color:#078bb9}.registration-heading h1{font-size:clamp(26px,4vw,36px);color:#073579;margin:8px 0}.registration-heading p{color:#64748b;margin:0}.registration-section{margin:26px 0 32px}.registration-section h2{font-size:15px;text-transform:uppercase;letter-spacing:.03em;background:#f4f9ff;border-left:4px solid #073579;padding:14px 16px;margin:0 0 20px;color:#102e50}.registration-grid{display:grid;gap:18px 16px}.registration-grid.two{grid-template-columns:repeat(2,minmax(0,1fr))}.registration-grid.three{grid-template-columns:repeat(3,minmax(0,1fr))}.registration-grid label{display:flex;flex-direction:column;gap:7px;font-size:14px;font-weight:600}.registration-grid input:not([type=radio]):not([type=checkbox]),.registration-grid select,.registration-grid textarea{width:100%;border:1px solid #cbd8e8;border-radius:5px;min-height:43px;padding:9px 12px;color:#142945;background:#fff;font-weight:400}.registration-grid textarea{resize:vertical}.registration-grid .full{grid-column:1/-1}fieldset{border:0;padding:0;margin:0}fieldset legend{font-size:14px;font-weight:600;margin-bottom:14px}.registration-options{display:flex;gap:18px;align-items:center;min-height:24px}.registration-options label{display:inline-flex;flex-direction:row;align-items:center;gap:5px;font-weight:400}.registration-fee-note{display:flex;flex-direction:column;gap:3px;padding:12px 15px;margin-bottom:16px;background:#fff8e8;border:1px solid #f4dda9;border-radius:8px;font-size:13px;color:#654b13}.registration-fee-tables{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}.registration-fee-tables h3{font-size:14px;margin:0 0 10px;color:#073579}.registration-table-scroll{overflow-x:auto} .registration-fee-tables table{width:100%;border-collapse:collapse;font-size:13px}.registration-fee-tables th,.registration-fee-tables td{padding:9px 10px;border:1px solid #dbe4ef;text-align:left}.registration-fee-tables th{background:#f2f6fb;font-weight:700}.registration-choice-row,.registration-payment-row{margin-top:20px}.registration-terms{max-height:190px;overflow:auto;border:1px solid #dbe4ef;border-radius:5px;padding:14px 18px;font-size:13px;line-height:1.65}.registration-terms ol{padding-left:20px}.registration-payment-row small{font-size:12px;color:#64748b;font-weight:400}.registration-declaration{font-size:14px;line-height:1.6;margin:0 0 16px}.signature-pad-wrap{border:1px dashed #a9bbd1;border-radius:5px;overflow:hidden}.signature-pad-wrap canvas{display:block;width:100%;height:155px;touch-action:none;cursor:crosshair}.signature-controls{display:flex;justify-content:space-between;align-items:center;background:#f5f8fc;padding:9px 12px;font-size:12px;color:#64748b}.signature-controls button{border:0;background:none;color:#c62828;font-weight:700;cursor:pointer}.registration-consent{display:flex;align-items:flex-start;gap:9px;margin-top:20px;font-size:13px}.registration-consent input{margin-top:3px}.registration-submit{width:100%;border:0;border-radius:5px;padding:16px;background:#0b2f50;color:#fff;font-size:16px;font-weight:700;cursor:pointer}.registration-submit:hover{background:#073579}.registration-alert{padding:14px 16px;border-radius:6px;background:#fff1f1;border:1px solid #f1c4c4;color:#812323;margin-bottom:18px}.registration-trap{position:absolute;left:-10000px;width:1px;height:1px;overflow:hidden}@media(max-width:680px){.registration-grid.two,.registration-grid.three,.registration-fee-tables{grid-template-columns:1fr}.registration-wrap{padding:20px 16px}.genz-registration{padding:20px 10px 40px}.registration-section{margin:22px 0}.registration-heading h1{font-size:27px}}
</style>
<script>
(() => {
    const feeDestinations = @json(collect($feeSchedules)->map(fn ($schedule) => collect($schedule['fees'])->pluck('destination')->values())->all());
    const programSelect = document.getElementById('registration-program');
    const destinationSelect = document.getElementById('registration-destination');
    const allDestinations = Array.from(destinationSelect.options).slice(1).map(option => ({value:option.value,label:option.text}));
    const filterDestinations = () => {
        const allowed = feeDestinations[programSelect.value] || allDestinations.map(option => option.value);
        const previous = destinationSelect.value;
        destinationSelect.replaceChildren(new Option('— Select Destination —', ''));
        allDestinations.filter(option => allowed.includes(option.value)).forEach(option => destinationSelect.add(new Option(option.label, option.value)));
        if (allowed.includes(previous)) destinationSelect.value = previous;
    };
    programSelect.addEventListener('change', filterDestinations);
    if (programSelect.value) filterDestinations();

    const canvas = document.getElementById('registration-signature');
    const context = canvas.getContext('2d');
    const hidden = document.getElementById('registration-signature-data');
    let drawing = false, signed = false;
    context.strokeStyle = '#102e50'; context.lineWidth = 3; context.lineCap = 'round'; context.lineJoin = 'round';
    const point = event => { const rect = canvas.getBoundingClientRect(); return {x:(event.clientX-rect.left)*canvas.width/rect.width,y:(event.clientY-rect.top)*canvas.height/rect.height}; };
    canvas.addEventListener('pointerdown', event => { drawing=true; signed=true; canvas.setPointerCapture(event.pointerId); const p=point(event); context.beginPath(); context.moveTo(p.x,p.y); });
    canvas.addEventListener('pointermove', event => { if(!drawing)return; const p=point(event); context.lineTo(p.x,p.y); context.stroke(); });
    canvas.addEventListener('pointerup', () => drawing=false);
    canvas.addEventListener('pointercancel', () => drawing=false);
    document.getElementById('clear-registration-signature').addEventListener('click', () => { context.clearRect(0,0,canvas.width,canvas.height); signed=false; hidden.value=''; });
    document.getElementById('study-registration-form').addEventListener('submit', event => {
        if(!signed){ event.preventDefault(); window.GenzToast.error('Please sign in the e-signature box before submitting.'); canvas.scrollIntoView({behavior:'smooth',block:'center'}); return; }
        hidden.value=canvas.toDataURL('image/png');
    });
})();
</script>
@endsection
