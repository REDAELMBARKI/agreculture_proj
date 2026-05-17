<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private function userId(): int
    {
        return (int) auth()->id();
    }

    public function stats()
    {
        $userId = $this->userId();

        $totalSold = Product::query()
            ->where('user_id', $userId)
            ->whereIn('status', ['sold', 'closed'])
            ->count();

        $totalViews = (int) Product::query()
            ->where('user_id', $userId)
            ->sum('views_count');

        $totalClicks = Conversation::query()
            ->where('seller_id', $userId)
            ->count();

        return view('user.dashboard.stats', compact('totalSold', 'totalViews', 'totalClicks'));
    }

    public function activity()
    {
        $userId = $this->userId();
        $start = Carbon::today()->subDays(29);

        $announcementsRaw = Product::query()
            ->where('user_id', $userId)
            ->whereDate('created_at', '>=', $start)
            ->get(['created_at'])
            ->groupBy(fn ($row) => $row->created_at?->format('Y-m-d'))
            ->map(fn ($group) => $group->count())
            ->all();

        $data = [];
        for ($i = 0; $i < 30; $i++) {
            $date = $start->copy()->addDays($i)->format('Y-m-d');
            $data[] = [
                'date' => $date,
                'announcements' => (int) ($announcementsRaw[$date] ?? 0),
            ];
        }

        return view('user.dashboard.activity', compact('data'));
    }

    public function topAnnouncements()
    {
        $userId = $this->userId();

        $contactCounts = Conversation::query()
            ->select('product_id', DB::raw('COUNT(*) as clicks'))
            ->groupBy('product_id')
            ->pluck('clicks', 'product_id');

        $rows = Product::query()
            ->where('user_id', $userId)
            ->with('thumbnail')
            ->orderByDesc('views_count')
            ->limit(3)
            ->get()
            ->map(function ($product) use ($contactCounts) {
                $thumb = $product->thumbnail;
                $imageUrl = $thumb?->url;
                if (! $imageUrl && $thumb?->path) {
                    $imageUrl = asset('storage/'.ltrim(str_replace('public/', '', $thumb->path), '/'));
                }

                return [
                    'id' => (int) $product->id,
                    'title' => (string) $product->title,
                    'image_url' => $imageUrl,
                    'views' => (int) $product->views_count,
                    'clicks' => (int) ($contactCounts[$product->id] ?? 0),
                ];
            })
            ->values()
            ->all();

        return view('user.dashboard.top-announcements', ['data' => $rows]);
    }

    public function categories()
    {
        $userId = $this->userId();

        $products = Product::query()
            ->where('user_id', $userId)
            ->with('superCategory')
            ->get();

        $counts = [
            'Clothes' => 0,
            'Shoes' => 0,
            'Accessories' => 0,
        ];

        foreach ($products as $product) {
            $categoryName = strtolower((string) ($product->superCategory?->name ?? ''));
            if (str_contains($categoryName, 'shoe')) {
                $counts['Shoes']++;
            } elseif (str_contains($categoryName, 'cloth') || str_contains($categoryName, 'wear')) {
                $counts['Clothes']++;
            } else {
                $counts['Accessories']++;
            }
        }

        $data = [
            ['category' => 'Clothes', 'count' => $counts['Clothes']],
            ['category' => 'Shoes', 'count' => $counts['Shoes']],
            ['category' => 'Accessories', 'count' => $counts['Accessories']],
        ];

        return view('user.dashboard.categories', compact('data'));
    }

    public function status()
    {
        $userId = $this->userId();

        $salesProducts = Product::query()
            ->where('user_id', $userId)
            ->get(['status']);

        $salesStatus = [
            'available' => 0,
            'reserved' => 0,
            'sold' => 0,
        ];

        foreach ($salesProducts as $product) {
            if (in_array($product->status, ['reserved'], true)) {
                $salesStatus['reserved']++;
            } elseif (in_array($product->status, ['sold', 'closed'], true)) {
                $salesStatus['sold']++;
            } else {
                $salesStatus['available']++;
            }
        }

        $data = [
            'sales' => $salesStatus,
        ];

        return view('user.dashboard.status', compact('data'));
    }
}
