<aside class="links">
    <ul>
        <li>
            <i class="ph ph-gauge"></i>
            <a href="{{ route('user.dashboard') }}" class="{{ request()->routeIs('user.dashboard') ? 'active' : '' }}">My Impact</a>
        </li>
        <li>
            <i class="ph ph-shop"></i>
            <a href="{{ route('marketplace') }}">Marketplace</a>
        </li>
        <li>
            <i class="ph ph-list"></i>
            <a href="{{ route('user.listings') }}" class="{{ request()->routeIs('user.listings') ? 'active' : '' }}">My Announcements</a>
        </li>
        <li>
            <i class="ph ph-user"></i>
            <a href="{{ route('user.profile') }}" class="{{ request()->routeIs('user.profile') ? 'active' : '' }}">Profile Settings</a>
        </li>
        <li>
            <i class="ph ph-sign-out"></i>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </li>
    </ul>
</aside>
