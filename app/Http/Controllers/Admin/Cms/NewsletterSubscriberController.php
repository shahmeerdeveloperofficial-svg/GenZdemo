<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class NewsletterSubscriberController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:Manage Cms Settings');
    }

    public function index(Request $request)
    {
        $request->validate(['q' => ['nullable', 'string', 'max:190'], 'status' => ['nullable', Rule::in(['subscribed', 'unsubscribed'])]]);
        $subscribers = NewsletterSubscriber::query()
            ->when($request->filled('q'), fn ($q) => $q->where('email', 'like', '%' . $request->string('q')->trim() . '%'))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->latest()->paginate(25)->withQueryString();
        $criteriaFields = [
            ['name' => 'q', 'label' => __('Email'), 'type' => 'search', 'placeholder' => __('Search subscribers')],
            ['name' => 'status', 'label' => __('Subscription status'), 'options' => ['subscribed' => __('Subscribed'), 'unsubscribed' => __('Unsubscribed')]],
        ];
        return view('admin.cms.newsletter-subscribers', compact('subscribers', 'criteriaFields'));
    }

    public function update(Request $request, NewsletterSubscriber $subscriber)
    {
        $request->merge(['email' => Str::lower(trim((string) $request->input('email')))]);
        $data = $request->validate([
            'email' => ['required', 'email:rfc', 'max:190', Rule::unique('newsletter_subscribers', 'email')->ignore($subscriber->id)],
            'status' => ['required', Rule::in(['subscribed', 'unsubscribed'])],
        ]);
        if ($data['status'] !== $subscriber->status) {
            $data['unsubscribed_at'] = $data['status'] === 'unsubscribed' ? now() : null;
            if ($data['status'] === 'subscribed') $data['subscribed_at'] = now();
        }
        $subscriber->update($data);
        return back()->with('success', __('Subscriber updated.'));
    }

    public function destroy(NewsletterSubscriber $subscriber)
    {
        $subscriber->delete();
        return back()->with('success', __('Subscriber deleted.'));
    }
}
