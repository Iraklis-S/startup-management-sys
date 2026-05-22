<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Kompania;
use Illuminate\Http\Request;

class CompanyLookupController extends Controller
{
   public function search(Request $request)
    {
       $q = trim($request->query('q', ''));

        $kompanite = Kompania::query()
            ->select('id', 'name')
            ->whereNotNull('name')
            ->when($q !== '', function ($query) use ($q) {
                $query->where('name', 'like', '%' . $q . '%')
                    ->orderByRaw(
                        "CASE
                        WHEN LOWER(name) = LOWER(?) THEN 0
                        WHEN LOWER(name) LIKE LOWER(?) THEN 1
                        ELSE 2
                    END",
                        [$q, $q . '%']
                    )
                    ->orderByRaw('LENGTH(name) ASC')
                    ->orderBy('name');
            }, function ($query) {
                $query->orderBy('name');
            })
            ->limit(10)
            ->get()
            ->map(fn($k) => [
                'id' => $k->id,
                'text' => $k->name . ' (#' . $k->id . ')',
            ]);

        return response()->json([
            'results' => $kompanite,
        ]);
    }
}
