<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kompania;
use App\Models\Personi;
use App\Models\RaundiFinancimit;
use App\Models\Investim;
use App\Models\Fond;
use App\Models\Blerje;

class DashboardController extends Controller
{
    public function index()
    {
        $total_kompanite = Kompania::count();
        $total_personat = Personi::count();
        $total_raundet = RaundiFinancimit::count();
        $total_investimet = Investim::count();
        $total_fondet = Fond::count();
        $total_blerjet = Blerje::count();

        return view('dashboard.index', compact(
            'total_kompanite',
            'total_personat',
            'total_raundet',
            'total_investimet',
            'total_fondet',
            'total_blerjet'
        ));
    }
}
