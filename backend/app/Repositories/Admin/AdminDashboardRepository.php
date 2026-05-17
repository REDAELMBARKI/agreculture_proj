<?php

namespace App\Repositories\Admin;

use App\Models\Product;
use App\Models\ProductItem;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AdminDashboardRepository implements AdminDashboardRepositoryInterface
{
    public function getAllUsers(): Collection
    {
        return User::with('roles')->get();
    }

    public function countAllProducts(): int
    {
        return Product::query()->count();
    }

    public function countAllUsers(): int
    {
        return User::query()->count();
    }

    public function countActiveProducts(): int
    {
        return Product::query()
            ->where('status', 'published')
            ->count();
    }

    public function getRecentUserRegistrationDates(int $limit = 10): array
    {
        return User::query()
            ->orderByDesc('created_at')
            ->take($limit)
            ->pluck('created_at')
            ->map(fn ($date) => $date?->format('Y-m-d'))
            ->filter()
            ->values()
            ->all();
    }

    public function getAllInventoryItems(): Collection
    {
        return ProductItem::with(['product.superCategory', 'product.user'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function countTotalAnnouncements(): int
    {
        return Product::query()->count();
    }

    public function countActiveAnnouncements(): int
    {
        return Product::query()->where('status', 'published')->count();
    }

    public function countPendingModeration(): int
    {
        // For now, assuming draft or a specific moderation status
        return Product::query()->where('status', 'draft')->count();
    }

    public function countNewUsersToday(): int
    {
        return User::query()->whereDate('created_at', Carbon::today())->count();
    }

    public function getUserTrend(): array
    {
        $start = Carbon::today()->subDays(6);
        $counts = User::query()
            ->whereDate('created_at', '>=', $start)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->pluck('count', 'date');

        $data = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $start->copy()->addDays($i)->format('Y-m-d');
            $data[] = $counts[$date] ?? 0;
        }
        return $data;
    }

    public function getAnnouncementFunnelCounts(): array
    {
        return [
            'posted' => Product::query()->count(),
            'active' => Product::query()->where('status', 'published')->count(),
            'contacted' => DB::table('conversations')->distinct('product_id')->count(),
            'closed' => Product::query()->whereIn('status', ['sold', 'closed'])->count(),
        ];
    }

    public function getTopCategories(): array
    {
        return DB::table('products')
            ->join('categories', 'products.super_category_id', '=', 'categories.id')
            ->select('categories.name as category', DB::raw('count(*) as count'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->map(fn($row) => (array)$row)
            ->all();
    }

    public function getUserRetentionStatsForCurrentMonth(): array
    {
        $now = Carbon::now();
        $start = $now->copy()->startOfMonth();
        
        $totalUsers = User::query()->where('created_at', '<', $start)->count();
        if ($totalUsers === 0) return ['returning' => 0, 'new' => 100];

        $returningUsers = User::query()
            ->where('created_at', '<', $start)
            ->whereHas('products', function($q) use ($start) {
                $q->where('created_at', '>=', $start);
            })->count();

        $returningPercent = round(($returningUsers / $totalUsers) * 100);
        
        return [
            'returning' => $returningPercent,
            'new' => 100 - $returningPercent,
        ];
    }

    public function getPendingModerationAnnouncements(int $limit = 5): Collection
    {
        return Product::query()
            ->where('status', 'draft')
            ->with(['user', 'superCategory', 'thumbnail'])
            ->orderByDesc('created_at')
            ->take($limit)
            ->get();
    }
}
