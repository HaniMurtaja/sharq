<?php

namespace App\Traits;

trait QueryBuilderTrait
{

    protected function applyFilters($query, $request, $fields = [])
    {
        foreach ($fields as $field => $type) {
            if (!$request->filled($field)) {
                continue;
            }

            $value = $request->input($field);

            if ($field === 'sort_date') {
                $this->applySort($query, $value);
                continue;
            }

            if (in_array($field, ['active', 'is_paid', 'is_read','admin_featured'], true)) {
                $query->where($field, $value);
                continue;
            }

            if (is_array($type)) {
                $this->applyArrayFilter($query, $value, $type);
            } else {
                $this->applyFilterType($query, $field, $type, $value);
            }
        }

        if (!$request->filled('sort_date')) {
            $this->applySort($query, 'desc');
        }

        return $query;
    }

    private function applySort($query, $sortDirection)
    {
        $query->orderBy('created_at', $sortDirection === 'desc' ? 'desc' : 'asc');
    }

    private function applyArrayFilter($query, $value, $type)
    {
        $query->where(function ($subquery) use ($value, $type) {
            foreach ($type as $columnName) {
                $subquery->orWhere($columnName, 'like', '%' . $value . '%');
            }
        });
    }

    private function applyFilterType($query, $field, $type, $value)
    {
        switch ($type) {
            case 'like':
                $query->where($field, 'like', '%' . $value . '%');
                break;
            case 'equal':
                $query->where($field, $value);
                break;
            case 'not_equal':
                $query->where($field, '<>', $value);
                break;
        }
    }
}
