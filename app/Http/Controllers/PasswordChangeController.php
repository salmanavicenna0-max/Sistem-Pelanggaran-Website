<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PasswordChangeController extends Controller
{
    public function edit(): View
    {
        return view('auth.change-password');
    }

    public function update(UpdatePasswordRequest $request): RedirectResponse
    {
        $request->user()->update([
            'password' => $request->string('password')->value(),
            'is_first_login' => false,
        ]);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('status', 'Password berhasil diubah.');
    }
}
