<?php

namespace App\Filters;

class TaskFilter
{
    public static function apply($query, $request)
    {
        // filter status
        if($request->has('status')){
            $query->where('status', $request->status);
        }

        // search by title
        if($request->has('search')){
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // sorting (safe column only)
        $allowedSorts = ['title', 'created_at', 'updated_at'];
        if($request->has('sort') && in_array($request->sort, $allowedSorts)){
            $query->orderBy($request->sort);
        }
        else {
            $query->latest();
        }

        return $query;

    }
}
