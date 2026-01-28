    <div class="space-y-6">
        {{-- Header --}}
        <div>
            <flux:heading size="xl">Hasil Ujian Saya</flux:heading>
            <flux:text class="mt-1 text-zinc-500">Riwayat dan hasil ujian yang telah Anda kerjakan</flux:text>
        </div>

        {{-- Stats --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <flux:card class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900">
                    <flux:icon.document-text class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <flux:text class="text-sm text-zinc-500">Total Ujian</flux:text>
                    <flux:heading size="xl">{{ $totalExams }}</flux:heading>
                </div>
            </flux:card>

            <flux:card class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900">
                    <flux:icon.chart-bar class="h-6 w-6 text-amber-600 dark:text-amber-400" />
                </div>
                <div>
                    <flux:text class="text-sm text-zinc-500">Rata-rata</flux:text>
                    <flux:heading size="xl">{{ $averageScore }}</flux:heading>
                </div>
            </flux:card>

            <flux:card class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100 dark:bg-green-900">
                    <flux:icon.arrow-trending-up class="h-6 w-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <flux:text class="text-sm text-zinc-500">Nilai Tertinggi</flux:text>
                    <flux:heading size="xl">{{ $highestScore }}</flux:heading>
                </div>
            </flux:card>

            <flux:card class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-red-100 dark:bg-red-900">
                    <flux:icon.arrow-trending-down class="h-6 w-6 text-red-600 dark:text-red-400" />
                </div>
                <div>
                    <flux:text class="text-sm text-zinc-500">Nilai Terendah</flux:text>
                    <flux:heading size="xl">{{ $lowestScore }}</flux:heading>
                </div>
            </flux:card>
        </div>

        {{-- Search --}}
        <flux:card>
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari judul ujian..." icon="magnifying-glass" />
        </flux:card>

        {{-- Table --}}
        <flux:card>
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Ujian</flux:table.column>
                    <flux:table.column>Mata Pelajaran</flux:table.column>
                    <flux:table.column>Tanggal</flux:table.column>
                    <flux:table.column>Durasi</flux:table.column>
                    <flux:table.column>Nilai</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse($sessions as $session)
                        <flux:table.row>
                            <flux:table.cell class="font-medium">{{ $session->exam->title ?? '-' }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge color="purple" size="sm">{{ $session->exam->subject->name ?? '-' }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>{{ $session->created_at->format('d M Y H:i') }}</flux:table.cell>
                            <flux:table.cell>
                                @if($session->start_time && $session->end_time)
                                    {{ $session->start_time->diffInMinutes($session->end_time) }} menit
                                @else
                                    -
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                @php
                                    $scoreColor = match(true) {
                                        $session->score >= 80 => 'green',
                                        $session->score >= 60 => 'amber',
                                        default => 'red'
                                    };
                                @endphp
                                <flux:badge :color="$scoreColor" size="sm">{{ $session->score }}</flux:badge>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5" class="text-center text-zinc-500">
                                Belum ada hasil ujian.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>

            <div class="mt-4">
                {{ $sessions->links() }}
            </div>
        </flux:card>
    </div>
