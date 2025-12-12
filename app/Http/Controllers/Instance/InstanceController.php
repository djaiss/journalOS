<?php

declare(strict_types=1);

namespace App\Http\Controllers\Instance;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class InstanceController extends Controller
{
    public function index(): View
    {
        $totalUsers = User::query()->count();
        $last30DaysUsers = User::query()
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
        $last7DaysUsers = User::query()
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        $users = User::query()
            ->withCount('journals')
            ->latest()
            ->get()
            ->map(fn(User $user): array => [
                'id' => $user->id,
                'name' => $user->getFullName(),
                'email' => $user->email,
                'last_activity_at' => $user->last_activity_at?->format('Y-m-d H:i:s'),
                'journals_count' => $user->journals_count,
                'avatar' => $user->initials(),
                'url' => route('instance.show', $user->id),
            ]);

        return view('instance.index', [
            'users' => $users,
            'totalUsers' => $totalUsers,
            'last30DaysUsers' => $last30DaysUsers,
            'last7DaysUsers' => $last7DaysUsers,
        ]);
    }

    public function show(Request $request, User $user): View
    {
        $logs = $user->logs()->with('user')->latest()->take(10)->get();

        return view('instance.show', [
            'user' => $user,
            'logs' => $logs,
        ]);
    }
}
