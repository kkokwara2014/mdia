@extends('layouts.app')

@section('title', 'Permissions')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
        <div class="col">
            <h2 class="page-title">Permissions</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('permissions.create') }}" class="btn btn-primary">
                Add Permission
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-vcenter card-table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Permission Name</th>
                    <th>Description</th>
                    <th>Assigned To</th>
                    <th class="w-1">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($permissions as $index => $permission)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $permission->name }}</td>
                    <td>{{ $permission->description ?? '—' }}</td>
                    <td>
                        @forelse($permission->roles as $role)
                            <span class="badge bg-blue-lt me-1">{{ $role->name }}</span>
                        @empty
                            <span class="text-secondary">—</span>
                        @endforelse
                    </td>
                    <td>
                        <div style="display: flex; gap: 6px;">
                            <a href="{{ route('permissions.edit', $permission) }}" class="btn btn-sm btn-ghost-warning" title="Edit">
                                <i class="ti ti-edit"></i>
                            </a>
                            <button type="button"
                                    class="btn btn-sm btn-ghost-danger"
                                    title="Delete"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteModal"
                                    data-delete-url="{{ route('permissions.destroy', $permission) }}"
                                    data-name="{{ $permission->name }}">
                                <i class="ti ti-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-secondary py-4">No permissions found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal modal-blur fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-title">Are you sure?</div>
                <div>Do you really want to delete <strong id="deleteModalName"></strong>? This action cannot be undone.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteModalForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteModal = document.getElementById('deleteModal');
    deleteModal.addEventListener('show.bs.modal', function (e) {
        const button = e.relatedTarget;
        const deleteUrl = button.getAttribute('data-delete-url');
        const name = button.getAttribute('data-name');
        document.getElementById('deleteModalName').textContent = name;
        document.getElementById('deleteModalForm').setAttribute('action', deleteUrl);
    });
});
</script>
@endsection
