<?php

namespace App\Http\Controllers;

use App\Services\Admin\AdminDashboardServiceInterface;
use Illuminate\Http\Request;

// controller for managing users, reports, and dashboard stats
class AdminController extends Controller
{
    public function __construct(
        private readonly AdminDashboardServiceInterface $adminDashboardService
    ) {}

    public function index()
    {
        $stats = $this->adminDashboardService->getDashboardStats();
        $funnel = $this->adminDashboardService->getAnnouncementFunnel();
        $topCategories = $this->adminDashboardService->getTopCategories();
        $userRetention = $this->adminDashboardService->getUserRetention();
        $pendingModeration = $this->adminDashboardService->getPendingModerationAnnouncements(5);

        return view('admin.dashboard', compact('stats', 'funnel', 'topCategories', 'userRetention', 'pendingModeration'));
    }

    public function reports()
    {
        return view('admin.reports');
    }

    public function users()
    {
        $users = $this->adminDashboardService->getAllUsers();
        return view('admin.users', compact('users'));
    }

    public function announcements(Request $request)
    {
        $filters = $request->all();
        $products = \App\Models\Product::with(['user', 'category', 'thumbnail'])
            ->when($request->search, function($q, $search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%$search%"));
            })
            ->when($request->mode, fn($q, $mode) => $q->where('listing_mode', $mode))
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(15);

        return view('admin.announcements', compact('products'));
    }

    // get all users 
    public function getAllUsers()
    {
        $users = $this->adminDashboardService->getAllUsers();
        return view('admin.users', compact('users'));
    }

    // metrics for all the charts
    public function getDashboardStats()
    {
        $stats = $this->adminDashboardService->getDashboardStats();
        return view('admin.dashboard-stats', compact('stats'));
    }

    public function getAnnouncementFunnel()
    {
        $funnel = $this->adminDashboardService->getAnnouncementFunnel();
        return view('admin.charts.funnel', compact('funnel'));
    }

    public function getTopCategories()
    {
        $topCategories = $this->adminDashboardService->getTopCategories();
        return view('admin.charts.top-categories', compact('topCategories'));
    }

    public function getUserRetention()
    {
        $userRetention = $this->adminDashboardService->getUserRetention();
        return view('admin.charts.user-retention', compact('userRetention'));
    }

    public function getHourlyActivity()
    {
        $hourlyActivity = $this->adminDashboardService->getHourlyActivity();
        return view('admin.charts.hourly-activity', compact('hourlyActivity'));
    }

    public function getPendingModeration(Request $request)
    {
        $limit = max(1, min(20, (int) $request->query('limit', 5)));
        $items = $this->adminDashboardService->getPendingModerationAnnouncements($limit);
        $totalPending = $this->adminDashboardService->getDashboardStats()['pending_moderation'] ?? count($items);

        return view('admin.pending-moderation', compact('items', 'totalPending'));
    }
}
