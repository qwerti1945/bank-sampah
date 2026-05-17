<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CollectorSale;
use App\Models\Waste;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CollectorSaleController extends Controller
{
    /**
     * Menampilkan riwayat penjualan komersial gudang.
     */
    public function index(Request $request)
    {
        $sales = CollectorSale::with(['admin', 'details.waste'])
            ->latest()
            ->paginate(10);

        // KONDISI KHUSUS: Mengambil jenis sampah yang MINIMAL sudah pernah disetor 1x oleh nasabah
        $availableWastes = Waste::whereHas('depositDetails')
            ->orderBy('name')
            ->get(['id', 'name', 'unit', 'current_price']);

        return Inertia::render('Admin/CollectorSales/Index', [
            'sales' => $sales,
            'wasteList' => $availableWastes
        ]);
    }

    /**
     * Membukukan transaksi penjualan baru beserta file nota fisik pengepul.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sold_at' => 'required|date',
            'photo_proof' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'items' => 'required|array|min:1',
            'items.*.waste_id' => 'required|exists:wastes,id',
            'items.*.qty' => 'required|numeric|gt:0',
            'items.*.price_per_unit' => 'required|numeric|gt:0',
        ], [
            'sold_at.required' => 'Tanggal penjualan wajib ditentukan.',
            'items.required' => 'Minimal harus ada 1 jenis komoditas barang yang dijual.',
            'items.*.qty.gt' => 'Berat kuantitas item harus lebih besar dari 0.',
            'items.*.price_per_unit.gt' => 'Harga jual pengepul wajib diisi di atas Rp 0.',
        ]);

        DB::transaction(function () use ($request) {
            $totalAmount = 0;
            $calculatedDetails = [];

            // Iterasi kalkulasi subtotal baris item dinamis
            foreach ($request->items as $item) {
                $subtotal = $item['qty'] * $item['price_per_unit'];
                $totalAmount += $subtotal;

                $calculatedDetails[] = [
                    'waste_id' => $item['waste_id'],
                    'qty' => $item['qty'],
                    'price_per_unit' => $item['price_per_unit'],
                    'subtotal' => $subtotal
                ];
            }

            // Manajemen upload gambar nota fisik
            $photoPath = null;
            if ($request->hasFile('photo_proof')) {
                $photoPath = $request->file('photo_proof')->store('collector_sales', 'public');
            }

            // 1. Simpan Header Nota Penjualan
            $sale = CollectorSale::create([
                'admin_id' => auth()->id(),
                'sold_at' => $request->sold_at,
                'total_amount' => $totalAmount,
                'photo_proof' => $photoPath
            ]);

            // 2. Simpan Seluruh Rincian Komoditas Jual
            foreach ($calculatedDetails as $detail) {
                $sale->details()->create($detail);
            }
        });

        return redirect()->route('admin.collector-sales.index')
            ->with('success', 'Pembukuan penjualan komoditas gudang ke pengepul besar berhasil disimpan.');
    }

    /**
     * Membatalkan / menghapus rekaman nota log niaga dari arsip.
     */
    public function destroy(CollectorSale $collectorSale)
    {
        DB::transaction(function () use ($collectorSale) {
            // Bersihkan file fisik jaminan di storage disk sebelum record dihapus
            if ($collectorSale->photo_proof) {
                Storage::disk('public')->delete($collectorSale->photo_proof);
            }
            $collectorSale->delete();
        });

        return redirect()->route('admin.collector-sales.index')
            ->with('success', 'Catatan log keuangan penjualan pengepul berhasil dibersihkan.');
    }
}