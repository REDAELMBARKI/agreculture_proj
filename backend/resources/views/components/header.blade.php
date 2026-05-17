<header class="header" style="background-color: var(--bgSecondary);">
    <div class="top_navbar" style="border-bottom: 1px solid var(--border);">
        <div class="brand">
            <a href="/" class="brand_logo" style="color: var(--textPrimary);">
                Donate&Sell<i class="ph-bold ph-leaf" style="margin-left: 8px; color: var(--primary); font-size: 24px;"></i>
            </a>
        </div>

        <nav class="main_links" aria-label="Main navigation">
            <a href="/" style="color: var(--textSecondary);">Home</a>
            <a href="/announcements" style="color: var(--textSecondary);">Marketplace</a>
            <a href="/our_partners" style="color: var(--textSecondary);">Our Partners</a>
            <a href="/faq" style="color: var(--textSecondary);">FAQ</a>
            <a href="/faq_chatbot" style="color: var(--textSecondary);">FAQ Chatbot</a>
        </nav>

        <div class="nav_actions" style="display: flex; align-items: center; gap: 12px;">
            @auth
                <a href="/add_announcement" class="post_btn"
                   style="padding: 10px 16px; background-color: var(--primary); color: white; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px;">
                    + Publish
                </a>

                {{-- Avatar Dropdown --}}
                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <button @click="open = !open"
                            style="display: flex; align-items: center; gap: 8px; padding: 6px 12px; background-color: var(--bgTertiary); border: 1px solid var(--border); border-radius: 24px; cursor: pointer; transition: all 0.2s;">
                        @if(auth()->user()->avatar || auth()->user()->avatar_url)
                            <img src="{{ auth()->user()->avatar ?: auth()->user()->avatar_url }}"
                                 alt="{{ auth()->user()->name }}"
                                 style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                        @else
                            <div style="width: 32px; height: 32px; border-radius: 50%; background-color: {{ '#' . substr(md5(auth()->user()->name), 0, 6) }}; display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; font-weight: 600;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </div>
                        @endif
                        <span style="color: var(--textPrimary); font-size: 14px; font-weight: 600; max-width: 100px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            {{ auth()->user()->name }}
                        </span>
                        <i class="ph ph-caret-down" :style="open ? 'transform: rotate(180deg)' : ''" style="font-size: 16px; color: var(--textSecondary); transition: transform 0.2s;"></i>
                    </button>

                    {{-- Dropdown Menu --}}
                    <div x-show="open"
                         style="position: absolute; top: 100%; right: 0; margin-top: 8px; background-color: var(--bgSecondary); border: 1px solid var(--border); border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); min-width: 200px; z-index: 1000; overflow: hidden;"
                         x-transition>
                        <div style="padding: 12px 16px; border-bottom: 1px solid var(--border);">
                            <p style="margin: 0; font-weight: 600; color: var(--textPrimary); font-size: 14px;">
                                {{ auth()->user()->name }}
                            </p>
                            <p style="margin: 4px 0 0 0; font-size: 12px; color: var(--textMuted);">
                                {{ auth()->user()->email }}
                            </p>
                        </div>

                        <a href="/favorites"
                           class="flex items-center gap-3 px-4 py-3 text-sm no-underline transition-colors hover:bg-[var(--bgTertiary)]"
                           style="color: var(--textPrimary);">
                            <i class="ph ph-heart text-xl" style="color: var(--primary);"></i>
                            Favorites
                        </a>

                        <a href="/my-listings"
                           class="flex items-center gap-3 px-4 py-3 text-sm no-underline transition-colors hover:bg-[var(--bgTertiary)]"
                           style="color: var(--textPrimary);">
                            <i class="ph ph-list text-xl" style="color: var(--primary);"></i>
                            My Listings
                        </a>

                        <a href="/chat"
                           class="flex items-center gap-3 px-4 py-3 text-sm no-underline transition-colors hover:bg-[var(--bgTertiary)]"
                           style="color: var(--textPrimary);">
                            <i class="ph ph-chat-circle-text text-xl" style="color: var(--primary);"></i>
                            Messages
                        </a>

                        <a href="/profile"
                           class="flex items-center gap-3 px-4 py-3 text-sm no-underline transition-colors hover:bg-[var(--bgTertiary)]"
                           style="color: var(--textPrimary);">
                            <i class="ph ph-user text-xl" style="color: var(--primary);"></i>
                            Profile
                        </a>

                        <div style="border-top: 1px solid var(--border);">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="flex items-center gap-3 px-4 py-3 w-full text-sm text-left no-underline transition-colors hover:bg-[var(--bgTertiary)]"
                                        style="color: var(--danger); border: none; background: transparent; cursor: pointer;">
                                    <i class="ph ph-sign-out text-xl"></i>
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <a href="/login" class="login_btn"
                   style="padding: 10px 20px; color: var(--textPrimary); text-decoration: none; font-weight: 600; font-size: 14px;">
                    Log In
                </a>
                <a href="/sign_up"
                   style="padding: 10px 20px; background-color: var(--primary); color: white; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px;">
                    Join Us
                </a>
            @endauth
        </div>
    </div>
</header>

{{-- Add Alpine.js for the dropdown --}}
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
