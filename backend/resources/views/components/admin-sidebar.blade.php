<aside class="w-64 bg-[var(--bgSecondary)] border-r border-[var(--border)] min-h-[calc(100vh-80px)] p-6">
    <div class="flex flex-col gap-2">
        <a href="{{ route('admin.dashboard') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-[var(--primary)] text-white' : 'text-[var(--textPrimary)] hover:bg-[var(--bgTertiary)]' }}">
            <i class="ph ph-chart-line text-xl"></i>
            <span class="font-semibold">Dashboard</span>
        </a>

        <a href="{{ route('admin.users') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('admin.users') ? 'bg-[var(--primary)] text-white' : 'text-[var(--textPrimary)] hover:bg-[var(--bgTertiary)]' }}">
            <i class="ph ph-users text-xl"></i>
            <span class="font-semibold">Users</span>
        </a>

        <a href="{{ route('admin.announcements') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('admin.announcements') ? 'bg-[var(--primary)] text-white' : 'text-[var(--textPrimary)] hover:bg-[var(--bgTertiary)]' }}">
            <i class="ph ph-megaphone text-xl"></i>
            <span class="font-semibold">Announcements</span>
        </a>

        <a href="{{ route('admin.reports') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('admin.reports') ? 'bg-[var(--primary)] text-white' : 'text-[var(--textPrimary)] hover:bg-[var(--bgTertiary)]' }}">
            <i class="ph ph-clipboard-text text-xl"></i>
            <span class="font-semibold">Reports</span>
        </a>

        <div class="mt-8 pt-8 border-t border-[var(--border)]">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" 
                        class="flex items-center gap-3 px-4 py-3 rounded-xl text-[var(--danger)] hover:bg-[var(--bgTertiary)] transition-colors w-full text-left font-semibold">
                    <i class="ph ph-sign-out text-xl"></i>
                    Logout
                </button>
            </form>
        </div>
    </div>
</aside>
