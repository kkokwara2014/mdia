<?php

namespace App\Http\Controllers\Web\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Models\Role;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
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

    public function store(StoreMemberRequest $request): View
    {
        $userImage = null;
        if ($request->hasFile('user_image')) {
            $userImage = $request->file('user_image')->store('members', 'public');
        }

        $uppercase = chr(rand(65, 90));
        $lowercase = substr(str_shuffle('abcdefghjkmnpqrstuvwxyz'), 0, 4);
        $numbers = substr(str_shuffle('23456789'), 0, 2);
        $special = ['@', '#', '$', '!'][rand(0, 3)];
        $plainPassword = str_shuffle(
            $uppercase . $lowercase . $numbers . $special
        );

        $member = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($plainPassword),
            'user_image' => $userImage,
        ]);

        $memberRole = Role::firstOrCreate(['name' => 'Member']);
        if ($request->has('roles') && count($request->roles) > 0) {
            $roleIds = Role::whereIn('uuid', $request->roles)->pluck('id');
            $member->roles()->sync($roleIds->merge([$memberRole->id])->unique()->values()->all());
        } else {
            $member->roles()->sync([$memberRole->id]);
        }

        $member->load(['roles', 'payments' => fn ($q) => $q->with(['paymentType', 'verifiedBy'])->orderBy('payment_date', 'desc')]);

        return view('members.show', [
            'member' => $member,
            'generatedPassword' => $plainPassword,
            'successMessage' => 'Member created successfully.'
        ]);
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

        if (auth()->user()->hasPermission('super_admin')) {
            $memberRole = Role::firstOrCreate(['name' => 'Member']);
            if ($request->has('roles') && count($request->roles) > 0) {
                $roleIds = Role::whereIn('uuid', $request->roles)->pluck('id');
                $user->roles()->sync($roleIds->merge([$memberRole->id])->unique()->values()->all());
            } else {
                $user->roles()->sync([$memberRole->id]);
            }
        }

        return redirect()->route('members.show', ['user' => $user->uuid])->with('success', 'Member updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->roles->contains('name', 'Super Admin')) {
            return redirect()->back()->with('error', 'Cannot delete a Super Admin.');
        }

        // Clear verified_by on payments this user verified (to avoid FK constraint)
        $user->verifiedPayments()->update(['verified_by' => null]);

        if ($user->user_image && Storage::disk('public')->exists($user->user_image)) {
            Storage::disk('public')->delete($user->user_image);
        }

        $user->delete();

        return redirect()->route('members.index')->with('success', 'Member deleted successfully.');
    }

    public function regeneratePassword(User $user): View
    {
        $uppercase = chr(rand(65, 90));
        $lowercase = substr(str_shuffle(
            'abcdefghjkmnpqrstuvwxyz'), 0, 4);
        $numbers = substr(str_shuffle('23456789'), 0, 2);
        $special = ['@', '#', '$', '!'][rand(0, 3)];
        $plainPassword = str_shuffle(
            $uppercase . $lowercase . $numbers . $special
        );

        $user->update([
            'password' => Hash::make($plainPassword),
        ]);

        $user->load(['roles', 'payments' => fn ($q) => $q->with(['paymentType', 'verifiedBy'])->orderBy('payment_date', 'desc')]);

        return view('members.show', [
            'member' => $user,
            'generatedPassword' => $plainPassword,
            'successMessage' => 'Password regenerated successfully.'
        ]);
    }

    public function downloadPdf(): Response
    {
        $members = User::query()
            ->orderBy('name')
            ->get(['name']);

        $pdf = Pdf::loadView('members.pdf', [
            'members' => $members->pluck('name')->values()->all(),
        ]);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('mdia-members-' . now()->format('Y-m-d') . '.pdf');
    }
}
