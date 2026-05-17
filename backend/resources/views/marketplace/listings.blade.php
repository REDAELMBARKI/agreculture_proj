<div class="grid gap-6 grid-cols-[repeat(auto-fill,minmax(280px,1fr))]">
    @foreach($listings as $product)
        <x-marketplace-card :product="$product" view="grid" />
    @endforeach
</div>
<div class="mt-10">
    {{ $listings->appends(request()->query())->links() }}
</div>
