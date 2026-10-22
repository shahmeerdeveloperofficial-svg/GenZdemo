<?php

namespace App\DataTables;

use Illuminate\Database\Eloquent\Builder;

trait FiltersQueryCriteria
{
    protected function filterRecords(): void
    {
        $query = $this->query;
        $base = $query instanceof Builder ? $query->getQuery() : $query;
        $table = $query instanceof Builder ? $query->getModel()->getTable() : $base->from;
        $metadata = [];
        $fields = [];
        $studentColumn = null;
        // Aggregated reports keep their own filters. Never interpolate a requested SQL column.
        if (is_string($table) && preg_match('/^[a-z_]+$/i', $table) && !$base->groups && !$base->unions) {
            $columns = $base->getConnection()->getSchemaBuilder()->getColumnListing($table);
            foreach (Criteria::fields() as $field => $label) {
                if (in_array($field, $columns, true)) $fields[$field] = $table . '.' . $field;
            }
            $foreignKeys = [
                'study_level' => ['study_level_id'], 'studyLevel' => ['study_level_id'],
                'student' => ['student_id', 'user_id'], 'studentName' => ['student_id', 'user_id'],
                'consultant' => ['consulter_id'], 'consulter' => ['consulter_id'],
                'country' => ['country_id'], 'university' => ['university_id'],
                'program' => ['program_id'], 'programTitle' => ['program_id'], 'service' => ['service_id'],
            ];
            foreach ($foreignKeys as $field => $candidates) {
                if (!array_key_exists($field, Criteria::fields()) || isset($fields[$field])) continue;
                foreach ($candidates as $candidate) {
                    if (in_array($candidate, $columns, true)) {
                        $fields[$field] = $table . '.' . $candidate;
                        break;
                    }
                }
            }
            $studentKey = [
                'appointments' => 'user_id', 'payments' => 'user_id', 'transactions' => 'user_id',
                'student_service_orders' => 'student_id', 'student_service_order_invoices' => 'student_id',
                'consulter_feedback' => 'student_id', 'course_enrollments' => 'user_id',
            ][$table] ?? null;
            if ($studentKey) {
                $studentColumn = $table . '.' . $studentKey;
                $branches = \App\Models\User::whereIn('id', (clone $query)->reorder()->select($studentColumn))
                    ->select('branch')->distinct()->orderBy('branch')->pluck('branch');
                if ($branches->isNotEmpty()) {
                    $metadata[] = ['key' => 'branch', 'label' => __('Branch'), 'options' => $branches->map(fn ($branch) => [
                        'value' => filled($branch) ? $branch : '__unassigned__',
                        'label' => filled($branch) ? $branch : __('Not assigned'),
                    ])->all()];
                }
            }
            foreach ($base->columns ?? [] as $selection) {
                if (is_string($selection) && preg_match('/^([a-z_]+\.[a-z_]+)\s+as\s+([a-z_]+)$/i', $selection, $match)
                    && array_key_exists($match[2], Criteria::fields())) {
                    $fields[$match[2]] = $match[1];
                }
            }
            foreach ($fields as $field => $column) {
                $samples = (clone $query)->reorder()->select($column . ' as criteria_value')
                    ->selectRaw('MIN(' . $base->getGrammar()->wrap($table . '.id') . ') as criteria_id')
                    ->groupBy($column)->get();
                if ($samples->isEmpty()) continue;
                $records = (clone $query)->whereIn($table . '.id', $samples->pluck('criteria_id'))->get();
                $processed = $this->processResults($records, true);
                $rendered = [];
                foreach ($records->values() as $index => $record) {
                    $rendered[(string) data_get($record, 'id')] = $processed[$index] ?? [];
                }
                $options = $samples->map(function ($sample) use ($rendered, $field) {
                    $raw = data_get($sample, 'criteria_value');
                    $label = Criteria::text(data_get($rendered[(string) data_get($sample, 'criteria_id')] ?? [], $field, $raw));
                    return [
                        'value' => $raw === null || $raw === '' ? '__unassigned__' : (string) $raw,
                        'label' => $label === '' ? __('Not assigned') : $label,
                    ];
                })->sortBy('label', SORT_NATURAL | SORT_FLAG_CASE)->values()->all();
                $metadata[] = ['key' => $field, 'label' => __(Criteria::LABELS[$field]), 'options' => $options];
            }
        }
        $this->with('criteriaOptions', $metadata);
        $changed = false;
        foreach (Criteria::selected() as $field => $value) {
            $definition = collect($metadata)->firstWhere('key', $field);
            if ($definition && !in_array((string) $value, array_column($definition['options'], 'value'), true)) {
                $query->whereRaw('1 = 0');
                $changed = true;
                continue;
            }
            if ($field === 'branch' && $studentColumn) {
                $students = \App\Models\User::query();
                \App\Support\ListCriteria::applyBranch($students, 'branch', (string) $value);
                $query->whereIn($studentColumn, $students->select('id'));
                $changed = true;
                continue;
            }
            if (!isset($fields[$field])) continue;
            $column = $fields[$field];
            if ($value === '__unassigned__') {
                $query->where(fn ($q) => $q->whereNull($column)->orWhere($column, ''));
            } else {
                $query->where($column, $value);
            }
            $changed = true;
        }
        parent::filterRecords();
        // Yajra's parent compares its initial query after our criteria were applied.
        if ($changed) $this->filteredRecords = $this->count();
    }
}
