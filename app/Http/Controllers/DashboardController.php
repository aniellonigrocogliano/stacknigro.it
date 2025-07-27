<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visit;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Visite totali
        $totalVisits = Visit::count();

        // Visite uniche totali
        $uniqueTotalVisits = Visit::distinct('ip_address')->count('ip_address');

        // Visite di oggi
        $todayVisits = Visit::whereDate('created_at', today())->count();

        // Visite della settimana (ultimi 7 giorni)
        $weekVisits = Visit::where('created_at', '>=', Carbon::now()->subDays(7))->count();

        // Visite del mese corrente
        $monthVisits = Visit::whereMonth('created_at', Carbon::now()->month)->count();

        // Dati per grafico ultimi 7 giorni (tutte le visite)
        $last7Days = [];
        $last7DaysUnique = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $last7Days[] = [
                'date' => $date->format('d M'),
                'visits' => Visit::whereDate('created_at', $date)->count()
            ];
            $last7DaysUnique[] = [
                'date' => $date->format('d M'),
                'visits' => Visit::whereDate('created_at', $date)
                                ->distinct('ip_address')
                                ->count('ip_address')
            ];
        }

        return view('backend.dashboard', [
            'totalVisits' => $totalVisits,
            'uniqueTotalVisits' => $uniqueTotalVisits,
            'todayVisits' => $todayVisits,
            'weekVisits' => $weekVisits,
            'monthVisits' => $monthVisits,
            'last7Days' => $last7Days,
            'last7DaysUnique' => $last7DaysUnique
        ]);
    }
}
