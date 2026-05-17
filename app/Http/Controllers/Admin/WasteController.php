<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Waste;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WasteController extends Controller
{
    /**
     * Menampilkan daftar semua data sampah dengan paginasi.
     */
    public function index()
    {
        $wastes = Waste::latest()->paginate(10);

        return Inertia::render('Admin/Wastes/Index', [
            'wastes' => $wastes
        ]);
    }

    /**
     * Menyimpan data sampah baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'current_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Nama sampah wajib diisi.',
            'unit.required' => 'Satuan wajib diisi.',
            'current_price.required' => 'Harga wajib diisi.',
            'current_price.numeric' => 'Harga harus berupa angka.',
            'current_price.min' => 'Harga tidak boleh minus.',
        ]);

        Waste::create($validated);

        return redirect()->route('admin.wastes.index')
            ->with('success', 'Data sampah baru berhasil ditambahkan.');
    }

    /**
     * Memperbarui data sampah yang sudah ada.
     */
    public function update(Request $request, Waste $waste)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'current_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Nama sampah wajib diisi.',
            'unit.required' => 'Satuan wajib diisi.',
            'current_price.required' => 'Harga wajib diisi.',
            'current_price.numeric' => 'Harga harus berupa angka.',
            'current_price.min' => 'Harga tidak boleh minus.',
        ]);

        $waste->update($validated);

        return redirect()->route('admin.wastes.index')
            ->with('success', 'Data sampah berhasil diperbarui.');
    }

    /**
     * Menghapus data sampah dari database.
     */
    public function destroy(Waste $waste)
    {
        $waste->delete();

        return redirect()->route('admin.wastes.index')
            ->with('success', 'Data sampah berhasil dihapus.');
    }
}