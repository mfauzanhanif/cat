    <div class="space-y-6">
        {{-- Header --}}
        <div>
            <flux:heading size="xl">Hasil Ujian</flux:heading>
            <flux:text class="mt-1 text-zinc-500">Lihat hasil nilai semua siswa</flux:text>
        </div>

        {{-- Stats --}}
        <div class="grid gap-4 sm:grid-cols-3">
            <flux:card class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900">
                    <flux:icon.users class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <flux:text class="text-sm text-zinc-500">Total Peserta</flux:text>
                    <flux:heading size="xl">{{ $totalSessions }}</flux:heading>
                </div>
            </flux:card>

            <flux:card class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100 dark:bg-green-900">
                    <flux:icon.check-circle class="h-6 w-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <flux:text class="text-sm text-zinc-500">Selesai</flux:text>
                    <flux:heading size="xl">{{ $completedSessions }}</flux:heading>
                </div>
            </flux:card>

            <flux:card class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900">
                    <flux:icon.chart-bar class="h-6 w-6 text-amber-600 dark:text-amber-400" />
                </div>
                <div>
                    <flux:text class="text-sm text-zinc-500">Rata-rata Nilai</flux:text>
                    <flux:heading size="xl">{{ $averageScore }}</flux:heading>
                </div>
            </flux:card>
        </div>

        {{-- Filters --}}
        <flux:card>
            <div class="flex flex-col gap-4 sm:flex-row">
                <div class="flex-1">
                    <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari nama siswa..." icon="magnifying-glass" />
                </div>
                <div class="w-full sm:w-64">
                    <flux:select wire:model.live="examFilter">
                        <option value="">Semua Ujian</option>
                        @foreach($exams as $exam)
                            <option value="{{ $exam->id }}">{{ $exam->title }}</option>
                        @endforeach
                    </flux:select>
                </div>
                <div class="w-full sm:w-40">
                    <flux:select wire:model.live="statusFilter">
                        <option value="">Semua Status</option>
                        <option value="ongoing">Berlangsung</option>
                        <option value="completed">Selesai</option>
                    </flux:select>
                </div>
            </div>
        </flux:card>

        {{-- Table --}}
        <flux:card>
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Siswa</flux:table.column>
                    <flux:table.column>Ujian</flux:table.column>
                    <flux:table.column>Mata Pelajaran</flux:table.column>
                    <flux:table.column>Mulai</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Nilai</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse($sessions as $session)
                        <flux:table.row>
                            <flux:table.cell>
                                <div class="flex items-center gap-3">
                                    <flux:avatar :name="$session->user->name ?? 'Unknown'" :initials="$session->user?->initials() ?? '?'" size="sm" />
                                    <span>{{ $session->user->name ?? 'Unknown' }}</span>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell class="font-medium">{{ $session->exam->title ?? '-' }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge color="purple" size="sm">{{ $session->exam->subject->name ?? '-' }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>{{ $session->start_time->format('d M Y H:i') }}</flux:table.cell>
                            <flux:table.cell>
                                @if($session->status === 'completed')
                                    <flux:badge color="green" size="sm">Selesai</flux:badge>
                                @else
                                    <flux:badge color="amber" size="sm">Berlangsung</flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                @if($session->status === 'completed')
                                    @php
                                        $scoreColor = match(true) {
                                            $session->score >= 80 => 'green',
                                            $session->score >= 60 => 'amber',
                                            default => 'red'
                                        };
                                    @endphp
                                    <flux:badge :color="$scoreColor" size="sm">{{ $session->score }}</flux:badge>
                                @else
                                    <span class="text-zinc-400">-</span>
                                @endif
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="6" class="text-center text-zinc-500">
                                Belum ada data hasil ujian.
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
