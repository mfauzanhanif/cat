<div>
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <flux:heading size="xl">Manajemen Mata Pelajaran</flux:heading>
                <flux:text class="mt-1 text-zinc-500">Kelola data mata pelajaran dan guru pengampu</flux:text>
            </div>
            <flux:button wire:click="openCreateModal" icon="plus">
                Tambah Mapel
            </flux:button>
        </div>

        {{-- Flash Messages --}}
        @if (session('success'))
            <flux:callout variant="success" icon="check-circle">
                {{ session('success') }}
            </flux:callout>
        @endif

        {{-- Search --}}
        <flux:card>
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari nama atau kode mapel..." icon="magnifying-glass" />
        </flux:card>

        {{-- Table --}}
        <flux:card>
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Kode</flux:table.column>
                    <flux:table.column>Nama</flux:table.column>
                    <flux:table.column>Guru Pengampu</flux:table.column>
                    <flux:table.column>Jumlah Soal</flux:table.column>
                    <flux:table.column>Jumlah Ujian</flux:table.column>
                    <flux:table.column class="text-right">Aksi</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse($subjects as $subject)
                        <flux:table.row>
                            <flux:table.cell>
                                <flux:badge color="zinc" size="sm">{{ $subject->code }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell class="font-medium">{{ $subject->name }}</flux:table.cell>
                            <flux:table.cell>
                                @if($subject->teacher)
                                    <div class="flex items-center gap-2">
                                        <flux:avatar :name="$subject->teacher->name" :initials="$subject->teacher->initials()" size="xs" />
                                        <span>{{ $subject->teacher->name }}</span>
                                    </div>
                                @else
                                    <span class="text-zinc-400">Belum ditugaskan</span>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>{{ $subject->questions->count() }}</flux:table.cell>
                            <flux:table.cell>{{ $subject->exams->count() }}</flux:table.cell>
                            <flux:table.cell class="text-right">
                                <flux:button wire:click="openEditModal({{ $subject->id }})" size="sm" variant="ghost" icon="pencil" />
                                <flux:button
                                    wire:click="delete({{ $subject->id }})"
                                    wire:confirm="Yakin ingin menghapus mata pelajaran ini? Semua soal dan ujian terkait akan ikut terhapus."
                                    size="sm"
                                    variant="ghost"
                                    icon="trash"
                                    class="text-red-500 hover:text-red-700"
                                />
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="6" class="text-center text-zinc-500">
                                Tidak ada data mata pelajaran.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>

            <div class="mt-4">
                {{ $subjects->links() }}
            </div>
        </flux:card>
    </div>

    {{-- Modal --}}
    <flux:modal wire:model="showModal" class="w-full max-w-lg">
        <div class="space-y-6">
            <flux:heading size="lg">
                {{ $isEditing ? 'Edit Mata Pelajaran' : 'Tambah Mata Pelajaran Baru' }}
            </flux:heading>

            <form wire:submit="save" class="space-y-4">
                <flux:input
                    wire:model="code"
                    label="Kode Mata Pelajaran"
                    placeholder="Contoh: MTK-10"
                    :error="$errors->first('code')"
                />

                <flux:input
                    wire:model="name"
                    label="Nama Mata Pelajaran"
                    placeholder="Contoh: Matematika"
                    :error="$errors->first('name')"
                />

                <flux:select
                    wire:model="teacher_id"
                    label="Guru Pengampu"
                    placeholder="Pilih guru pengampu"
                    :error="$errors->first('teacher_id')"
                >
                    <option value="">-- Belum ditugaskan --</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                    @endforeach
                </flux:select>

                <div class="flex justify-end gap-3 pt-4">
                    <flux:button wire:click="closeModal" variant="ghost">Batal</flux:button>
                    <flux:button type="submit">
                        {{ $isEditing ? 'Simpan Perubahan' : 'Tambah Mapel' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
