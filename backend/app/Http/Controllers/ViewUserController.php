<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class ViewUserController extends Controller
{
    // this gets all users with their roles
    public function getViewUsers()
    {
        $users = User::with('roles')->get();
        return view('admin.users', compact('users'));
    }

    // gets all roles
    public function getRoles()
    {
        $roles = Role::all();
        return view('admin.roles.index', compact('roles'));
    }

    // updates user
    public function updateUser(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) return back()->with('error', 'User not found');

        $user->update($request->only(['name', 'email']));
        
        if ($request->has('role_id')) {
            $user->roles()->sync([$request->role_id]);
        } elseif ($request->has('role_name')) {
            $role = Role::where('name', $request->role_name)->first();
            if ($role) {
                $user->roles()->sync([$role->id]);
            }
        }

        return back()->with('success', 'User updated successfully');
    }

    // deletes user
    public function deleteUser($id)
    {
        $user = User::find($id);
        if (!$user) return back()->with('error', 'User not found');
        
        $user->delete();
        return back()->with('success', 'User deleted successfully');
    }
}
