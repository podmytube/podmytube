<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Jobs\RemoveAccountJob;
use App\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return view('user.edit', compact('user'));
    }

    public function update(UserRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $validatedParams = $request->validated();

        if (!array_key_exists('newsletter', $validatedParams)) {
            $validatedParams['newsletter'] = false;
        }

        $user->update($validatedParams);

        return redirect(route('home'))->with('success', 'Your account is up to date.');
    }

    public function destroy()
    {
        $user = Auth::user();

        RemoveAccountJob::dispatch($user);

        Auth::logout();

        return redirect(route('www.index'))
            ->with(
                'success',
                'Your account is planned for deletion.'
            )
        ;
    }
}
