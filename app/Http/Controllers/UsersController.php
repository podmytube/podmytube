<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;

class UsersController extends Controller
{
    public const NB_ITEMS_PER_PAGE = 100;

    public function index()
    {
        $this->authorize('superadmin');

        return view('users.index');
    }

    public function impersonate(User $user)
    {
        $this->authorize('superadmin');

        auth()->user()->impersonate($user);

        return redirect()->route('home');
    }

    public function leaveImpersonate()
    {
        auth()->user()->leaveImpersonation();

        return redirect()->route('users.index');
    }
}
