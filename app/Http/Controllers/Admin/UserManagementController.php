<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function index()
    {
        $users = User::where('role', 'user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function approve(User $user)
    {
        $user->update(['status' => 'approved']);

        return back()->with('success', 'User approved successfully.');
    }

    public function reject(User $user)
    {
        $user->update(['status' => 'rejected']);

        return back()->with('success', 'User rejected.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }

    public function forceLogout(User $user)
    {
        $user->deactivateAllSessions();
        $user->device_fingerprint = null;
        $user->save();

        return back()->with('success', 'User logged out from all devices.');
    }
}
