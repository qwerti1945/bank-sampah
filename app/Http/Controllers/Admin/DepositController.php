<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\User;
use App\Models\Waste;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class DepositController extends Controller
{
    public function index(Request $request)
    {
        $deposits = Deposit::with(['nasabah', 'admin', 'details.waste'])
            ->when($request->input('search'), function ($query, $search) {
                $query->whereHas('nasabah', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Deposits/Index', [
            'deposits' => $deposits,
            'nasabahList' => User::where('role', 'nasabah')->orderBy('name')->get(['id', 'name', 'balance']),
            'wasteList' => Waste::orderBy('name')->get(['id', 'name', 'current_price', 'unit']),
            'filters' => $request->only(['search'])
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nasabah_id' => 'required|exists:users,id',
            'photo_proof' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Batasi gambar max 2MB
            'items' => 'required|array|min:1',
            'items.*.waste_id' => 'required|exists:wastes,id',
            'items.*.qty' => 'required|numeric|gt:0',
        ], [
            'nasabah_id.required' => 'Nama nasabah harus dipilih.',
            'photo_proof.image' => 'Berkas harus berupa gambar foto.',
            'items.required' => 'Wajib memasukkan minimal 1 baris sampah.',
            'items.*.qty.gt' => 'Berat/Jumlah harus lebih besar dari 0.',
        ]);

        DB::transaction(function () use ($request) {
            $nasabah = User::findOrFail($request->nasabah_id);
            $totalAmount = 0;
            $calculatedDetails = [];

            // 1. Handle Upload Foto Bukti Nota jika ada
            $photoPath = null;
            if ($request->hasFile('photo_proof')) {
                $photoPath = $request->file('photo_proof')->store('deposits', 'public');
            }

            // 2. Iterasi kalkulasi item sampah
            foreach ($request->items as $item) {
                $waste = Waste::findOrFail($item['waste_id']);
                $subtotal = $item['qty'] * $waste->current_price;
                $totalAmount += $subtotal;

                $calculatedDetails[] = [
                    'waste_id' => $waste->id,
                    'qty' => $item['qty'],
                    'price_at_transaction' => $waste->current_price,
                    'subtotal' => $subtotal
                ];
            }

            // 3. Simpan Header Nota (Termasuk Path Foto)
            $deposit = Deposit::create([
                'nasabah_id' => $nasabah->id,
                'admin_id' => auth()->id(),
                'total_amount' => $totalAmount,
                'photo_proof' => $photoPath
            ]);

            // 4. Simpan Rincian Baris Sampah
            foreach ($calculatedDetails as $detail) {
                $deposit->details()->create($detail);
            }

            // 5. Tambahkan Nilai Uang Langsung ke Saldo Nasabah
            $nasabah->increment('balance', $totalAmount);
        });

        return redirect()->route('admin.deposits.index')
            ->with('success', 'Nota timbangan multi-item beserta foto bukti berhasil dibukukan.');
    }

    public function destroy(Deposit $deposit)
    {
        DB::transaction(function () use ($deposit) {
            $nasabah = User::findOrFail($deposit->nasabah_id);
            
            // Ambil kembali nominal uang dari rekening saldo nasabah
            $nasabah->decrement('balance', $deposit->total_amount);
            
            // Hapus file foto fisik dari disk server jika ada sebelum data dihapus
            if ($deposit->photo_proof) {
                Storage::disk('public')->delete($deposit->photo_proof);
            }
            
            $deposit->delete();
        });

        return redirect()->route('admin.deposits.index')
            ->with('success', 'Nota setoran berhasil dibatalkan dan berkas foto dibersihkan.');
    }
}