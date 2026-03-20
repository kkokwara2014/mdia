@extends('layouts.app')

@section('title', 'Leaders')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
        <div class="col">
            <h2 class="page-title">Organization Leaders</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('leaders.create') }}" class="btn btn-primary">Add Leader</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-vcenter card-table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Order</th>
                    <th>Status</th>
                    <th class="w-1">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leaders as $leader)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <img src="{{ $leader->getImageUrl() }}" alt="" width="40" height="40" class="rounded-circle" style="object-fit: cover;">
                    </td>
                    <td>{{ $leader->getName() }}</td>
                    <td>{{ $leader->position }}</td>
                    <td>{{ $leader->order }}</td>
                    <td>
                        @if($leader->is_published)
                            <span class="badge bg-success">Published</span>
                        @else
                            <span class="badge bg-warning">Unpublished</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex flex-row flex-nowrap align-items-center gap-1">
                            <a href="{{ route('leaders.edit', $leader) }}" class="btn btn-sm btn-ghost-warning" title="Edit">
                                <i class="ti ti-edit"></i>
                            </a>
                            @if(!$leader->is_published)
                            <button type="button"
                                    class="btn btn-sm btn-ghost-primary"
                                    title="Publish"
                                    data-bs-toggle="modal"
                                    data-bs-target="#publishModal"
                                    data-action-url="{{ route('leaders.publish', $leader) }}"
                                    data-leader-name="{{ $leader->getName() }}">
                                <i class="ti ti-eye"></i>
                            </button>
                            @endif
                            @if($leader->is_published)
                            <button type="button"
                                    class="btn btn-sm btn-ghost-secondary"
                                    title="Unpublish"
                                    data-bs-toggle="modal"
                                    data-bs-target="#unpublishModal"
                                    data-action-url="{{ route('leaders.unpublish', $leader) }}"
                                    data-leader-name="{{ $leader->getName() }}">
                                <i class="ti ti-eye-off"></i>
                            </button>
                            @endif
                            <button type="button"
                                    class="btn btn-sm btn-ghost-danger"
                                    title="Delete"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteModal"
                                    data-delete-url="{{ route('leaders.destroy', $leader) }}"
                                    data-name="{{ $leader->getName() }}">
                                <i class="ti ti-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-secondary py-4">No leaders found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal modal-blur fade" id="publishModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-title">Publish leader</div>
                <div>Publish <strong id="publishModalName"></strong> on the public about page?</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Cancel</button>
                <form id="publishModalForm" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary">Publish</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-blur fade" id="unpublishModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-title">Unpublish leader</div>
                <div>Remove <strong id="unpublishModalName"></strong> from the public about page?</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Cancel</button>
                <form id="unpublishModalForm" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-warning">Unpublish</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-blur fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-title">Are you sure?</div>
                <div>Remove <strong id="deleteModalName"></strong> from leaders? The member account will not be deleted.</div>
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
    const publishModal = document.getElementById('publishModal');
    if (publishModal) {
        publishModal.addEventListener('show.bs.modal', function (e) {
            const button = e.relatedTarget;
            document.getElementById('publishModalName').textContent = button.getAttribute('data-leader-name');
            document.getElementById('publishModalForm').setAttribute('action', button.getAttribute('data-action-url'));
        });
    }
    const unpublishModal = document.getElementById('unpublishModal');
    if (unpublishModal) {
        unpublishModal.addEventListener('show.bs.modal', function (e) {
            const button = e.relatedTarget;
            document.getElementById('unpublishModalName').textContent = button.getAttribute('data-leader-name');
            document.getElementById('unpublishModalForm').setAttribute('action', button.getAttribute('data-action-url'));
        });
    }
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (e) {
            const button = e.relatedTarget;
            document.getElementById('deleteModalName').textContent = button.getAttribute('data-name');
            document.getElementById('deleteModalForm').setAttribute('action', button.getAttribute('data-delete-url'));
        });
    }
});
</script>
@endsection
