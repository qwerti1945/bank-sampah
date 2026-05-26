<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Waste; 
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $user->load([
            'deposits' => fn($q) => $q->latest(),
            'deposits.details.waste',
            'withdrawals' => fn($q) => $q->latest(),
        ]);

        // Diurutkan berdasarkan nama saja karena kolom category tidak ada
        $wastes = Waste::orderBy('name')->get();

        return Inertia::render('Nasabah/Dashboard', [
            'nasabah' => [
                'name' => $user->name,
                'balance' => (float) $user->balance,
            ],
            'deposits' => $user->deposits,
            'withdrawals' => $user->withdrawals,
            'wastes' => $wastes, 
        ]);
    }
}