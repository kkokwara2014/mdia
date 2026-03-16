@extends('layouts.app')

@section('title', 'My Profile')

@section('css')
<style>
.profile-cards-full { display: flex; flex-direction: column; gap: var(--card-gap); width: 100%; }
.profile-cards-full .card { width: 100% !important; max-width: none !important; }
</style>
@endsection

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
        <div class="col">
            <h2 class="page-title">My Profile</h2>
        </div>
    </div>
</div>

<div class="profile-cards-full">
    <div class="card card-centered">
        <div class="card-body text-center">
            <img src="{{ $user->getAvatarUrl() }}" alt="{{ $user->name }}" style="width: var(--avatar-xl); height: var(--avatar-xl); object-fit: cover; border-radius: 50%;" class="mb-3">
            <h3 class="card-title mb-1">{{ $user->name }}</h3>
            <div class="text-secondary mb-1">{{ $user->email }}</div>
            <div class="text-secondary mb-2">{{ $user->phone ?? '—' }}</div>
            <div class="text-secondary small mb-2">Member since {{ $user->created_at->format('M j, Y') }}</div>
            <div class="mt-2">
                @foreach($user->roles as $role)
                    <span class="badge bg-blue-lt">{{ $role->name }}</span>
                @endforeach
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Update Profile</h3>
        </div>
        <div class="card-body">
                @if($errors->has(['name', 'email', 'phone', 'user_image']))
                    <div class="alert alert-danger" role="alert">
                        <div class="d-flex">
                            <div>
                                <h4 class="alert-title">Please fix the following errors</h4>
                                <ul class="mb-0">
                                    @foreach($errors->get(['name', 'email', 'phone', 'user_image']) as $messages)
                                        @foreach((array) $messages as $message)
                                            <li>{{ $message }}</li>
                                        @endforeach
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label required">Name</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Phone</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}" required>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">User Image</label>
                        <div class="mb-2">
                            <img src="{{ $user->getAvatarUrl() }}" alt="" class="rounded" style="width: var(--avatar-md); height: var(--avatar-md); object-fit: cover;">
                        </div>
                        <input type="file" name="user_image" class="form-control @error('user_image') is-invalid @enderror" accept="image/jpeg,image/png,image/webp">
                        <small class="form-hint">Leave empty to keep current image.</small>
                        @error('user_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Change Password</h3>
                </div>
            <div class="card-body">
                @if($errors->has(['current_password', 'password']))
                    <div class="alert alert-danger" role="alert">
                        <div class="d-flex">
                            <div>
                                <h4 class="alert-title">Please fix the following errors</h4>
                                <ul class="mb-0">
                                    @foreach($errors->get(['current_password', 'password']) as $messages)
                                        @foreach((array) $messages as $message)
                                            <li>{{ $message }}</li>
                                        @endforeach
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
                <form method="POST" action="{{ route('profile.change-password') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label required">Current Password</label>
                        <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required autocomplete="current-password">
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">New Password</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required minlength="8" autocomplete="new-password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required minlength="8" autocomplete="new-password">
                    </div>
                    <button type="submit" class="btn btn-primary">Change Password</button>
                </form>
            </div>
        </div>
</div>
@endsection
