<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\RoomSupervisor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Tampilkan daftar user (panitia & pengawas) dengan filter dan statistik.
     */
    public function index(Request $request)
    {
        $totalUsers = User::count();
        $totalPanitia = User::where('role', 'panitia')->count();
        $totalPengawas = User::where('role', 'pengawas')->count();
        $totalAssignments = RoomSupervisor::count();

        $query = User::query()->withCount('pengawasan');

        // Pencarian Nama / Email
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter Role
        if ($request->filled('role') && in_array($request->role, ['panitia', 'pengawas'])) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('role', 'asc')
                       ->orderBy('name', 'asc')
                       ->paginate(12)
                       ->withQueryString();

        return view('admin.users.index', compact(
            'users',
            'totalUsers',
            'totalPanitia',
            'totalPengawas',
            'totalAssignments'
        ));
    }

    /**
     * Simpan user baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'role' => 'required|in:panitia,pengawas',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role harus berupa panitia atau pengawas.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        $roleLabel = $validated['role'] === 'panitia' ? 'Panitia Ujian' : 'Pengawas Ruangan';
        return back()->with('success', "Pengguna '{$validated['name']}' berhasil ditambahkan sebagai {$roleLabel}.");
    }

    /**
     * Perbarui data user yang ada.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:panitia,pengawas',
            'password' => 'nullable|string|min:6|confirmed',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role harus berupa panitia atau pengawas.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        // Safeguard: Jangan izinkan user menurunkan role dirinya sendiri jika dia panitia
        if ($user->id === Auth::id() && $validated['role'] !== 'panitia') {
            return back()->with('error', 'Anda tidak dapat mengubah peran akun Anda sendiri menjadi non-panitia.');
        }

        // Safeguard: Jangan biarkan panitia terakhir diubah jadi pengawas
        if ($user->role === 'panitia' && $validated['role'] !== 'panitia') {
            $panitiaCount = User::where('role', 'panitia')->count();
            if ($panitiaCount <= 1) {
                return back()->with('error', 'Perubahan dibatalkan. Sistem harus memiliki minimal satu akun Panitia aktif.');
            }
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        // Jika password diisi, update password baru
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return back()->with('success', "Data pengguna '{$user->name}' berhasil diperbarui.");
    }

    /**
     * Hapus user dari database.
     */
    public function destroy(User $user)
    {
        // Safeguard: Cegah hapus akun sendiri
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif digunakan.');
        }

        // Safeguard: Cegah hapus akun panitia terakhir
        if ($user->role === 'panitia') {
            $panitiaCount = User::where('role', 'panitia')->count();
            if ($panitiaCount <= 1) {
                return back()->with('error', 'Tidak dapat menghapus akun panitia terakhir. Sistem membutuhkan setidaknya satu panitia.');
            }
        }

        $name = $user->name;
        $user->delete();

        return back()->with('success', "Akun '{$name}' berhasil dihapus dari sistem.");
    }
}
