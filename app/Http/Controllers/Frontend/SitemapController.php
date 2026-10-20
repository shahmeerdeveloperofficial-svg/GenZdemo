<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\AcademicProgram;
use App\Models\Country;
use App\Models\Course;
use App\Models\Event;
use App\Models\Scholarship;
use App\Models\Service;
use App\Models\Subject;
use App\Models\University;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $entries = [
            ['loc' => route('frontend'), 'lastmod' => now()->toAtomString()],
            ['loc' => route('about-us.details'), 'lastmod' => now()->toAtomString()],
            ['loc' => route('contact-us'), 'lastmod' => now()->toAtomString()],
            ['loc' => route('universities.list'), 'lastmod' => now()->toAtomString()],
            ['loc' => route('academic-programs.list'), 'lastmod' => now()->toAtomString()],
            ['loc' => route('subject.list'), 'lastmod' => now()->toAtomString()],
            ['loc' => route('scholarship.list'), 'lastmod' => now()->toAtomString()],
            ['loc' => route('blog.list'), 'lastmod' => now()->toAtomString()],
            ['loc' => route('event.list'), 'lastmod' => now()->toAtomString()],
            ['loc' => route('consultations.list'), 'lastmod' => now()->toAtomString()],
        ];
        $models = [
            [Country::class, 'country.details'], [University::class, 'universities.details'],
            [Subject::class, 'subject.details'], [Course::class, 'courses.single'],
            [Service::class, 'service.details'], [Event::class, 'event.details'],
            [Blog::class, 'blog.details'], [Scholarship::class, 'scholarship.details'],
        ];
        if (Schema::hasTable('academic_programs')) {
            AcademicProgram::query()->where('status', 'published')->select(['slug', 'updated_at'])->orderBy('id')->chunk(250, function ($records) use (&$entries) {
                foreach ($records as $record) $entries[] = ['loc' => route('academic-programs.details', $record->slug), 'lastmod' => optional($record->updated_at)->toAtomString() ?: now()->toAtomString()];
            });
        }
        foreach ($models as [$model, $routeName]) {
            $query = $model === Course::class ? Course::publiclyVisible() : $model::query()->where('status', STATUS_ACTIVE);
            $query->whereNotNull('slug')->select(['slug', 'updated_at'])->orderBy('id')->chunk(250, function ($records) use (&$entries, $routeName) {
                foreach ($records as $record) $entries[] = ['loc' => route($routeName, $record->slug), 'lastmod' => optional($record->updated_at)->toAtomString() ?: now()->toAtomString()];
            });
        }

        return response()->view('frontend.sitemap', compact('entries'), 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
