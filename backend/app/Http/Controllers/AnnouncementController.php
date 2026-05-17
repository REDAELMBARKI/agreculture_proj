<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Models\Review;
use App\Models\Offer;
use App\Models\Media;
use App\Services\AnnouncementService;
use App\Services\ProductService;
use App\Services\ReviewService;
use App\Services\OfferService;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\ReviewRequest;
use App\Http\Requests\OfferRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Unified controller for managing marketplace announcements
class AnnouncementController extends Controller
{
     function __construct(
        protected AnnouncementService $announcementService,
        protected ProductService $productService,
        protected ReviewService $reviewService,
        protected OfferService $offerService
    ) {}

    /**
     * Toggle favorite status for an announcement.
     */
     function toggleFavorite(Request $request, Product $announcement)
    {
        try {
            $userId = Auth::id() ?? 1;
            $res = $this->productService->toggleFavorite($userId, $announcement->id);

            return back()->with('success', $res['message'] ?? 'Favorite status updated');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function index(Request $request)
    {
        $initData = $this->announcementService->getMarketplaceInitData();
        $filters = $request->all();
        
        $listings = $this->announcementService->getMarketplaceListings(
            $filters, 
            $request->input('per_page', 12)
        );

        return view('marketplace', [
            'initData' => $initData,
            'listings' => $listings,
            'filters' => $filters
        ]);
    }

    public function show(Request $request, Product $announcement)
    {
        $announcement->load(['user', 'category', 'thumbnail', 'gallery', 'superCategory', 'subCategories', 'items', 'address']);
        $reviews = $announcement->reviews()->with('reviewer')->latest()->get();

        return view('products.show', [
            'product' => $announcement,
            'reviews' => $reviews
        ]);
    }

    /**
     * Fetch initialization data for the marketplace filters.
     */
    function getMarketplaceInitData()
    {
        $data = $this->announcementService->getMarketplaceInitData();
        return view('marketplace.init', $data);
    }

    /**
     * Fetch paginated listings with filtering.
     */
     function getMarketplaceListings(Request $request)
    {
        $filters = $request->all();
        
        $listings = $this->announcementService->getMarketplaceListings(
            $filters, 
            $request->input('per_page', 12)
        );

        return view('marketplace.listings', [
            'listings' => $listings,
            'filters' => $filters
        ]);
    }

    /**
     * Show the form for creating a new announcement.
     */
    public function create()
    {
        $initData = $this->announcementService->getMarketplaceInitData();
        return view('user.add-announcement', $initData);
    }

    /**
     * Show the form for editing the specified announcement.
     */
    public function edit(Product $announcement)
    {
        if ($announcement->user_id !== Auth::id()) {
            abort(403);
        }
        $initData = $this->announcementService->getMarketplaceInitData();
        $announcement->load(['subCategories', 'thumbnail', 'gallery', 'address']);
        return view('user.edit-announcement', array_merge($initData, ['announcement' => $announcement]));
    }

    /**
     * Store a new announcement.
     */
     function store(ProductRequest $request)
    {
        try {
            $data = $request->validated();
            $data['user_id'] = Auth::id();

            // Handle file uploads if present
            if ($request->hasFile('images')) {
                $mediaIds = $data['media_ids'] ?? [];
                foreach ($request->file('images') as $image) {
                    $path = $image->store('products', 'public');
                    $media = Media::create([
                        'disk' => 'public',
                        'path' => $path,
                        'url' => asset('storage/' . $path),
                        'file_name' => $image->getClientOriginalName(),
                        'mime_type' => $image->getMimeType(),
                        'size' => $image->getSize(),
                        'collection' => 'gallery',
                        'is_temporary' => false,
                    ]);
                    $mediaIds[] = $media->id;
                }
                $data['media_ids'] = $mediaIds;
            }

            $product = $this->announcementService->createAnnouncement($data);

            return redirect()->route('user.listings')->with('success', 'Announcement created successfully!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error creating announcement: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified announcement by slug.
     */
    public function showBySlug(Product $announcement)
    {
        try {
            $announcement->load(['user', 'thumbnail', 'gallery', 'superCategory', 'subCategories', 'items', 'address']);
            $reviews = $announcement->reviews()->with('reviewer')->latest()->get();
            
            return view('products.show', [
                'product' => $announcement,
                'reviews' => $reviews
            ]);
        } catch (\Exception $e) {
            abort(404, 'Announcement not found');
        }
    }

    /**
     * Update the specified announcement.
     */
    function update(ProductRequest $request, User $user, Product $announcement)
    {
        try {
            if ($announcement->user_id !== Auth::id()) {
                return back()->with('error', 'Unauthorized: Announcement does not belong to you');
            }

            $data = $request->validated();
            $product = $this->announcementService->updateAnnouncement($announcement->id, $data);

            return redirect()->route('user.listings')->with('success', 'Announcement updated successfully');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to update announcement: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified announcement.
     */
     function destroy(User $user, Product $announcement)
    {
        try {
            if ($announcement->user_id !== Auth::id()) {
                return back()->with('error', 'Unauthorized: Announcement does not belong to you');
            }

            $this->productService->deleteAnnouncement($announcement->id);

            return back()->with('success', 'Announcement deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete announcement: ' . $e->getMessage());
        }
    }

    /**
     * Update the status of an announcement.
     */
    public function updateStatus(Request $request, Product $announcement)
    {
        try {
            $request->validate([
                'status' => 'required|string|in:reserved,sold,closed,published,draft'
            ]);

            $announcement->update(['status' => $request->status]);

            return back()->with('success', 'Status updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }

    /**
     * Fetch announcements for a specific user.
     */
    public function getUserAnnouncements(Request $request, User $user)
    {
        $products = Product::with(['superCategory', 'thumbnail', 'user'])
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();
            
        return view('user.listings', [
            'listings' => $products,
            'user' => $user
        ]);
    }

    /**
     * Fetch all active announcements.
     */
     function getAllAnnouncements()
    {
        $products = Product::with(['superCategory', 'thumbnail', 'user'])
            ->where('status', 'published')
            ->orderByDesc('created_at')
            ->get();

        return view('marketplace', [
            'listings' => $products
        ]);
    }

    /**
     * Fetch all announcements for admin.
     */
     function getAllAnnouncementsForAdmin()
    {
        $products = Product::with(['superCategory', 'subCategories', 'thumbnail', 'gallery', 'user', 'items'])
            ->orderByDesc('created_at')
            ->get();

        return view('admin.announcements', [
            'products' => $products
        ]);
    }

    /**
     * Fetch reviews for an announcement.
     */
    public function getReviews(Product $announcement)
    {
        $reviews = $announcement->reviews()->with('user')->orderByDesc('created_at')->get();
        return view('products.reviews', [
            'reviews' => $reviews,
            'product' => $announcement
        ]);
    }

    /**
     * Store a review for an announcement.
     */
    public function storeReview(ReviewRequest $request, Product $announcement)
    {
        try {
            $data = $request->validated();
            $data['user_id'] = Auth::id();
            $review = $announcement->reviews()->create($data);

            return back()->with('success', 'Review added successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Fetch offers for an announcement.
     */
    public function getOffers(Product $announcement)
    {
        $offers = $announcement->offers()->with('user')->orderByDesc('created_at')->get();
        return view('products.offers', [
            'offers' => $offers,
            'product' => $announcement
        ]);
    }

    /**
     * Make an offer for an announcement.
     */
    public function makeOffer(OfferRequest $request, Product $announcement)
    {
        try {
            $data = $request->validated();
            $data['user_id'] = Auth::id();
            $offer = $announcement->offers()->create($data);

            return back()->with('success', 'Offer made successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
