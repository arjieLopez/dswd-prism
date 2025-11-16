<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;

class QueryService
{
    /**
     * Apply search filter to query
     *
     * @param Builder $query
     * @param string|null $searchTerm
     * @param array $fields
     * @return Builder
     */
    public function applySearch(Builder $query, ?string $searchTerm, array $fields): Builder
    {
        if (!$searchTerm) {
            return $query;
        }

        return $query->where(function ($q) use ($searchTerm, $fields) {
            foreach ($fields as $field) {
                // Handle relationship fields (e.g., 'user.name')
                if (str_contains($field, '.')) {
                    [$relation, $attribute] = explode('.', $field, 2);
                    $q->orWhereHas($relation, function ($subQuery) use ($attribute, $searchTerm) {
                        $subQuery->where($attribute, 'like', "%{$searchTerm}%");
                    });
                } else {
                    $q->orWhere($field, 'like', "%{$searchTerm}%");
                }
            }
        });
    }

    /**
     * Apply status filter to query
     *
     * @param Builder $query
     * @param string|null $status
     * @return Builder
     */
    public function applyStatusFilter(Builder $query, ?string $status): Builder
    {
        if (!$status || $status === 'all') {
            return $query;
        }

        return $query->whereHas('status', function ($q) use ($status) {
            $q->where('name', $status);
        });
    }

    /**
     * Apply date range filter to query
     *
     * @param Builder $query
     * @param string|null $dateFrom
     * @param string|null $dateTo
     * @param string $field
     * @return Builder
     */
    public function applyDateRangeFilter(Builder $query, ?string $dateFrom, ?string $dateTo, string $field = 'created_at'): Builder
    {
        if ($dateFrom) {
            $query->whereDate($field, '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate($field, '<=', $dateTo);
        }

        return $query;
    }

    /**
     * Apply sorting to query
     *
     * @param Builder $query
     * @param string $sortBy
     * @param string $sortOrder
     * @return Builder
     */
    public function applySorting(Builder $query, string $sortBy = 'created_at', string $sortOrder = 'desc'): Builder
    {
        return $query->orderBy($sortBy, $sortOrder);
    }

    /**
     * Apply pagination preserving query string
     *
     * @param Builder $query
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function applyPagination(Builder $query, int $perPage = 10)
    {
        return $query->paginate($perPage)->withQueryString();
    }
}
