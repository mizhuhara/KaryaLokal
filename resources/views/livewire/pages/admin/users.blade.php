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

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif font-semibold text-xl text-neutral-900 leading-tight">
            Manajemen Users
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search & Filter -->
            <div class="bg-white overflow-hidden shadow-card sm:rounded-lg p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <input
                            type="text"
                            wire:model.live="search"
                            placeholder="Cari nama atau email..."
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400"
                        />
                    </div>
                    <div>
                        <select
                            wire:model.live="filterRole"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400"
                        >
                            <option value="">Semua Role</option>
                            <option value="buyer">Buyer</option>
                            <option value="seller">Seller</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-sm text-gray-600">{{ $users->total() }} users</span>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="bg-white overflow-hidden shadow-card sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Joined</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($users as $user)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm">{{ $user->id }}</td>
                                    <td class="px-6 py-4">
                                        <p class="font-semibold">{{ $user->name }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                                            {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : '' }}
                                            {{ $user->role === 'seller' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $user->role === 'buyer' ? 'bg-green-100 text-green-800' : '' }}
                                        ">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $user->created_at->format('d M Y') }}</td>
                                    <td class="px-6 py-4">
                                        @if ($user->id !== auth()->id())
                                            <button
                                                wire:click="deleteUser({{ $user->id }})"
                                                wire:confirm="Hapus user {{ $user->name }}?"
                                                class="text-red-600 hover:text-red-800 text-sm"
                                            >
                                                Hapus
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
