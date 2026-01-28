    <div class="space-y-6">
        {{-- Header --}}
        <div>
            <flux:heading size="xl">Dashboard Siswa</flux:heading>
            <flux:text class="mt-1 text-zinc-500">Selamat datang, {{ auth()->user()->name }}!</flux:text>
        </div>

        {{-- Stats --}}
        <div class="grid gap-4 sm:grid-cols-2">
            <flux:card class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900">
                    <flux:icon.document-text class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <flux:text class="text-sm text-zinc-500">Ujian Selesai</flux:text>
                    <flux:heading size="xl">{{ $totalExamsTaken }}</flux:heading>
                </div>
            </flux:card>

            <flux:card class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100 dark:bg-green-900">
                    <flux:icon.chart-bar class="h-6 w-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <flux:text class="text-sm text-zinc-500">Rata-rata Nilai</flux:text>
                    <flux:heading size="xl">{{ $averageScore }}</flux:heading>
                </div>
            </flux:card>
        </div>

        {{-- Available Exams --}}
        <flux:card>
            <flux:heading size="lg" class="mb-4">Ujian Tersedia</flux:heading>

            @if($availableExams->count() > 0)
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($availableExams as $exam)
                        <div class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                            <div class="mb-2">
                                <flux:badge color="purple" size="sm">{{ $exam->subject->name }}</flux:badge>
                            </div>
                            <flux:heading size="base" class="mb-2">{{ $exam->title }}</flux:heading>
                            <div class="mb-4 space-y-1 text-sm text-zinc-500">
                                <div class="flex items-center gap-2">
                                    <flux:icon.clock class="h-4 w-4" />
                                    <span>{{ $exam->duration_minutes }} menit</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <flux:icon.clipboard-document-list class="h-4 w-4" />
                                    <span>{{ $exam->total_questions }} soal</span>
                                </div>
                            </div>

                            @if($exam->is_completed)
                                <div class="flex items-center justify-between">
                                    <flux:badge color="green" size="sm">Selesai</flux:badge>
                                    @php
                                        $scoreColor = match(true) {
                                            $exam->student_session->score >= 80 => 'green',
                                            $exam->student_session->score >= 60 => 'amber',
                                            default => 'red'
                                        };
                                    @endphp
                                    <flux:badge :color="$scoreColor">Nilai: {{ $exam->student_session->score }}</flux:badge>
                                </div>
                            @elseif($exam->is_ongoing)
                                <flux:button href="{{ route('siswa.exam.take', $exam->id) }}" wire:navigate class="w-full" variant="primary">
                                    Lanjutkan Ujian
                                </flux:button>
                            @else
                                <flux:button href="{{ route('siswa.exam.take', $exam->id) }}" wire:navigate class="w-full">
                                    Mulai Ujian
                                </flux:button>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <flux:text class="text-zinc-500">Tidak ada ujian yang tersedia saat ini.</flux:text>
            @endif
        </flux:card>

        {{-- Recent Results --}}
        <flux:card>
            <flux:heading size="lg" class="mb-4">Hasil Ujian Terakhir</flux:heading>
            @if($myResults->count() > 0)
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>Ujian</flux:table.column>
                        <flux:table.column>Mata Pelajaran</flux:table.column>
                        <flux:table.column>Tanggal</flux:table.column>
                        <flux:table.column>Nilai</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach($myResults as $result)
                            <flux:table.row>
                                <flux:table.cell class="font-medium">{{ $result->exam->title }}</flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge color="purple" size="sm">{{ $result->exam->subject->name }}</flux:badge>
                                </flux:table.cell>
                                <flux:table.cell>{{ $result->created_at->format('d M Y H:i') }}</flux:table.cell>
                                <flux:table.cell>
                                    @php
                                        $scoreColor = match(true) {
                                            $result->score >= 80 => 'green',
                                            $result->score >= 60 => 'amber',
                                            default => 'red'
                                        };
                                    @endphp
                                    <flux:badge :color="$scoreColor" size="sm">{{ $result->score }}</flux:badge>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            @else
                <flux:text class="text-zinc-500">Belum ada hasil ujian.</flux:text>
            @endif
        </flux:card>
    </div>
