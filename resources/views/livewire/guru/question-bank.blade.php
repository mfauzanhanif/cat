<div>
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <flux:heading size="xl">Bank Soal</flux:heading>
                <flux:text class="mt-1 text-zinc-500">Kelola bank soal untuk mata pelajaran Anda</flux:text>
            </div>
            @if($subjects->count() > 0)
                <flux:button wire:click="openCreateModal" icon="plus">
                    Tambah Soal
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
                        <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari isi soal..." icon="magnifying-glass" />
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
                        <flux:table.column>Mata Pelajaran</flux:table.column>
                        <flux:table.column>Pertanyaan</flux:table.column>
                        <flux:table.column>Jawaban</flux:table.column>
                        <flux:table.column class="text-right">Aksi</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse($questions as $question)
                            <flux:table.row>
                                <flux:table.cell>
                                    <flux:badge color="purple" size="sm">{{ $question->subject->name }}</flux:badge>
                                </flux:table.cell>
                                <flux:table.cell class="max-w-md">
                                    <div class="truncate">{{ Str::limit($question->content, 80) }}</div>
                                </flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge color="green" size="sm">{{ $question->correct_answer }}</flux:badge>
                                </flux:table.cell>
                                <flux:table.cell class="text-right">
                                    <flux:button wire:click="openEditModal({{ $question->id }})" size="sm" variant="ghost" icon="pencil" />
                                    <flux:button
                                        wire:click="delete({{ $question->id }})"
                                        wire:confirm="Yakin ingin menghapus soal ini?"
                                        size="sm"
                                        variant="ghost"
                                        icon="trash"
                                        class="text-red-500 hover:text-red-700"
                                    />
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="4" class="text-center text-zinc-500">
                                    Tidak ada data soal.
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>

                <div class="mt-4">
                    {{ $questions->links() }}
                </div>
            </flux:card>
        @endif
    </div>

    {{-- Modal --}}
    <flux:modal wire:model="showModal" class="w-full max-w-2xl">
        <div class="space-y-6">
            <flux:heading size="lg">
                {{ $isEditing ? 'Edit Soal' : 'Tambah Soal Baru' }}
            </flux:heading>

            <form wire:submit="save" class="space-y-4">
                <flux:select
                    wire:model="subject_id"
                    label="Mata Pelajaran"
                    :error="$errors->first('subject_id')"
                >
                    <option value="">-- Pilih Mata Pelajaran --</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                    @endforeach
                </flux:select>

                <flux:textarea
                    wire:model="content"
                    label="Pertanyaan"
                    placeholder="Masukkan pertanyaan..."
                    rows="3"
                    :error="$errors->first('content')"
                />

                <div class="grid gap-4 sm:grid-cols-2">
                    <flux:input
                        wire:model="option_a"
                        label="Opsi A"
                        placeholder="Jawaban A"
                        :error="$errors->first('option_a')"
                    />
                    <flux:input
                        wire:model="option_b"
                        label="Opsi B"
                        placeholder="Jawaban B"
                        :error="$errors->first('option_b')"
                    />
                    <flux:input
                        wire:model="option_c"
                        label="Opsi C"
                        placeholder="Jawaban C"
                        :error="$errors->first('option_c')"
                    />
                    <flux:input
                        wire:model="option_d"
                        label="Opsi D"
                        placeholder="Jawaban D"
                        :error="$errors->first('option_d')"
                    />
                </div>

                <flux:select
                    wire:model="correct_answer"
                    label="Kunci Jawaban"
                    :error="$errors->first('correct_answer')"
                >
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </flux:select>

                <div class="flex justify-end gap-3 pt-4">
                    <flux:button wire:click="closeModal" variant="ghost">Batal</flux:button>
                    <flux:button type="submit">
                        {{ $isEditing ? 'Simpan Perubahan' : 'Tambah Soal' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
