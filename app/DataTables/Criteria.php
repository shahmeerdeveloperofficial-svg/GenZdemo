<?php

namespace App\DataTables;

class Criteria
{
    // Only categorical fields intended for the visible list become filters.
    public const LABELS = [
        'branch' => 'Branch', 'status' => 'Status', 'payment_status' => 'Payment status',
        'working_status' => 'Working status', 'consultation_type' => 'Consultation type',
        'type' => 'Type', 'source' => 'Source', 'category' => 'Category',
        'country' => 'Country', 'country_name' => 'Country', 'countryName' => 'Country',
        'university' => 'University', 'university_name' => 'University',
        'program' => 'Program', 'programTitle' => 'Program', 'program_name' => 'Program',
        'categoryName' => 'Category', 'blogCategoryName' => 'Category',
        'study_level' => 'Study level', 'studyLevel' => 'Study level',
        'gatewayName' => 'Payment method', 'gateway' => 'Payment method',
        'payment_method' => 'Payment method', 'purpose' => 'Purpose',
        'consulter' => 'Consultant', 'consultant' => 'Consultant', 'consultantName' => 'Consultant',
        'student' => 'Student', 'studentName' => 'Student', 'role_name' => 'Role', 'service' => 'Service',
    ];

    public static function fields(): array
    {
        $visible = collect(request()->input('columns', []))->pluck('data')->filter()->all();
        return array_intersect_key(self::LABELS, array_flip([...$visible, 'branch']));
    }

    public static function text($value): string
    {
        if (!is_scalar($value) && $value !== null) return '';
        return trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags((string) $value), ENT_QUOTES, 'UTF-8')));
    }

    public static function selected(): array
    {
        $selected = request()->input('criteria', []);
        if (!is_array($selected)) return [];
        return array_filter(array_intersect_key($selected, self::LABELS), fn ($v) => is_scalar($v) && (string) $v !== '' && strlen((string) $v) <= 500);
    }
}
