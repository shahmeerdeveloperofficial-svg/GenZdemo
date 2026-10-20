<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $rules = [
            "email" => ['required', 'email', 'max:255', 'unique:users'],
            "first_name" => ['required', 'string', 'max:255'],
            "last_name" => ['required', 'string', 'max:255'],
            "password" => ['required', 'string', Password::min(12)->letters()->mixedCase()->numbers()],
            "agree_policy" => ['required'],
        ];

        // Check if Google reCAPTCHA is enabled
        if (getOption('enable_recaptcha')) {
            $rules['g-recaptcha-response'] = 'required';
        }

        return Validator::make($data, $rules);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        DB::beginTransaction();
        try {
            $remember_token = Str::random(64);

            $google2fa = app('pragmarx.google2fa');

            $createdBy = null;

            $affiliateKey = $data['affiliate_referral_key'] ?? $data['test_affiliate_key'] ?? null;

            if (auth()->check() && auth()->user()->role === USER_ROLE_ADMIN) {
                $createdBy = auth()->id();
            } 
            
            elseif (!empty($affiliateKey)) {

                $affiliate = User::where('affiliate_key', $affiliateKey)
                    ->where('role', USER_ROLE_AFFILIATE)
                    ->where('status', STATUS_ACTIVE)
                    ->first();

                if ($affiliate) {
                    session(['affiliate_id' => $affiliate->id]);
                    $createdBy = $affiliate->id;
                }
            }

            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => USER_ROLE_STUDENT,
                'remember_token' => $remember_token,
                'status' => USER_STATUS_ACTIVE,
                'verify_token' => str_replace('-', '', Str::uuid()->toString()),
                'google2fa_secret' => $google2fa->generateSecretKey(),
                'parent_id' => $createdBy,
            ]);

            // Handle affiliate referral if present (check both hidden field and test field)


            DB::commit();
            return $user;
        } catch (\Throwable $e) {
            DB::rollBack();

            \Log::error('User registration failed', [
                'email' => $data['email'] ?? null,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Handle the registration request.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        // Validate reCAPTCHA
        if (getOption('enable_recaptcha') && !$this->validateRecaptcha($request->input('g-recaptcha-response'), $request->ip())) {
            return redirect()->back()->with('error', 'Please complete the reCAPTCHA again.');
        }

        // Create the user
        $user = $this->create($request->all());

        if ($user) {
            // Log the user in and trigger the Registered event
            $this->guard()->login($user);
            $request->session()->regenerate();
            event(new Registered($user));

            return $this->registered($request, $user) ?: redirect($this->redirectPath());
        }

        // If user creation failed, redirect back with an error message
        return redirect()->back()->with('error', 'Failed to create user. Please try again.');
    }

    /**
     * Validate the reCAPTCHA response.
     *
     * @param string $recaptchaResponse
     * @param string $ip
     * @return bool
     */
    private function validateRecaptcha(string $recaptchaResponse, string $ip): bool
    {
        $url = "https://www.google.com/recaptcha/api/siteverify";
        $response = Http::asForm()->post($url, [
            'secret' => getOption('recaptcha_secret'),
            'response' => $recaptchaResponse,
            'remoteip' => $ip,
        ]);

        if ($response->successful()) {
            $result = json_decode($response->body());
            return $result->success;
        }

        return false;
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }
}
