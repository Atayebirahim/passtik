<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile.show');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . auth()->id(),
            'phone' => 'required|string|max:20',
        ]);

        auth()->user()->update($validated);

        return back()->with('alert_success', __('messages.profile_updated'));
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if (!Hash::check($validated['current_password'], auth()->user()->password)) {
            return back()->with('alert_error', __('messages.current_password_incorrect'));
        }

        auth()->user()->update([
            'password' => Hash::make($validated['password'])
        ]);

        return back()->with('alert_success', __('messages.password_updated'));
    }

    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        if (!Hash::check($request->password, auth()->user()->password)) {
            return back()->with('alert_error', __('messages.password_incorrect'));
        }

        $user = auth()->user();
        auth()->logout();
        $user->delete();

        return redirect()->route('login')->with('alert_success', __('messages.account_deleted'));
    }
}
