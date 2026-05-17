<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $announcements = Product::where('user_id', $user->id)->latest()->get();
        
        // Mocking or fetching impact stats
        $stats = [
            'total_donated' => Product::where('user_id', $user->id)->where('listing_mode', 'donate')->where('status', 'completed')->count(),
            'total_sold' => Product::where('user_id', $user->id)->where('listing_mode', 'sell')->where('status', 'sold')->count(),
            'total_views' => 450, // Placeholder
            'total_clicks' => 92,  // Placeholder
        ];

        $activity = [
            ['date' => Carbon::now()->subDays(21)->format('Y-m-d'), 'donations' => 2, 'announcements' => 1],
            ['date' => Carbon::now()->subDays(14)->format('Y-m-d'), 'donations' => 1, 'announcements' => 3],
            ['date' => Carbon::now()->subDays(7)->format('Y-m-d'), 'donations' => 3, 'announcements' => 2],
            ['date' => Carbon::now()->format('Y-m-d'), 'donations' => 2, 'announcements' => 4],
        ];

        $topAnnouncements = Product::where('user_id', $user->id)
            ->with(['thumbnail'])
            ->latest()
            ->take(3)
            ->get()
            ->map(function($p) {
                return [
                    'id' => $p->id,
                    'title' => $p->title,
                    'image_url' => $p->thumbnail?->url,
                    'views' => rand(50, 200),
                    'clicks' => rand(5, 30)
                ];
            });

        $categories = DB::table('products')
            ->join('categories', 'products.super_category_id', '=', 'categories.id')
            ->where('products.user_id', $user->id)
            ->select('categories.label as category', DB::raw('count(*) as count'))
            ->groupBy('categories.label')
            ->get();

        $statusData = [
            'donations' => [
                'pending' => Product::where('user_id', $user->id)->where('listing_mode', 'donate')->where('status', 'pending')->count(),
                'scheduled' => Product::where('user_id', $user->id)->where('listing_mode', 'donate')->where('status', 'scheduled')->count(),
                'completed' => Product::where('user_id', $user->id)->where('listing_mode', 'donate')->where('status', 'completed')->count(),
            ],
            'sales' => [
                'available' => Product::where('user_id', $user->id)->where('listing_mode', 'sell')->where('status', 'active')->count(),
                'reserved' => Product::where('user_id', $user->id)->where('listing_mode', 'sell')->where('status', 'reserved')->count(),
                'sold' => Product::where('user_id', $user->id)->where('listing_mode', 'sell')->where('status', 'sold')->count(),
            ],
        ];

        $foundations_count = DB::table('users')
            ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
            ->where('user_roles.role_id', 3) // Assuming 3 is Charity/Foundation
            ->count();
        
        return view('user.dashboard', compact('user', 'announcements', 'stats', 'activity', 'topAnnouncements', 'categories', 'statusData', 'foundations_count'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'avatar' => 'nullable|image|max:4096',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->hasFile('avatar')) {
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $user->avatar_path = $request->file('avatar')->store('avatars', 'public');
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully!');
    }

    public function listings()
    {
        $user = Auth::user();
        $listings = Product::where('user_id', $user->id)->latest()->paginate(10);
        return view('user.listings', compact('user', 'listings'));
    }
}
