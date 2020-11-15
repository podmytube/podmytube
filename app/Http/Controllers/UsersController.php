<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\User;

class UsersController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);
        return view('user.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $this->authorize('edit', $user);
        return view('user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $validatedParams = $request->validated();
        if (!array_key_exists('newsletter', $validatedParams)) {
            $validatedParams['newsletter'] = false;
        }

        $user->update($validatedParams);

        return redirect(route('user.show', $user))->with(
            'success',
            'User has been successfully updated.'
        );
    }
}
