<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\Address;
use App\Models\Category;

class ReportController extends Controller
{
    public function all()
    {
        return view('admin.reports');
    }

    public function sales()
    {
        $rows = Product::query()
            ->whereIn('status', ['sold', 'closed'])
            ->with(['user', 'category'])
            ->latest()
            ->get();
            
        return view('admin.reports.sales', ['data' => $rows]);
    }

    public function users()
    {
        $rows = User::query()
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($user) => [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'registered_at' => $user->created_at?->toDateString(),
            ])
            ->values()
            ->all();

        return view('admin.reports.users', ['data' => $rows]);
    }

    public function topUsers()
    {
        $rows = User::query()
            ->leftJoin('products', 'users.id', '=', 'products.user_id')
            ->select(
                'users.id',
                'users.name',
                DB::raw("COUNT(products.id) as total_listings")
            )
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_listings')
            ->limit(20)
            ->get()
            ->map(fn ($row) => [
                'user_id' => (int) $row->id,
                'name' => (string) $row->name,
                'total_listings' => (int) $row->total_listings,
            ])
            ->values()
            ->all();

        return view('admin.reports.top-users', ['data' => $rows]);
    }

    public function userActivity()
    {
        $rows = User::query()
            ->leftJoin('products', 'users.id', '=', 'products.user_id')
            ->select(
                'users.id',
                'users.name',
                DB::raw("COUNT(products.id) as total_posts"),
                DB::raw("COALESCE(SUM(products.views_count), 0) as total_views"),
                DB::raw("MAX(products.created_at) as last_posted_at")
            )
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_posts')
            ->get()
            ->map(fn ($row) => [
                'user_id' => (int) $row->id,
                'name' => (string) $row->name,
                'total_posts' => (int) $row->total_posts,
                'total_views' => (int) $row->total_views,
                'last_posted_at' => $row->last_posted_at,
            ])
            ->values()
            ->all();

        return view('admin.reports.user-activity', ['data' => $rows]);
    }

    public function usersByCity()
    {
        $userAddressType = (new User())->getMorphClass();

        $rows = User::query()
            ->leftJoin('addresses', function ($join) use ($userAddressType) {
                $join->on('users.id', '=', 'addresses.addressable_id')
                    ->where('addresses.addressable_type', '=', $userAddressType);
            })
            ->leftJoin('cities', 'addresses.city_id', '=', 'cities.id')
            ->select(
                DB::raw("COALESCE(cities.name, 'Unknown') as city"),
                DB::raw('COUNT(users.id) as users_count')
            )
            ->groupBy(DB::raw("COALESCE(cities.name, 'Unknown')"))
            ->orderByDesc('users_count')
            ->get()
            ->map(fn ($row) => [
                'city' => (string) $row->city,
                'users_count' => (int) $row->users_count,
            ])
            ->values()
            ->all();

        return view('admin.reports.users-by-city', ['data' => $rows]);
    }

    public function sales()
    {
        $rows = Product::query()
            ->get(['created_at'])
            ->groupBy(fn ($product) => $product->created_at?->format('Y-m-d'))
            ->map(fn ($group, $date) => [
                'date' => $date,
                'sales_count' => $group->count(),
            ])
            ->sortBy('date')
            ->values()
            ->all();

        return view('admin.reports.sales', ['data' => $rows]);
    }

    public function listingsPerformance()
    {
        $contactCounts = Conversation::query()
            ->select('product_id', DB::raw('COUNT(*) as contacts'))
            ->groupBy('product_id')
            ->pluck('contacts', 'product_id');

        $rows = Product::query()
            ->orderByDesc('views_count')
            ->limit(100)
            ->get(['id', 'title', 'views_count'])
            ->map(fn ($product) => [
                'product' => (string) $product->title,
                'views' => (int) $product->views_count,
                'contacts' => (int) ($contactCounts[$product->id] ?? 0),
            ])
            ->values()
            ->all();

        return view('admin.reports.listings-performance', ['data' => $rows]);
    }

    public function inventoryByCategory()
    {
        $rows = Category::query()
            ->leftJoin('products', 'categories.id', '=', 'products.super_category_id')
            ->select(
                'categories.name as category',
                DB::raw('COUNT(products.id) as products_count')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('products_count')
            ->get()
            ->map(fn ($row) => [
                'category' => (string) $row->category,
                'products_count' => (int) $row->products_count,
            ])
            ->values()
            ->all();

        return view('admin.reports.inventory-by-category', ['data' => $rows]);
    }

    public function timeBased()
    {
        $products = Product::query()->get(['created_at']);

        $hourly = $products
            ->groupBy(fn ($product) => (int) $product->created_at?->format('H'))
            ->map(fn ($group, $hour) => [
                'hour' => (int) $hour,
                'posts_count' => $group->count(),
            ])
            ->sortBy('hour')
            ->values()
            ->all();

        $daily = $products
            ->groupBy(fn ($product) => $product->created_at?->format('l'))
            ->map(fn ($group, $day) => [
                'day' => (string) $day,
                'posts_count' => $group->count(),
            ])
            ->sortByDesc('posts_count')
            ->values()
            ->all();

        $bestHour = collect($hourly)->sortByDesc('posts_count')->first();
        $bestDay = collect($daily)->first();

        return view('admin.reports.time-based', [
            'summary' => [
                'best_hour' => $bestHour['hour'] ?? null,
                'best_hour_posts' => $bestHour['posts_count'] ?? 0,
                'best_day' => $bestDay['day'] ?? null,
                'best_day_posts' => $bestDay['posts_count'] ?? 0,
            ],
            'hourly' => $hourly,
            'daily' => $daily,
        ]);
    }

    public function all()
    {
        return view('admin.reports.all');
    }
}
