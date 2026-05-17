<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $withdrawals = Withdrawal::with(['nasabah', 'admin'])
            ->when($request->input('search'), function ($query, $search) {
                $query->whereHas('nasabah', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Withdrawals/Index', [
            'withdrawals' => $withdrawals,
            'nasabahList' => User::where('role', 'nasabah')->orderBy('name')->get(['id', 'name', 'balance']),
            'filters' => $request->only(['search'])
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nasabah_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|gt:0',
            'photo_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Diwajibkan untuk akuntabilitas berkas tunai
        ], [
            'nasabah_id.required' => 'Nasabah harus dipilih.',
            'amount.required' => 'Nominal penarikan wajib diisi.',
            'amount.gt' => 'Nominal harus lebih besar dari Rp 0.',
            'photo_proof.required' => 'Foto bukti penyerahan uang wajib diunggah.',
        ]);

        $nasabah = User::findOrFail($request->nasabah_id);

        // Validasi Krusial: Cek kecukupan saldo dompet digital nasabah
        if ($request->amount > $nasabah->balance) {
            return back()->withErrors([
                'amount' => 'Saldo tidak mencukupi. Saldo saat ini: Rp ' . number_format($nasabah->balance, 0, ',', '.')
            ]);
        }

        DB::transaction(function () use ($request, $nasabah) {
            // 1. Simpan gambar berkas fisik ke disk storage
            $photoPath = $request->file('photo_proof')->store('withdrawals', 'public');

            // 2. Buat log data penarikan tunai
            Withdrawal::create([
                'nasabah_id' => $nasabah->id,
                'admin_id' => auth()->id(),
                'amount' => $request->amount,
                'photo_proof' => $photoPath
            ]);

            // 3. Potong langsung saldo milik nasabah
            $nasabah->decrement('balance', $request->amount);
        });

        return redirect()->route('admin.withdrawals.index')
            ->with('success', "Penarikan tunai sebesar Rp " . number_format($request->amount, 0, ',', '.') . " berhasil diproses.");
    }

    public function destroy(Withdrawal $withdrawal)
    {
        // Pembatalan penarikan: Mengembalikan saldo uang ke dompet nasabah
        DB::transaction(function () use ($withdrawal) {
            $nasabah = User::findOrFail($withdrawal->nasabah_id);
            
            // Kembalikan uangnya
            $nasabah->increment('balance', $withdrawal->amount);

            // Hapus foto fisik berkas
            if ($withdrawal->photo_proof) {
                Storage::disk('public')->delete($withdrawal->photo_proof);
            }

            $withdrawal->delete();
        });

        return redirect()->route('admin.withdrawals.index')
            ->with('success', 'Transaksi penarikan dibatalkan, saldo dikembalikan ke rekening nasabah.');
    }
}