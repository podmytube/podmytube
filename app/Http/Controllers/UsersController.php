<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UsersController extends Controller
{
    public const NB_ITEMS_PER_PAGE = 100;

    public function index(Request $request)
    {
        Gate::authorize('superadmin');

        $nbItemsPerPage = $request->query('nb') ?? self::NB_ITEMS_PER_PAGE;

        $users = User::orderBy('created_at')
            ->simplePaginate($nbItemsPerPage)
        ;

        return view('users.index', compact('users', 'nbItemsPerPage'));
    }

    public function impersonate(User $user)
    {
        Gate::authorize('superadmin');

        auth()->user()->impersonate($user);

        return redirect()->route('home');
    }

    public function leaveImpersonate()
    {
        auth()->user()->leaveImpersonation();

        return redirect()->route('home');
    }
}
