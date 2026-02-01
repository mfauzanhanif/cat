<div>
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <flux:heading size="xl">Bank Soal</flux:heading>
                <flux:text class="mt-1 text-zinc-500">Kelola bank soal untuk mata pelajaran Anda</flux:text>
            </div>
            @if($subjects->count() > 0)
            <div class="flex gap-2">
                <flux:button wire:click="openImportModal" variant="ghost" icon="arrow-up-tray">
                    Import Excel
                </flux:button>
                <flux:button wire:click="openCreateModal" icon="plus">
                    Tambah Soal
                </flux:button>
            </div>
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
                    <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari isi soal..."
                        icon="magnifying-glass" />
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
                            <div class="flex items-center gap-2">
                                <div class="truncate">{{ Str::limit($question->content, 80) }}</div>
                                @if($question->image_path)
                                <flux:icon.photo class="h-4 w-4 flex-shrink-0 text-blue-500"
                                    title="Soal memiliki gambar" />
                                @endif
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge color="green" size="sm">{{ $question->correct_answer }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="text-right">
                            <flux:button wire:click="openEditModal({{ $question->id }})" size="sm" variant="ghost"
                                icon="pencil" />
                            <flux:button wire:click="delete({{ $question->id }})"
                                wire:confirm="Yakin ingin menghapus soal ini?" size="sm" variant="ghost" icon="trash"
                                class="text-red-500 hover:text-red-700" />
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
                <flux:select wire:model="subject_id" label="Mata Pelajaran" :error="$errors->first('subject_id')">
                    <option value="">-- Pilih Mata Pelajaran --</option>
                    @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                    @endforeach
                </flux:select>

                <flux:textarea wire:model="content" label="Pertanyaan" placeholder="Masukkan pertanyaan..." rows="3"
                    :error="$errors->first('content')" />

                {{-- Input Gambar & Preview --}}
                <div class="space-y-2">
                    <flux:input type="file" wire:model="image" label="Gambar Pendukung (Opsional)" accept="image/*"
                        :error="$errors->first('image')" />

                    {{-- Preview gambar yang baru diupload --}}
                    @if ($image)
                    <div class="mt-2">
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-1">Preview Gambar Baru:</p>
                        <img src="{{ $image->temporaryUrl() }}" alt="Preview"
                            class="h-32 w-auto rounded-lg border border-zinc-300 dark:border-zinc-700">
                    </div>
                    {{-- Preview gambar yang sudah ada saat mode edit --}}
                    @elseif ($existingImage)
                    <div class="mt-2">
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-1">Gambar Saat Ini:</p>
                        <img src="{{ asset('storage/'.$existingImage) }}" alt="Existing"
                            class="h-32 w-auto rounded-lg border border-zinc-300 dark:border-zinc-700">
                    </div>
                    @endif
                    <div wire:loading wire:target="image" class="text-sm text-zinc-500 dark:text-zinc-400">Sedang
                        mengupload...</div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <flux:input wire:model="option_a" label="Opsi A" placeholder="Jawaban A"
                        :error="$errors->first('option_a')" />
                    <flux:input wire:model="option_b" label="Opsi B" placeholder="Jawaban B"
                        :error="$errors->first('option_b')" />
                    <flux:input wire:model="option_c" label="Opsi C" placeholder="Jawaban C"
                        :error="$errors->first('option_c')" />
                    <flux:input wire:model="option_d" label="Opsi D" placeholder="Jawaban D"
                        :error="$errors->first('option_d')" />
                </div>

                <flux:select wire:model="correct_answer" label="Kunci Jawaban"
                    :error="$errors->first('correct_answer')">
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

    {{-- Modal Import --}}
    <flux:modal wire:model="showImportModal" class="w-full max-w-lg">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Import Soal</flux:heading>
                <flux:text class="mt-1 text-zinc-500">
                    Upload file Excel (.xlsx, .xls) atau CSV. Pastikan format header sesuai.
                    <br><code
                        class="text-xs bg-zinc-100 dark:bg-zinc-800 px-1 py-0.5 rounded">no, mata_pelajaran, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, kunci_jawaban</code>
                    <br><span class="text-xs mt-1 block">Kolom <strong>no</strong> bersifat opsional dan akan
                        diabaikan.</span>
                </flux:text>

                <flux:button wire:click="downloadTemplate" variant="ghost" icon="arrow-down-tray" size="sm"
                    class="mt-2">
                    Download Template Excel
                </flux:button>
            </div>

            <form wire:submit="importExcel" class="space-y-4">
                <flux:input type="file" wire:model="importFile" label="File Excel/CSV"
                    accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                    :error="$errors->first('importFile')" />
                <div wire:loading wire:target="importFile" class="text-sm text-zinc-500 dark:text-zinc-400">Sedang
                    memproses file...</div>

                <div class="flex justify-end gap-3 pt-4">
                    <flux:button wire:click="closeImportModal" variant="ghost">Batal</flux:button>
                    <flux:button type="submit" wire:loading.attr="disabled" wire:target="importExcel">
                        Mulai Import
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>