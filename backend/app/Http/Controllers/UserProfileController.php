<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserProfileController extends Controller
{
    public function show(Request $request, int $id)
    {
        if ((int) $id !== (int) Auth::id()) {
            abort(403);
        }

        $user = User::find($id);

        if (! $user) {
            abort(404);
        }

        return view('user.profile', compact('user'));
    }

    public function uploadAvatar(Request $request)
    {
        $user = Auth::user();
        if (! $user instanceof User) {
            return redirect()->route('login');
        }

        $request->validate([
            'avatar' => ['required', 'image', 'max:4096'],
        ]);

        $path = $request->file('avatar')->store('avatars/'.$user->id, 'public');

        if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $user->avatar_path = $path;
        $user->save();

        return back()->with('success', 'Avatar updated successfully');
    }

    public function update(Request $request, int $id)
    {
        if ((int) $id !== (int) Auth::id()) {
            abort(403);
        }

        $user = User::find($id);

        if (! $user) {
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:6|confirmed',
            ]);
            $user->password = Hash::make($request->password);
        }

        $user->name = $request->name;
        $user->email = $request->email;

        $user->save();

        return back()->with('success', 'Profile updated successfully');
    }
}
