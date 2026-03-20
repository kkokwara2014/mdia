<?php

namespace App\Http\Controllers\Web\Leader;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Leader\StoreLeaderRequest;
use App\Http\Requests\Web\Leader\UpdateLeaderRequest;
use App\Models\Leader;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LeaderController extends Controller
{
    public function index(): View
    {
        $leaders = Leader::query()
            ->with('user')
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        return view('leaders.index', compact('leaders'));
    }

    public function create(): View
    {
        return view('leaders.create');
    }

    public function store(StoreLeaderRequest $request): RedirectResponse
    {
        $user = User::where('uuid', $request->user_uuid)->firstOrFail();

        if (Leader::where('user_id', $user->id)->exists()) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'This member is already a leader.');
        }

        if ($request->hasFile('user_image')) {
            $file = $request->file('user_image');
            $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $newPath = $file->storeAs('members', $filename, 'public');

            if ($user->user_image && Storage::disk('public')->exists($user->user_image)) {
                Storage::disk('public')->delete($user->user_image);
            }

            $user->update(['user_image' => $newPath]);
            $user->refresh();
        }

        $image = $user->user_image ?? null;
        $order = $request->input('order');
        if ($order === null || $order === '') {
            $order = 0;
        }

        Leader::create([
            'user_id' => $user->id,
            'position' => $request->position,
            'order' => (int) $order,
            'image' => $image,
            'social_links' => $request->input('social_links', []),
        ]);

        return redirect()->route('leaders.index')
            ->with('success', 'Leader added successfully.');
    }

    public function edit(Leader $leader): View
    {
        $leader->load('user');

        return view('leaders.edit', compact('leader'));
    }

    public function update(UpdateLeaderRequest $request, Leader $leader): RedirectResponse
    {
        $leader->load('user');
        $user = $leader->user;

        $order = $request->input('order');
        if ($order === null || $order === '') {
            $order = 0;
        }

        $payload = [
            'position' => $request->position,
            'order' => (int) $order,
            'social_links' => $request->input('social_links', []),
        ];

        if ($request->hasFile('user_image')) {
            $file = $request->file('user_image');
            $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $newPath = $file->storeAs('members', $filename, 'public');

            $paths = array_unique(array_filter([$user->user_image, $leader->image]));
            foreach ($paths as $path) {
                if ($path && Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            $user->update(['user_image' => $newPath]);
            $payload['image'] = null;
        }

        $leader->update($payload);

        return redirect()->route('leaders.index')
            ->with('success', 'Leader updated successfully.');
    }

    public function destroy(Leader $leader): RedirectResponse
    {
        $leader->delete();

        return redirect()->route('leaders.index')
            ->with('success', 'Leader removed successfully.');
    }

    public function publish(Leader $leader): RedirectResponse
    {
        $leader->update(['is_published' => true]);

        return redirect()->back()
            ->with('success', 'Leader published.');
    }

    public function unpublish(Leader $leader): RedirectResponse
    {
        $leader->update(['is_published' => false]);

        return redirect()->back()
            ->with('success', 'Leader unpublished.');
    }
}
