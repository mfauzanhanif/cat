<div class="space-y-6" x-data="examTimer({{ $remainingSeconds }})" x-init="startTimer()">
    {{-- Header with Timer --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl">{{ $exam->title }}</flux:heading>
            <flux:text class="mt-1 text-zinc-500">
                {{ $exam->subject->name }} • {{ count($questions) }} Soal • {{ $exam->duration_minutes }} Menit
            </flux:text>
        </div>

        {{-- Timer --}}
        <div class="flex items-center gap-3 rounded-lg bg-zinc-100 px-4 py-2 dark:bg-zinc-800">
            <flux:icon.clock class="h-5 w-5"
                x-bind:class="remainingSeconds < 300 ? 'text-red-500 animate-pulse' : ''" />
            <span class="font-mono text-lg font-bold" x-bind:class="remainingSeconds < 300 ? 'text-red-500' : ''"
                x-text="formatTime(remainingSeconds)"></span>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-4">
        {{-- Question Navigation --}}
        <div class="order-2 lg:order-1 lg:col-span-1">
            <flux:card>
                <flux:heading size="base" class="mb-3">Navigasi Soal</flux:heading>
                <div class="grid grid-cols-5 gap-2">
                    @foreach($questions as $index => $question)
                    <button wire:click="goToQuestion({{ $index }})" class="flex h-10 w-10 items-center justify-center rounded-lg border text-sm font-medium transition-colors
                                    {{ $currentQuestionIndex === $index ? 'bg-blue-600 text-white border-blue-600' : '' }}
                                    {{ isset($answers[$question['answer_id']]) && $answers[$question['answer_id']] ? 'bg-green-100 border-green-500 text-green-700 dark:bg-green-900 dark:text-green-300' : '' }}
                                    {{ $currentQuestionIndex !== $index && (!isset($answers[$question['answer_id']]) || !$answers[$question['answer_id']]) ? 'border-zinc-300 dark:border-zinc-600 hover:bg-zinc-100 dark:hover:bg-zinc-700' : '' }}
                                ">
                        {{ $index + 1 }}
                    </button>
                    @endforeach
                </div>

                <div class="mt-4 space-y-2 text-sm">
                    <div class="flex items-center gap-2">
                        <div class="h-4 w-4 rounded bg-green-500"></div>
                        <span class="text-zinc-600 dark:text-zinc-400">Sudah dijawab</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="h-4 w-4 rounded border border-zinc-300 dark:border-zinc-600"></div>
                        <span class="text-zinc-600 dark:text-zinc-400">Belum dijawab</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="h-4 w-4 rounded bg-blue-600"></div>
                        <span class="text-zinc-600 dark:text-zinc-400">Soal aktif</span>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:text class="text-sm text-zinc-500">
                        Terjawab: {{ collect($answers)->filter()->count() }} / {{ count($questions) }}
                    </flux:text>
                </div>
            </flux:card>

            {{-- Submit Button --}}
            <div class="mt-4">
                <flux:button wire:click="submitExam"
                    wire:confirm="Yakin ingin mengumpulkan ujian? Anda tidak dapat mengubah jawaban setelah dikumpulkan."
                    class="w-full" variant="primary">
                    Kumpulkan Ujian
                </flux:button>
            </div>
        </div>

        {{-- Current Question --}}
        <div class="order-1 lg:order-2 lg:col-span-3">
            @if(count($questions) > 0 && isset($questions[$currentQuestionIndex]))
            @php $currentQuestion = $questions[$currentQuestionIndex]; @endphp

            <flux:card>
                <div class="mb-4 flex items-center justify-between">
                    <flux:badge color="purple">Soal {{ $currentQuestionIndex + 1 }} dari {{ count($questions) }}
                    </flux:badge>
                </div>

                {{-- Question Content --}}
                <div class="mb-6">
                    <flux:heading size="lg" class="leading-relaxed">
                        {!! nl2br(e($currentQuestion['content'])) !!}
                    </flux:heading>

                    {{-- Question Image --}}
                    @if(isset($currentQuestion['image_path']) && $currentQuestion['image_path'])
                    <div class="mt-4">
                        <img src="{{ asset('storage/'.$currentQuestion['image_path']) }}" alt="Gambar Soal"
                            class="max-w-full h-auto rounded-lg border border-zinc-300 dark:border-zinc-700 shadow-sm">
                    </div>
                    @endif
                </div>

                {{-- Options --}}
                <div class="space-y-3">
                    @foreach(['A' => 'option_a', 'B' => 'option_b', 'C' => 'option_c', 'D' => 'option_d'] as $letter =>
                    $optionKey)
                    <button wire:click="selectAnswer({{ $currentQuestion['answer_id'] }}, '{{ $letter }}')" class="flex w-full items-start gap-3 rounded-lg border p-4 text-left transition-all hover:bg-zinc-50 dark:hover:bg-zinc-800
                                        {{ ($answers[$currentQuestion['answer_id']] ?? null) === $letter
                                            ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/30 ring-2 ring-blue-500'
                                            : 'border-zinc-200 dark:border-zinc-700'
                                        }}
                                    ">
                        <span class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full border-2 font-bold
                                        {{ ($answers[$currentQuestion['answer_id']] ?? null) === $letter
                                            ? 'border-blue-500 bg-blue-500 text-white'
                                            : 'border-zinc-300 dark:border-zinc-600'
                                        }}
                                    ">
                            {{ $letter }}
                        </span>
                        <span class="pt-1">{{ $currentQuestion[$optionKey] }}</span>
                    </button>
                    @endforeach
                </div>

                {{-- Navigation Buttons --}}
                <div class="mt-6 flex justify-between">
                    <flux:button wire:click="previousQuestion" variant="ghost" :disabled="$currentQuestionIndex === 0">
                        <flux:icon.chevron-left class="h-4 w-4" />
                        Sebelumnya
                    </flux:button>

                    <flux:button wire:click="nextQuestion" :disabled="$currentQuestionIndex === count($questions) - 1">
                        Selanjutnya
                        <flux:icon.chevron-right class="h-4 w-4" />
                    </flux:button>
                </div>
            </flux:card>
            @else
            <flux:card>
                <flux:text class="text-zinc-500">Tidak ada soal yang tersedia.</flux:text>
            </flux:card>
            @endif
        </div>
    </div>
</div>

{{-- Timer Script --}}
<script>
    function examTimer(initialSeconds) {
        return {
            remainingSeconds: initialSeconds,
            timerInterval: null,

            startTimer() {
                this.timerInterval = setInterval(() => {
                    if (this.remainingSeconds > 0) {
                        this.remainingSeconds--;
                    } else {
                        this.stopTimer();
                        // Dispatch event to Livewire to auto-submit
                        Livewire.dispatch('timer-expired');
                    }
                }, 1000);
            },

            stopTimer() {
                if (this.timerInterval) {
                    clearInterval(this.timerInterval);
                }
            },

            formatTime(seconds) {
                const hours = Math.floor(seconds / 3600);
                const minutes = Math.floor((seconds % 3600) / 60);
                const secs = seconds % 60;

                if (hours > 0) {
                    return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
                }
                return `${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
            }
        }
    }
</script>