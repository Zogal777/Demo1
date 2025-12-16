<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequests\PasswordUpdateRequest;
use App\Http\Requests\UserRequests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('Users', [
            'users' => User::all(),
        ]);
    }

    public function edit(User $user)
    {
        return Inertia::render('UserProfile/Edit', [
            'user' => $user,
            'mustVerifyEmail' => $user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    public function update(UserUpdateRequest $request, User $user)
    {
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('users.edit', $user->id);
    }

    public function updatePassword(PasswordUpdateRequest $request, User $user)
    {
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return Redirect::route('users.edit', $user->id)
            ->with('status', 'Password updated.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return Redirect::route('users.index');
    }
}
