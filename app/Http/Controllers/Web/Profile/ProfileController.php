<?php

namespace App\Http\Controllers\Web\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\Web\Profile\ChangePasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        return view('profile.show', ['user' => auth()->user()]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        if ($request->hasFile('user_image')) {
            if ($user->user_image && Storage::disk('public')->exists($user->user_image)) {
                Storage::disk('public')->delete($user->user_image);
            }
            $data['user_image'] = $request->file('user_image')->store('members', 'public');
        }

        $user->update($data);

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully.');
    }

    public function changePassword(ChangePasswordRequest $request): RedirectResponse
    {
        if (!Hash::check($request->current_password, $request->user()->password)) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.show')->with('success', 'Password changed successfully.');
    }
}
