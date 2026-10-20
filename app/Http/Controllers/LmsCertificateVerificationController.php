<?php

namespace App\Http\Controllers;

use App\Models\LmsCertificate;

class LmsCertificateVerificationController extends Controller
{
    public function show(string $token)
    {
        $certificate=LmsCertificate::where('verification_token',$token)->with('course')->first();
        abort_unless($certificate,404);
        return response()->view('certificates.verify',compact('certificate'))->header('Cache-Control','no-store, private');
    }
}
