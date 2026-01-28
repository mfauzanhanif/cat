<div>
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <flux:heading size="xl">Manajemen User</flux:heading>
                <flux:text class="mt-1 text-zinc-500">Kelola data siswa, guru, dan admin</flux:text>
            </div>
            <flux:button wire:click="openCreateModal" icon="plus">
                Tambah User
            </flux:button>
        </div>

        {{-- Flash Messages --}}
        @if (session('success'))
            <flux:callout variant="success" icon="check-circle">
                {{ session('success') }}
            </flux:callout>
        @endif

        @if (session('error'))
            <flux:callout variant="danger" icon="x-circle">
                {{ session('error') }}
            </flux:callout>
        @endif

        {{-- Filters --}}
        <flux:card>
            <div class="flex flex-col gap-4 sm:flex-row">
                <div class="flex-1">
                    <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari nama atau email..." icon="magnifying-glass" />
                </div>
                <div class="w-full sm:w-48">
                    <flux:select wire:model.live="roleFilter">
                        <option value="">Semua Role</option>
                        <option value="admin">Admin</option>
                        <option value="guru">Guru</option>
                        <option value="siswa">Siswa</option>
                    </flux:select>
                </div>
            </div>
        </flux:card>

        {{-- Table --}}
        <flux:card>
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Nama</flux:table.column>
                    <flux:table.column>Email</flux:table.column>
                    <flux:table.column>Role</flux:table.column>
                    <flux:table.column>Dibuat</flux:table.column>
                    <flux:table.column class="text-right">Aksi</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse($users as $user)
                        <flux:table.row>
                            <flux:table.cell>
                                <div class="flex items-center gap-3">
                                    <flux:avatar :name="$user->name" :initials="$user->initials()" size="sm" />
                                    <span>{{ $user->name }}</span>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>{{ $user->email }}</flux:table.cell>
                            <flux:table.cell>
                                @php
                                    $badgeColor = match($user->role) {
                                        'admin' => 'red',
                                        'guru' => 'blue',
                                        'siswa' => 'green',
                                        default => 'zinc'
                                    };
                                @endphp
                                <flux:badge :color="$badgeColor" size="sm">
                                    {{ ucfirst($user->role) }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>{{ $user->created_at->format('d M Y') }}</flux:table.cell>
                            <flux:table.cell class="text-right">
                                <flux:button wire:click="openEditModal({{ $user->id }})" size="sm" variant="ghost" icon="pencil" />
                                @if($user->id !== auth()->id())
                                    <flux:button
                                        wire:click="delete({{ $user->id }})"
                                        wire:confirm="Yakin ingin menghapus user ini?"
                                        size="sm"
                                        variant="ghost"
                                        icon="trash"
                                        class="text-red-500 hover:text-red-700"
                                    />
                                @endif
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5" class="text-center text-zinc-500">
                                Tidak ada data user.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>

            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </flux:card>
    </div>

    {{-- Modal --}}
    <flux:modal wire:model="showModal" class="w-full max-w-lg">
        <div class="space-y-6">
            <flux:heading size="lg">
                {{ $isEditing ? 'Edit User' : 'Tambah User Baru' }}
            </flux:heading>

            <form wire:submit="save" class="space-y-4">
                <flux:input
                    wire:model="name"
                    label="Nama Lengkap"
                    placeholder="Masukkan nama lengkap"
                    :error="$errors->first('name')"
                />

                <flux:input
                    wire:model="email"
                    type="email"
                    label="Email"
                    placeholder="Masukkan email"
                    :error="$errors->first('email')"
                />

                <flux:input
                    wire:model="password"
                    type="password"
                    label="{{ $isEditing ? 'Password (kosongkan jika tidak diubah)' : 'Password' }}"
                    placeholder="Masukkan password"
                    :error="$errors->first('password')"
                />

                <flux:select
                    wire:model="role"
                    label="Role"
                    :error="$errors->first('role')"
                >
                    <option value="siswa">Siswa</option>
                    <option value="guru">Guru</option>
                    <option value="admin">Admin</option>
                </flux:select>

                <div class="flex justify-end gap-3 pt-4">
                    <flux:button wire:click="closeModal" variant="ghost">Batal</flux:button>
                    <flux:button type="submit">
                        {{ $isEditing ? 'Simpan Perubahan' : 'Tambah User' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
