<div>
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <flux:heading size="xl">Manajemen Ujian</flux:heading>
                <flux:text class="mt-1 text-zinc-500">Setup jadwal ujian untuk mata pelajaran Anda</flux:text>
            </div>
            @if($subjects->count() > 0)
                <flux:button wire:click="openCreateModal" icon="plus">
                    Tambah Ujian
                </flux:button>
            @endif
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

        @if($subjects->count() == 0)
            <flux:callout variant="warning" icon="exclamation-triangle">
                Anda belum ditugaskan ke mata pelajaran apapun. Hubungi Admin untuk penugasan.
            </flux:callout>
        @else
            {{-- Filters --}}
            <flux:card>
                <div class="flex flex-col gap-4 sm:flex-row">
                    <div class="flex-1">
                        <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari judul ujian..." icon="magnifying-glass" />
                    </div>
                    <div class="w-full sm:w-64">
                        <flux:select wire:model.live="subjectFilter">
                            <option value="">Semua Mata Pelajaran Saya</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </flux:select>
                    </div>
                </div>
            </flux:card>

            {{-- Table --}}
            <flux:card>
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Judul</flux:table.column>
                        <flux:table.column>Mata Pelajaran</flux:table.column>
                        <flux:table.column>Durasi</flux:table.column>
                        <flux:table.column>Jumlah Soal</flux:table.column>
                        <flux:table.column>Peserta</flux:table.column>
                        <flux:table.column>Status</flux:table.column>
                        <flux:table.column class="text-right">Aksi</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse($exams as $exam)
                            <flux:table.row>
                                <flux:table.cell class="font-medium">{{ $exam->title }}</flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge color="purple" size="sm">{{ $exam->subject->name }}</flux:badge>
                                </flux:table.cell>
                                <flux:table.cell>{{ $exam->duration_minutes }} menit</flux:table.cell>
                                <flux:table.cell>{{ $exam->total_questions }} soal</flux:table.cell>
                                <flux:table.cell>{{ $exam->sessions->count() }} siswa</flux:table.cell>
                                <flux:table.cell>
                                    <flux:switch
                                        wire:click="toggleActive({{ $exam->id }})"
                                        :checked="$exam->is_active"
                                    />
                                </flux:table.cell>
                                <flux:table.cell class="text-right">
                                    <flux:button wire:click="openEditModal({{ $exam->id }})" size="sm" variant="ghost" icon="pencil" />
                                    <flux:button
                                        wire:click="delete({{ $exam->id }})"
                                        wire:confirm="Yakin ingin menghapus ujian ini?"
                                        size="sm"
                                        variant="ghost"
                                        icon="trash"
                                        class="text-red-500 hover:text-red-700"
                                    />
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="7" class="text-center text-zinc-500">
                                    Tidak ada data ujian.
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>

                <div class="mt-4">
                    {{ $exams->links() }}
                </div>
            </flux:card>
        @endif
    </div>

    {{-- Modal --}}
    <flux:modal wire:model="showModal" class="w-full max-w-lg">
        <div class="space-y-6">
            <flux:heading size="lg">
                {{ $isEditing ? 'Edit Ujian' : 'Tambah Ujian Baru' }}
            </flux:heading>

            <form wire:submit="save" class="space-y-4">
                <flux:select
                    wire:model="subject_id"
                    label="Mata Pelajaran"
                    :error="$errors->first('subject_id')"
                >
                    <option value="">-- Pilih Mata Pelajaran --</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }} ({{ $subject->questions_count }} soal)</option>
                    @endforeach
                </flux:select>

                <flux:input
                    wire:model="title"
                    label="Judul Ujian"
                    placeholder="Contoh: UTS Semester Ganjil"
                    :error="$errors->first('title')"
                />

                <div class="grid gap-4 sm:grid-cols-2">
                    <flux:input
                        wire:model="duration_minutes"
                        type="number"
                        label="Durasi (menit)"
                        min="5"
                        max="300"
                        :error="$errors->first('duration_minutes')"
                    />
                    <flux:input
                        wire:model="total_questions"
                        type="number"
                        label="Jumlah Soal"
                        min="1"
                        max="100"
                        :error="$errors->first('total_questions')"
                    />
                </div>

                <flux:checkbox
                    wire:model="is_active"
                    label="Aktifkan ujian (siswa dapat mengerjakan)"
                />

                <div class="flex justify-end gap-3 pt-4">
                    <flux:button wire:click="closeModal" variant="ghost">Batal</flux:button>
                    <flux:button type="submit">
                        {{ $isEditing ? 'Simpan Perubahan' : 'Tambah Ujian' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
