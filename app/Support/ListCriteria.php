<?php

namespace App\Support;

class ListCriteria
{
    public static function periods(): array
    {
        return ['today' => __('Today'), '7' => __('Last 7 days'), '30' => __('Last 30 days'), '90' => __('Last 90 days')];
    }

    public static function applyPeriod($query, string $column, ?string $period): void
    {
        if (!array_key_exists($period ?? '', self::periods())) return;
        $start = $period === 'today' ? now()->startOfDay() : now()->subDays((int) $period - 1)->startOfDay();
        $query->whereBetween($column, [$start, now()]);
    }

    public static function branchOptions(): array
    {
        return array_combine(config('genz.registration.branches'), config('genz.registration.branches'))
            + ['__unassigned__' => __('Not assigned')];
    }

    public static function applyBranch($query, string $column, ?string $branch): void
    {
        if ($branch === '__unassigned__') {
            $query->where(fn ($q) => $q->whereNull($column)->orWhere($column, ''));
        } elseif (filled($branch)) {
            $query->where($column, $branch);
        }
    }
}
