@extends('frontend.layouts.app')

@section('title', 'Registration Received | GenZ Consultants')

@section('content')
<main style="min-height:55vh;display:grid;place-items:center;padding:48px 18px;background:#f4f9ff">
    <section style="max-width:650px;text-align:center;background:#fff;padding:clamp(28px,5vw,52px);border-radius:16px;box-shadow:0 14px 38px #07357912">
        <span style="display:inline-grid;place-items:center;width:56px;height:56px;border-radius:50%;background:#e8f8ff;color:#073579;font-size:28px">✓</span>
        <h1 style="color:#073579;margin:18px 0 10px">Registration received</h1>
        <p style="color:#64748b">Thank you. Our counselling team will review your details and contact you. The fee schedule shown on the form was demo-only and is not a payment request.</p>
        <a href="{{ route('frontend') }}" style="display:inline-block;margin-top:16px;padding:12px 22px;background:#073579;color:#fff;border-radius:6px">Back to home</a>
    </section>
</main>
@endsection
