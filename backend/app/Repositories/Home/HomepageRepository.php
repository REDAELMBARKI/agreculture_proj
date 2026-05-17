<?php

namespace App\Repositories\Home;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use App\Models\User;
use App\Models\HeroSlider;
use App\Models\Banner;
use App\Models\Review;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class HomepageRepository implements HomepageRepositoryInterface
{
    public function __construct(
        private Category $category,
        private Product $product,
        private User $user
    ) {}
    public function getStats(): array
    {
        return [
            'total_products' => $this->product->count(),
            'total_users' => $this->user->count(),
            'total_donations' => 0, // No donation mode without listing_mode
        ];
    }

    public function getFeaturedCategories(): Collection
    {
        return $this->category->select(['id', 'name', 'slug', 'icon', 'sort_order'])
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->with(['thumbnail'])
            ->withCount(['superCategoryProducts as products_count'])
            ->orderBy('sort_order')
            ->get();
    }

    public function getPopularProducts(array $filters): Collection
    {
        $query = $this->product->select([
            'id', 'title', 'slug', 'price', 'age_range', 'condition',
            'views_count', 'favorites_count', 'created_at', 'user_id'
        ])
            ->with(['user:id,name', 'categories:id,name,slug', 'address', 'thumbnail', 'gallery'])
            ->whereIn('status', ['published', 'draft', 'reserved', 'sold', 'closed'])
            ->orderBy('views_count', 'desc')
            ->limit(10);

        if (!empty($filters['age'])) {
            $query->where('age_range', $filters['age']);
        }

        if (!empty($filters['category_id'])) {
            $query->where('super_category_id', $filters['category_id']);
        }

        return $query->get();
    }

    public function getNewArrivals(): Collection
    {
        return $this->product->select([
            'id', 'title', 'slug', 'price', 'age_range', 'condition',
            'views_count', 'favorites_count', 'created_at', 'user_id'
        ])
            ->with(['user:id,name', 'categories:id,name,slug', 'address', 'thumbnail', 'gallery'])
            ->whereIn('status', ['published', 'draft', 'reserved', 'sold', 'closed'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    public function getProductsByCategory(int $categoryId, int $limit = 10): Collection
    {
        return $this->product->select([
            'id', 'title', 'slug', 'price', 'age_range', 'condition',
            'views_count', 'favorites_count', 'created_at', 'user_id'
        ])
            ->with(['user:id,name', 'categories:id,name,slug', 'address', 'thumbnail', 'gallery'])
            ->whereIn('status', ['published', 'draft', 'reserved', 'sold', 'closed'])
            ->where('super_category_id', $categoryId)
            ->orderBy('views_count', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getAllProductsByCategory(): array
    {
        $categories = $this->category->whereNull('parent_id')->where('is_active', true)->get();
        $result = [];
        
        foreach ($categories as $category) {
            $products = $this->product->select([
                'id', 'title', 'slug', 'price', 'age_range', 'condition',
                'views_count', 'favorites_count', 'created_at', 'user_id'
            ])
                ->with(['user:id,name', 'categories:id,name,slug', 'address', 'thumbnail', 'gallery'])
                ->whereIn('status', ['published', 'draft', 'reserved', 'sold', 'closed'])
                ->where('super_category_id', $category->id)
                ->orderBy('views_count', 'desc')
                ->limit(10)
                ->get();
                
            $result[$category->id] = $products;
        }
        
        return $result;
    }

    public function getTrendingTags(): Collection
    {
        return Tag::select(['tags.id', 'tags.name', 'tags.slug'])
            ->join('product_tag', 'tags.id', '=', 'product_tag.tag_id')
            ->join('products', 'product_tag.product_id', '=', 'products.id')
            ->groupBy('tags.id', 'tags.name', 'tags.slug')
            ->orderByRaw('COUNT(product_tag.product_id) DESC')
            ->limit(15)
            ->get();
    }

    public function getTopSellers(): Collection
    {
        return User::select('users.id', 'users.name')
            ->selectRaw('COALESCE(AVG(reviews.rating), 0) as avg_rating, COUNT(reviews.id) as total_reviews')
            ->withCount(['products'])
            ->leftJoin('reviews', 'users.id', '=', 'reviews.reviewed_id')
            ->groupBy('users.id', 'users.name')
            ->orderBy('avg_rating', 'desc')
            ->orderBy('total_reviews', 'desc')
            ->limit(6)
            ->get();
    }

    public function getRecentReviews(): Collection
    {
        return Review::with(['reviewer:id,name', 'product:id,title'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    public function getNearbyProducts(string $city): Collection
    {
        return Product::select([
            'id', 'title', 'slug', 'price', 'age_range', 'condition',
            'views_count', 'favorites_count', 'created_at', 'user_id'
        ])
            ->with(['user:id,name', 'addresses' => function ($query) use ($city) {
                $query->select(['addressable_id', 'addressable_type', 'city', 'district'])
                    ->where('addressable_type', Product::class)
                    ->where('city', $city);
            }])
            ->whereHas('addresses', function ($query) use ($city) {
                $query->where('addressable_type', Product::class)->where('city', $city);
            })
            ->whereIn('status', ['published', 'draft', 'reserved', 'sold', 'closed'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    public function getFreeItems(): Collection
    {
        return Product::select([
            'id', 'title', 'slug', 'age_range', 'condition', 'views_count',
            'favorites_count', 'created_at', 'user_id'
        ])
            ->with(['user:id,name', 'categories:id,name,slug'])
            ->where(function ($query) {
                $query->where('price', 0)->orWhereNull('price');
            })
            ->whereIn('status', ['published', 'draft', 'reserved', 'sold', 'closed'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    public function getBoostedListings(): Collection
    {
        return Product::select([
            'id', 'title', 'slug', 'price', 'age_range', 'condition',
            'views_count', 'favorites_count', 'created_at', 'user_id'
        ])
            ->with(['user:id,name', 'categories:id,name,slug', 'thumbnail'])
            ->whereIn('status', ['published', 'draft', 'reserved', 'sold', 'closed'])
            ->orderBy('views_count', 'desc')
            ->limit(10)
            ->get();
    }

    public function getHeroSliders(): Collection
    {
        return HeroSlider::where('is_active', true)
            ->with(['thumbnail'])
            ->orderBy('sort_order')
            ->get();
    }

    public function getBanners(): Collection
    {
        return Banner::where('is_active', true)
            ->with(['thumbnail'])
            ->orderBy('sort_order')
            ->get();
    }
}
