<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\PointLog;
use App\Models\UserPoints;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()->where('role', '!=', 'admin');
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        $users = $query->with('points')->latest()->paginate(20);
        return view('admin.members.index', compact('users'));
    }

    public function points(int $userId): View
    {
        $user = User::with('points')->findOrFail($userId);
        $logs = PointLog::where('user_id', $userId)->latest()->paginate(30);
        $balance = UserPoints::getBalance($userId);
        return view('admin.members.points', compact('user', 'logs', 'balance'));
    }

    public function addPoints(Request $request, int $userId)
    {
        $request->validate(['points' => 'required|integer|min:1', 'description' => 'required|string|max:255']);
        UserPoints::addPoints($userId, $request->points, 'manual', $request->description);
        return redirect()->back()->with('success', $request->points . ' points added.');
    }

    public function deductPoints(Request $request, int $userId)
    {
        $request->validate(['points' => 'required|integer|min:1', 'description' => 'required|string|max:255']);
        UserPoints::deductPoints($userId, $request->points, 'manual', $request->description);
        return redirect()->back()->with('success', $request->points . ' points deducted.');
    }
}