@extends('layouts.main')

@section('title', 'Our Partners - LetUsDonate')

@section('content')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/our_partners.css') }}">
@endpush
<section class="max-w-7xl mx-auto py-16 px-4">
    <div class="text-center mb-16">
        <h1 class="text-4xl font-bold text-[var(--textPrimary)] mb-4">Charities We’re Proud to Work With</h1>
        <p class="text-xl text-[var(--textSecondary)] max-w-2xl mx-auto leading-relaxed">
            We collaborate with compassionate organizations across the UK to make
            clothing donations more impactful and sustainable.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Partner 1 -->
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-[var(--border)] hover:border-[var(--primary)] transition-all">
            <h3 class="text-2xl font-bold text-[var(--textPrimary)] mb-4">WearAgain Foundation England</h3>
            <p class="text-[var(--textSecondary)] mb-6 leading-relaxed">
                Helps low-income families by providing gently used clothes for work,
                school, and daily life. Working with thousands of families across
                England, WearAgain ensures children have proper school uniforms,
                parents can dress for job interviews, and households can meet basic
                clothing needs with pride and dignity.
            </p>

            <h4 class="font-bold text-[var(--textPrimary)] mb-4 flex items-center gap-2">
                <i class="ph ph-heart text-[var(--primary)]"></i>
                How They Help:
            </h4>
            <ul class="space-y-2 text-[var(--textSecondary)] text-sm">
                <li class="flex gap-2"><span>•</span> Delivers quality clothing across England.</li>
                <li class="flex gap-2"><span>•</span> Runs community clothing drives.</li>
                <li class="flex gap-2"><span>•</span> Empowers job seekers with workwear and interview outfits.</li>
                <li class="flex gap-2"><span>•</span> Builds stronger communities through volunteering.</li>
                <li class="flex gap-2"><span>•</span> Reaches thousands of families every year.</li>
            </ul>
        </div>

        <!-- Partner 2 -->
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-[var(--border)] hover:border-[var(--primary)] transition-all">
            <h3 class="text-2xl font-bold text-[var(--textPrimary)] mb-4">Threads of Hope UK</h3>
            <p class="text-[var(--textSecondary)] mb-6 leading-relaxed">
                Supports refugees and homeless individuals with essential clothing
                and footwear. Distributes coats, shoes, and everyday wear across the
                UK to help people stay warm and feel confident.
            </p>

            <h4 class="font-bold text-[var(--textPrimary)] mb-4 flex items-center gap-2">
                <i class="ph ph-heart text-[var(--primary)]"></i>
                How They Help:
            </h4>
            <ul class="space-y-2 text-[var(--textSecondary)] text-sm">
                <li class="flex gap-2"><span>•</span> Runs seasonal drives to meet summer and winter needs.</li>
                <li class="flex gap-2"><span>•</span> Helps refugees feel welcomed and included.</li>
                <li class="flex gap-2"><span>•</span> Engages communities through fundraising and awareness events.</li>
            </ul>
        </div>

        <!-- Partner 3 -->
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-[var(--border)] hover:border-[var(--primary)] transition-all">
            <h3 class="text-2xl font-bold text-[var(--textPrimary)] mb-4">SecondChance Wardrobe</h3>
            <p class="text-[var(--textSecondary)] mb-6 leading-relaxed">
                Collects and redistributes quality fashion items to women’s shelters
                and youth hostels — empowering vulnerable women and at-risk youth to
                rebuild their lives with dignity and style.
            </p>

            <h4 class="font-bold text-[var(--textPrimary)] mb-4 flex items-center gap-2">
                <i class="ph ph-heart text-[var(--primary)]"></i>
                How They Help:
            </h4>
            <ul class="space-y-2 text-[var(--textSecondary)] text-sm">
                <li class="flex gap-2"><span>•</span> Turns pre-loved fashion into new opportunities.</li>
                <li class="flex gap-2"><span>•</span> Supports job interviews and education access.</li>
                <li class="flex gap-2"><span>•</span> Promotes sustainable, circular fashion.</li>
                <li class="flex gap-2"><span>•</span> Encourages reuse and community compassion.</li>
            </ul>
        </div>

        <!-- Partner 4 -->
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-[var(--border)] hover:border-[var(--primary)] transition-all">
            <h3 class="text-2xl font-bold text-[var(--textPrimary)] mb-4">GreenStitch Collective</h3>
            <p class="text-[var(--textSecondary)] mb-6 leading-relaxed">
                Focuses on textile recycling and sustainable fashion initiatives,
                inspiring communities to embrace ethical fashion choices.
            </p>

            <h4 class="font-bold text-[var(--textPrimary)] mb-4 flex items-center gap-2">
                <i class="ph ph-heart text-[var(--primary)]"></i>
                Initiatives Include:
            </h4>
            <ul class="space-y-2 text-[var(--textSecondary)] text-sm">
                <li class="flex gap-2"><span>•</span> Community textile drop-off points.</li>
                <li class="flex gap-2"><span>•</span> Collaborations with ethical brands.</li>
                <li class="flex gap-2"><span>•</span> Workshops for repair and upcycling.</li>
                <li class="flex gap-2"><span>•</span> Events selling pre-loved clothes and fabrics.</li>
            </ul>
        </div>
    </div>
</section>
@endsection
