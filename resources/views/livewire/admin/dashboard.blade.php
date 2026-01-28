    <div class="space-y-6">
        {{-- Header --}}
        <div>
            <flux:heading size="xl">Dashboard Admin</flux:heading>
            <flux:text class="mt-1 text-zinc-500">Selamat datang, {{ auth()->user()->name }}!</flux:text>
        </div>

        {{-- Stats Grid --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            {{-- Total Siswa --}}
            <flux:card class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900">
                    <flux:icon.users class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <flux:text class="text-sm text-zinc-500">Total Siswa</flux:text>
                    <flux:heading size="xl">{{ $totalSiswa }}</flux:heading>
                </div>
            </flux:card>

            {{-- Total Guru --}}
            <flux:card class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100 dark:bg-green-900">
                    <flux:icon.academic-cap class="h-6 w-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <flux:text class="text-sm text-zinc-500">Total Guru</flux:text>
                    <flux:heading size="xl">{{ $totalGuru }}</flux:heading>
                </div>
            </flux:card>

            {{-- Total Mata Pelajaran --}}
            <flux:card class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900">
                    <flux:icon.book-open class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                </div>
                <div>
                    <flux:text class="text-sm text-zinc-500">Mata Pelajaran</flux:text>
                    <flux:heading size="xl">{{ $totalSubjects }}</flux:heading>
                </div>
            </flux:card>

            {{-- Total Soal --}}
            <flux:card class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900">
                    <flux:icon.clipboard-document-list class="h-6 w-6 text-amber-600 dark:text-amber-400" />
                </div>
                <div>
                    <flux:text class="text-sm text-zinc-500">Bank Soal</flux:text>
                    <flux:heading size="xl">{{ $totalQuestions }}</flux:heading>
                </div>
            </flux:card>

            {{-- Total Ujian --}}
            <flux:card class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-rose-100 dark:bg-rose-900">
                    <flux:icon.document-text class="h-6 w-6 text-rose-600 dark:text-rose-400" />
                </div>
                <div>
                    <flux:text class="text-sm text-zinc-500">Total Ujian</flux:text>
                    <flux:heading size="xl">{{ $totalExams }}</flux:heading>
                </div>
            </flux:card>

            {{-- Ujian Aktif --}}
            <flux:card class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-teal-100 dark:bg-teal-900">
                    <flux:icon.play-circle class="h-6 w-6 text-teal-600 dark:text-teal-400" />
                </div>
                <div>
                    <flux:text class="text-sm text-zinc-500">Ujian Aktif</flux:text>
                    <flux:heading size="xl">{{ $activeExams }}</flux:heading>
                </div>
            </flux:card>
        </div>

        {{-- Recent Exam Sessions --}}
        <flux:card>
            <flux:heading size="lg" class="mb-4">Aktivitas Ujian Terbaru</flux:heading>
            @if($recentSessions->count() > 0)
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Siswa</flux:table.column>
                        <flux:table.column>Ujian</flux:table.column>
                        <flux:table.column>Status</flux:table.column>
                        <flux:table.column>Nilai</flux:table.column>
                        <flux:table.column>Waktu</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach($recentSessions as $session)
                            <flux:table.row>
                                <flux:table.cell>{{ $session->user->name ?? '-' }}</flux:table.cell>
                                <flux:table.cell>{{ $session->exam->title ?? '-' }}</flux:table.cell>
                                <flux:table.cell>
                                    @if($session->status === 'completed')
                                        <flux:badge color="green" size="sm">Selesai</flux:badge>
                                    @else
                                        <flux:badge color="amber" size="sm">Berlangsung</flux:badge>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell>{{ $session->score ?? '-' }}</flux:table.cell>
                                <flux:table.cell>{{ $session->created_at->diffForHumans() }}</flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            @else
                <flux:text class="text-zinc-500">Belum ada aktivitas ujian.</flux:text>
            @endif
        </flux:card>
    </div>
