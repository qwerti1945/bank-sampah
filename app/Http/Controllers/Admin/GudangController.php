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
        // Tangkap parameter filter dari frontend
        $search = $request->input('search');
        $month = $request->input('month');
        $year = $request->input('year');

        // Menggunakan keunggulan Eloquent dengan aggregates withSum() dan closure filter
        $wastes = Waste::withSum(['depositDetails as total_in' => function ($query) use ($month, $year) {
                // Filter berdasarkan bulan/tahun pada tabel parent (deposits)
                if ($month || $year) {
                    $query->whereHas('deposit', function ($q) use ($month, $year) {
                        if ($month) $q->whereMonth('created_at', $month);
                        if ($year) $q->whereYear('created_at', $year);
                    });
                }
            }], 'qty')
            ->withSum(['collectorSaleDetails as total_out' => function ($query) use ($month, $year) {
                // Filter berdasarkan bulan/tahun pada tabel parent (collector_sales)
                if ($month || $year) {
                    $query->whereHas('collectorSale', function ($q) use ($month, $year) {
                        // Kolom sold_at digunakan berdasarkan form penjualan pengepul
                        if ($month) $q->whereMonth('sold_at', $month);
                        if ($year) $q->whereYear('sold_at', $year);
                    });
                }
            }], 'qty')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('category', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        // Mutasi koleksi data untuk menghitung sisa stok bersih di gudang pada periode tersebut
        $wastes->getCollection()->transform(function ($waste) {
            $incoming = (float) ($waste->total_in ?? 0);
            $outgoing = (float) ($waste->total_out ?? 0);
            
            // Current stock pada filter ini merepresentasikan "Perubahan Stok" di bulan/tahun tersebut
            // Jika Anda ingin ini tetap menjadi total real-time tanpa peduli filter, 
            // pisahkan query current_stock di luar filter $month/$year.
            $waste->current_stock = max(0, $incoming - $outgoing);
            return $waste;
        });

        return Inertia::render('Admin/Gudang/Index', [
            'stocks' => $wastes,
            'filters' => [
                'search' => $search,
                'month'  => $month,
                'year'   => $year,
            ]
        ]);
    }
}