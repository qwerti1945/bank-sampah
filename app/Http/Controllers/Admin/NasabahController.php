<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class NasabahController extends Controller
{
    /**
     * Menampilkan daftar data nasabah dengan fitur pencarian dan paginasi.
     */
    public function index(Request $request)
    {
        // Menyaring user ber-role nasabah dan mencocokkan dengan query pencarian jika ada
        $nasabah = User::where('role', 'nasabah')
            ->when($request->input('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString(); // Memastikan parameter pencarian tidak hilang saat pindah page paginasi

        return Inertia::render('Admin/Nasabah/Index', [
            'nasabah' => $nasabah,
            'filters' => $request->only(['search']) // Mengirim kembali kata kunci pencarian ke Vue
        ]);
    }

    /**
     * Mengubah peran (role) pengguna secara dinamis.
     */
    public function changeRole(Request $request, User $nasabah)
    {
        $validated = $request->validate([
            'role' => 'required|in:admin,super_admin,nasabah'
        ]);

        $nasabah->update([
            'role' => $validated['role']
        ]);

        return redirect()->route('admin.nasabah.index')
            ->with('success', "Peran akun {$nasabah->name} berhasil diubah menjadi " . strtoupper($validated['role']) . ".");
    }

    /**
     * Menyimpan data nasabah baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
        ], [
            'name.required' => 'Nama nasabah wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah terdaftar di sistem.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 6 karakter.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'address.required' => 'Alamat lengkap wajib diisi.',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'role' => 'nasabah',
            'balance' => 0
        ]);

        return redirect()->route('admin.nasabah.index')
            ->with('success', 'Nasabah baru berhasil didaftarkan.');
    }

    /**
     * Memperbarui data profil nasabah.
     */
    public function update(Request $request, User $nasabah)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($nasabah->id)],
            'password' => 'nullable|string|min:6',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
        ], [
            'name.required' => 'Nama nasabah wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah digunakan oleh user lain.',
            'password.min' => 'Password baru minimal 6 karakter.',
            'phone.required' => 'Nomor telepon wajib diisi.',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $nasabah->update($updateData);

        return redirect()->route('admin.nasabah.index')
            ->with('success', 'Profil data nasabah berhasil diperbarui.');
    }

    /**
     * Menghapus akun nasabah dari database.
     */
    public function destroy(User $nasabah)
    {
        $nasabah->delete();

        return redirect()->route('admin.nasabah.index')
            ->with('success', 'Data akun nasabah berhasil dihapus.');
    }
}