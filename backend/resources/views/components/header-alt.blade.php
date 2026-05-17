@props(['size' => 'large'])

<header class="header {{ $size === 'small' ? 'header-small' : 'header-large' }}" x-data="{ scrolled: false }" @scroll.window="scrolled = window.scrollY > 10" :class="{ 'scrolled': scrolled }">
    <div class="navbar" style="padding: 20px 4%; max-width: 1440px; margin: 0 auto;">
        <div class="logo">
            <h1 style="display: flex; align-items: center; font-size: 28px; font-weight: 800; margin: 0;">
                <a href="/" style="color: var(--textPrimary); text-decoration: none;">LetUsDonate.uk</a> 
                <i class="ph-bold ph-leaf" style="margin-left: 8px; color: var(--primary); font-size: 28px;"></i>
            </h1>
            <div class="header_content" style="margin-top: 12px;">
                <h2 style="font-size: 20px; font-weight: 700; color: var(--textPrimary); margin: 0;">Sell or donate — connect by phone</h2>
                <h3 style="font-size: 16px; font-weight: 500; color: var(--textSecondary); margin-top: 8px; max-width: 600px; line-height: 1.5;">
                    LetUsDonateUK helps you list anything you want to sell or give away. Interested people call you
                    directly so you can agree pickup, price, or handover in minutes.
                </h3>
            </div>
        </div>
    </div>
</header>

<style>
    .header-alt {
        transition: all 0.3s ease;
    }
    .header-alt.scrolled {
        background-color: var(--bgSecondary);
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
</style>
