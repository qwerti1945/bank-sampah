<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Waste;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GudangController extends Controller
{
    public function index(Request $request)
    {
        // Menggunakan keunggulan Eloquent dengan aggregates withSum() untuk performa cepat
        $wastes = Waste::withSum('depositDetails as total_in', 'qty')
            ->withSum('collectorSaleDetails as total_out', 'qty')
            ->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('category', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        // Mutasi koleksi data untuk menghitung sisa stok bersih di gudang
        $wastes->getCollection()->transform(function ($waste) {
            $incoming = (float) ($waste->total_in ?? 0);
            $outgoing = (float) ($waste->total_out ?? 0);
            
            $waste->current_stock = max(0, $incoming - $outgoing);
            return $waste;
        });

        return Inertia::render('Admin/Gudang/Index', [
            'stocks' => $wastes,
            'filters' => $request->only(['search'])
        ]);
    }
}