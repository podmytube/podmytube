<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class RefereeController extends Controller
{
    public function __invoke()
    {
        $user = Auth::user();

        $this->authorize('view', $user);

        $referees = collect();

        return view('referee.index', compact('user', 'referees'));
    }
}
