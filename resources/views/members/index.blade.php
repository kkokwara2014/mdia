@extends('layouts.app')

@section('title', 'Members')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
        <div class="col">
            <h2 class="page-title">All Members</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('members.create') }}" class="btn btn-primary">
                Add Member
            </a>
        </div>
    </div>
</div>

<form method="GET" action="{{ route('members.index') }}" class="mb-3">
    <div class="row g-2">
        <div class="col-auto">
            <input type="text" name="search" class="form-control" placeholder="Search by name, email or phone" value="{{ request('search') }}">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-outline-primary">Search</button>
        </div>
        @if(request()->filled('search'))
        <div class="col-auto">
            <a href="{{ route('members.index') }}" class="btn btn-outline-secondary">Clear</a>
        </div>
        @endif
    </div>
</form>

<div class="card">
    <div class="table-responsive">
        <table class="table table-vcenter card-table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th></th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Roles</th>
                    <th>Status</th>
                    <th class="w-1">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $index => $member)
                <tr>
                    <td>{{ $members->firstItem() + $index }}</td>
                    <td>
                        @if($member->user_image)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($member->user_image) }}" alt="" style="width: 32px; height: 32px; object-fit: cover; border-radius: 50%;">
                        @else
                            <span class="avatar rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.75rem;">{{ strtoupper(substr($member->name, 0, 2)) }}</span>
                        @endif
                    </td>
                    <td>{{ $member->name }}</td>
                    <td>{{ $member->email }}</td>
                    <td>{{ $member->phone ?? '—' }}</td>
                    <td>
                        @foreach($member->roles as $role)
                            <span class="badge bg-blue-lt">{{ $role->name }}</span>
                        @endforeach
                        @if($member->roles->isEmpty())
                            —
                        @endif
                    </td>
                    <td>
                        @if($member->password)
                            <span class="badge bg-success-lt">Claimed</span>
                        @else
                            <span class="badge bg-yellow-lt">Unclaimed</span>
                        @endif
                    </td>
                    <td>
                        <div style="display: flex; gap: 6px;">
                            <a href="{{ route('members.show', $member) }}" class="btn btn-sm btn-ghost-primary" title="View">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <circle cx="12" cy="12" r="2" />
                                    <path d="M22 12c-2.667 4.667-6 7-10 7s-7.333-2.333-10-7c2.667-4.667 6-7 10-7s7.333 2.333 10 7" />
                                </svg>
                            </a>
                            <a href="{{ route('members.edit', $member) }}" class="btn btn-sm btn-ghost-warning" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M9 7h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3" />
                                    <path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3" />
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-secondary py-4">No members found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($members->hasPages())
    <div class="card-footer d-flex align-items-center">
        <p class="m-0 text-secondary">
            Showing {{ $members->firstItem() }} to {{ $members->lastItem() }} of {{ $members->total() }} results
        </p>
        <ul class="pagination m-0 ms-auto">
            @if($members->onFirstPage())
                <li class="page-item disabled"><span class="page-link">Previous</span></li>
            @else
                <li class="page-item"><a class="page-link" href="{{ $members->previousPageUrl() }}">Previous</a></li>
            @endif
            @foreach($members->getUrlRange(1, $members->lastPage()) as $page => $url)
                <li class="page-item {{ $members->currentPage() === $page ? 'active' : '' }}">
                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                </li>
            @endforeach
            @if($members->hasMorePages())
                <li class="page-item"><a class="page-link" href="{{ $members->nextPageUrl() }}">Next</a></li>
            @else
                <li class="page-item disabled"><span class="page-link">Next</span></li>
            @endif
        </ul>
    </div>
    @endif
</div>
@endsection
