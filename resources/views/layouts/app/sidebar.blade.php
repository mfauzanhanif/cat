<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky collapsible="mobile"
        class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.header>
            <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
            <flux:sidebar.collapse class="lg:hidden" />
        </flux:sidebar.header>

        <flux:sidebar.nav>
            @php $user = auth()->user(); @endphp

            {{-- ADMIN MENU --}}
            @if($user->role === 'admin')
            <flux:sidebar.group :heading="__('Admin')" class="grid">
                <flux:sidebar.item icon="home" :href="route('admin.dashboard')"
                    :current="request()->routeIs('admin.dashboard')" wire:navigate>
                    {{ __('Dashboard') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="users" :href="route('admin.users')"
                    :current="request()->routeIs('admin.users')" wire:navigate>
                    {{ __('Manajemen User') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="book-open" :href="route('admin.subjects')"
                    :current="request()->routeIs('admin.subjects')" wire:navigate>
                    {{ __('Mata Pelajaran') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="clipboard-document-list" :href="route('admin.questions')"
                    :current="request()->routeIs('admin.questions')" wire:navigate>
                    {{ __('Bank Soal') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="document-text" :href="route('admin.exams')"
                    :current="request()->routeIs('admin.exams')" wire:navigate>
                    {{ __('Manajemen Ujian') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="chart-bar" :href="route('admin.results')"
                    :current="request()->routeIs('admin.results')" wire:navigate>
                    {{ __('Hasil Ujian') }}
                </flux:sidebar.item>
            </flux:sidebar.group>
            @endif

            {{-- GURU MENU --}}
            @if($user->role === 'guru')
            <flux:sidebar.group :heading="__('Guru')" class="grid">
                <flux:sidebar.item icon="home" :href="route('guru.dashboard')"
                    :current="request()->routeIs('guru.dashboard')" wire:navigate>
                    {{ __('Dashboard') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="clipboard-document-list" :href="route('guru.questions')"
                    :current="request()->routeIs('guru.questions')" wire:navigate>
                    {{ __('Bank Soal') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="document-text" :href="route('guru.exams')"
                    :current="request()->routeIs('guru.exams')" wire:navigate>
                    {{ __('Manajemen Ujian') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="chart-bar" :href="route('guru.results')"
                    :current="request()->routeIs('guru.results')" wire:navigate>
                    {{ __('Hasil Ujian') }}
                </flux:sidebar.item>
            </flux:sidebar.group>
            @endif

            {{-- SISWA MENU --}}
            @if($user->role === 'siswa')
            <flux:sidebar.group :heading="__('Siswa')" class="grid">
                <flux:sidebar.item icon="home" :href="route('siswa.dashboard')"
                    :current="request()->routeIs('siswa.dashboard')" wire:navigate>
                    {{ __('Dashboard') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="chart-bar" :href="route('siswa.results')"
                    :current="request()->routeIs('siswa.results')" wire:navigate>
                    {{ __('Hasil Ujian') }}
                </flux:sidebar.item>
            </flux:sidebar.group>
            @endif
        </flux:sidebar.nav>

        <flux:spacer />

        <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" />

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                        class="w-full cursor-pointer" data-test="logout-button">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    @fluxScripts
</body>

</html>