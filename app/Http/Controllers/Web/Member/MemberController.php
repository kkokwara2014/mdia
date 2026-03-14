<?php

namespace App\Http\Controllers\Web\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $q = $request->input('q', '');
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $users = User::query()
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', '%' . $q . '%')
                    ->orWhere('email', 'like', '%' . $q . '%');
            })
            ->orderBy('name')
            ->limit(10)
            ->get(['uuid', 'name', 'email']);

        return response()->json($users);
    }

    public function index(): View
    {
        $query = User::query()->with('roles');

        if (request()->filled('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $members = $query->paginate(15)->withQueryString();

        return view('members.index', compact('members'));
    }

    public function create(): View
    {
        $roles = Role::all();

        return view('members.create', compact('roles'));
    }

    public function store(StoreMemberRequest $request): RedirectResponse
    {
        $userImage = null;
        if ($request->hasFile('user_image')) {
            $userImage = $request->file('user_image')->store('members', 'public');
        }

        $member = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => null,
            'user_image' => $userImage,
        ]);

        if ($request->has('roles') && count($request->roles) > 0) {
            $roleIds = Role::whereIn('uuid', $request->roles)->pluck('id');
            $member->roles()->sync($roleIds);
        } else {
            $memberRole = Role::firstOrCreate(['name' => 'Member']);
            $member->roles()->sync([$memberRole->id]);
        }

        return redirect()->route('members.index')->with('success', 'Member created successfully.');
    }

    public function show(User $user): View
    {
        $user->load(['roles', 'payments' => fn ($q) => $q->with(['paymentType', 'verifiedBy'])->orderBy('payment_date', 'desc')]);

        return view('members.show', ['member' => $user]);
    }

    public function edit(User $user): View
    {
        $roles = Role::all();

        return view('members.edit', ['member' => $user, 'roles' => $roles]);
    }

    public function update(UpdateMemberRequest $request, User $user): RedirectResponse
    {
        $userImage = $user->user_image;

        if ($request->hasFile('user_image')) {
            if ($user->user_image && Storage::disk('public')->exists($user->user_image)) {
                Storage::disk('public')->delete($user->user_image);
            }
            $userImage = $request->file('user_image')->store('members', 'public');
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'user_image' => $userImage,
        ]);

        if ($request->has('roles') && count($request->roles) > 0) {
            $roleIds = Role::whereIn('uuid', $request->roles)->pluck('id');
            $user->roles()->sync($roleIds);
        }

        return redirect()->route('members.show', $user)->with('success', 'Member updated successfully.');
    }
}
