<?php

namespace App\DataTables;

class CollectionDataTable extends \Yajra\DataTables\CollectionDataTable
{
    public function make($mDataSupport = true): \Illuminate\Http\JsonResponse
    {
        $first = $this->original->first();
        if ($first instanceof \Illuminate\Database\Eloquent\Model) {
            $relation = match ($first->getTable()) {
                'consulter_feedback', 'student_service_orders', 'student_service_order_invoices' => 'student',
                'appointments', 'payments', 'transactions', 'course_enrollments' => 'user',
                default => null,
            };
            if ($relation && $first->relationLoaded($relation)) {
                $this->addColumn('branch', fn ($row) => $row->getRelation($relation)?->branch);
            }
        }
        return parent::make($mDataSupport);
    }

    protected function filterRecords(): void
    {
        $metadata = [];
        foreach (Criteria::fields() as $field => $label) {
            if (!$this->collection->contains(fn ($row) => array_key_exists($field, (array) $row))) continue;
            $values = $this->collection->map(fn ($row) => Criteria::text(data_get($row, $field)))
                ->unique()->sort(SORT_NATURAL | SORT_FLAG_CASE)->values();
            if ($field !== 'branch') $values = $values->filter(fn ($value) => $value !== '');
            $metadata[] = [
                'key' => $field, 'label' => __($label),
                'options' => $values->map(fn ($value) => [
                    'value' => $value === '' ? '__unassigned__' : $value,
                    'label' => $value === '' ? __('Not assigned') : $value,
                ])->values()->all(),
            ];
        }
        $this->with('criteriaOptions', $metadata);
        $supported = array_column($metadata, 'key');
        foreach (Criteria::selected() as $field => $value) {
            if (!in_array($field, $supported, true)) continue;
            $this->collection = $this->collection->filter(function ($row) use ($field, $value) {
                return Criteria::text(data_get($row, $field)) === ($value === '__unassigned__' ? '' : (string) $value);
            });
        }
        parent::filterRecords();
    }
}
