<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\User;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $filterRole = '';

    public function deleteUser($userId)
    {
        if ($userId === auth()->id()) {
            $this->dispatch('notify', message: 'Tidak bisa menghapus akun sendiri');
            return;
        }

        User::findOrFail($userId)->delete();
        $this->dispatch('notify', message: 'User dihapus');
    }

    public function with()
    {
        $query = User::query();

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%");
        }

        if ($this->filterRole) {
            $query->where('role', $this->filterRole);
        }

        return [
            'users' => $query->orderBy('created_at', 'desc')->paginate(20),
        ];
    }
};

?>

    <div>
<div class="min-h-screen bg-kl-warm">
        <div class="bg-white border-b border-kl">
            <div class="max-w-7xl mx-auto px-6 py-6">
                <h1 class="kl-section-title mb-1">👥 Manajemen Users</h1>
                <p class="text-gray-600 text-sm">Total {{ $users->total() }} pengguna</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 py-8">
            <div class="kl-card p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input
                        type="text"
                        wire:model.live="search"
                        placeholder="Cari nama atau email..."
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-kl-primary"
                    />
                    <select
                        wire:model.live="filterRole"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-kl-primary"
                    >
                        <option value="">Semua Role</option>
                        <option value="buyer">Buyer</option>
                        <option value="seller">Seller</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </div>

            <div class="kl-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-kl">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Nama</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Email</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Role</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Bergabung</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-kl">
                            @foreach ($users as $user)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold
                                            {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : '' }}
                                            {{ $user->role === 'seller' ? 'bg-blue-100 text-blue-700' : '' }}
                                            {{ $user->role === 'buyer' ? 'bg-green-100 text-green-700' : '' }}
                                        ">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $user->created_at->format('d M Y') }}</td>
                                    <td class="px-6 py-4 text-center">
                                        @if ($user->id !== auth()->id())
                                            <button
                                                wire:click="deleteUser({{ $user->id }})"
                                                wire:confirm="Hapus user {{ $user->name }}?"
                                                class="text-red-600 hover:text-red-800 font-semibold text-sm transition"
                                            >
                                                Hapus
                                            </button>
                                        @else
                                            <span class="text-gray-400 text-sm">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-kl bg-gray-50">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>

</div>
