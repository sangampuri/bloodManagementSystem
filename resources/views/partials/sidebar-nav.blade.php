@php
    // Admin pages live under /admin. That decides which menu shows.
    $navItems = $isAdmin
        ? [
            ['Dashboard',       'admin',           'layout-dashboard'],
            ['Users',           'admin/users',     'users'],
            ['Donors',          'admin/donors',    'heart-pulse'],
            ['Blood Inventory', 'admin/inventory', 'boxes'],
            ['Blood Requests',  'admin/requests',  'clipboard-list'],
            ['Reports',         'admin/reports',   'file-text'],
            ['Profile',         'admin/profile',   'user'],
        ]
        : [
            ['Dashboard',          'dashboard',       'layout-dashboard'],
            ['My Profile',         'profile',         'user'],
            ['Become a Donor',     'donor',           'heart-pulse'],
            ['Donor List',         'donors',          'users'],
            ['Blood Availability', 'availability',    'droplets'],
            ['Blood Requests',     'requests/create', 'clipboard-plus'],
            ['My Requests',        'my-requests',     'clipboard-list'],
            ['Notifications',      'notifications',   'bell'],
        ];
@endphp

<ul class="space-y-0.5">
    @foreach ($navItems as [$label, $path, $icon])
        @php
            // '/admin' must match only itself, or it would light up on every admin page.
            $active = $path === 'admin' ? request()->is('admin') : request()->is($path, $path . '/*');
        @endphp
        <li>
            <a href="{{ url($path) }}"
               @class([
                   'flex items-center gap-3 rounded-md px-3 py-2',
                   'bg-brand-50 font-medium text-brand-700' => $active,
                   'text-stone-700 hover:bg-stone-100' => ! $active,
               ])
               @if ($active) aria-current="page" @endif>
                <x-dynamic-component :component="'lucide-' . $icon" class="h-4 w-4 shrink-0" />
                {{ $label }}
            </a>
        </li>
    @endforeach
</ul>

<form method="POST" action="{{ url('/logout') }}" class="mt-6 border-t border-line pt-4">
    @csrf
    <button type="submit" class="flex w-full items-center gap-3 rounded-md px-3 py-2 text-stone-700 hover:bg-stone-100">
        <x-lucide-log-out class="h-4 w-4 shrink-0" />
        Log out
    </button>
</form>