@extends('layouts.app')

@section('title', 'Add Leader')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
        <div class="col">
            <h2 class="page-title">Add Leader</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('leaders.index') }}" class="btn btn-outline-secondary">Back to Leaders</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger" role="alert">
                <div class="d-flex">
                    <div>
                        <h4 class="alert-title">Please fix the following errors</h4>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('leaders.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3 position-relative" id="memberSearchWrapper">
                <label class="form-label">Member <span class="text-danger">*</span></label>
                <input type="text"
                       id="memberSearch"
                       class="form-control @error('user_uuid') is-invalid @enderror"
                       placeholder="Search member by name or email..."
                       autocomplete="off">
                <div id="memberSearchResults"
                     class="list-group shadow-sm"
                     style="position: absolute; top: 100%; left: 0; right: 0; z-index: 9999; background: #ffffff; border: 1px solid #dee2e6; border-radius: 4px; max-height: 250px; overflow-y: auto; display: none;">
                </div>
                <input type="hidden"
                       id="selectedMemberUuid"
                       name="user_uuid"
                       value="{{ old('user_uuid') }}">
                <div id="selectedMemberDisplay" class="mt-2" style="display: none;">
                    <span class="badge bg-blue-lt px-2 py-1" id="selectedMemberName" style="font-size:13px;"></span>
                    <a href="#" id="clearMember" class="text-danger ms-2" style="font-size:12px;">Clear</a>
                </div>
                @error('user_uuid')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div id="memberImagePreviewBlock" class="mb-3" style="display: none;">
                <label class="form-label">Photo preview</label>
                <div>
                    <img id="memberImagePreview" src="" alt="" width="80" height="80" class="rounded" style="object-fit: cover;">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Member photo</label>
                <input type="file" id="user_image_input" name="user_image" class="form-control @error('user_image') is-invalid @enderror" accept="image/jpeg,image/png,image/webp">
                <small class="text-muted">Optional. JPG, PNG, or WEBP, max 2&nbsp;MB.</small>
                @error('user_image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Position (role) <span class="text-danger">*</span></label>
                <select name="role_uuid" class="form-select @error('role_uuid') is-invalid @enderror" required>
                    <option value="" disabled @selected(old('role_uuid') === null || old('role_uuid') === '')>Select a role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->uuid }}" @selected(old('role_uuid') === $role->uuid)>{{ $role->name }}</option>
                    @endforeach
                </select>
                @error('role_uuid')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Display Order</label>
                <input type="number" name="order" class="form-control @error('order') is-invalid @enderror" value="{{ old('order', 0) }}" min="0" step="1">
                <small class="text-muted">Lower number appears first</small>
                @error('order')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            @include('leaders.partials.social-links')
            @error('social_links')
                <div class="text-danger small mb-2">{{ $message }}</div>
            @enderror

            <button type="submit" class="btn btn-primary">Add Leader</button>
        </form>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('memberSearch');
    const resultsDiv = document.getElementById('memberSearchResults');
    const hiddenInput = document.getElementById('selectedMemberUuid');
    const selectedDisplay = document.getElementById('selectedMemberDisplay');
    const selectedName = document.getElementById('selectedMemberName');
    const clearBtn = document.getElementById('clearMember');
    const memberSearchWrapper = document.getElementById('memberSearchWrapper');
    const previewBlock = document.getElementById('memberImagePreviewBlock');
    const previewImg = document.getElementById('memberImagePreview');
    const fileInput = document.getElementById('user_image_input');
    const searchUrl = @json(route('members.search'));
    let searchTimeout;
    let previewBlobUrl = null;

    function revokePreviewBlob() {
        if (previewBlobUrl) {
            URL.revokeObjectURL(previewBlobUrl);
            previewBlobUrl = null;
        }
    }

    function hidePreview() {
        revokePreviewBlob();
        previewBlock.style.display = 'none';
        previewImg.removeAttribute('src');
    }

    function loadMemberPreview(uuid) {
        if (!uuid) {
            hidePreview();
            return;
        }
        if (fileInput && fileInput.files && fileInput.files.length) {
            return;
        }
        fetch(searchUrl + '?q=' + encodeURIComponent(uuid))
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.length && data[0].image_url) {
                    previewImg.src = data[0].image_url;
                    previewBlock.style.display = 'block';
                } else {
                    previewBlock.style.display = 'block';
                    previewImg.removeAttribute('src');
                }
            })
            .catch(function () { hidePreview(); });
    }

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            revokePreviewBlob();
            if (this.files && this.files[0]) {
                previewBlobUrl = URL.createObjectURL(this.files[0]);
                previewImg.src = previewBlobUrl;
                previewBlock.style.display = 'block';
            } else if (hiddenInput.value) {
                loadMemberPreview(hiddenInput.value);
            } else {
                hidePreview();
            }
        });
    }

    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        const q = this.value.trim();
        if (q.length < 2) {
            resultsDiv.style.display = 'none';
            return;
        }
        searchTimeout = setTimeout(function () {
            fetch(searchUrl + '?q=' + encodeURIComponent(q))
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    resultsDiv.innerHTML = '';
                    if (data.length === 0) {
                        const wrap = document.createElement('div');
                        wrap.className = 'list-group-item text-muted';
                        wrap.innerHTML = 'No member found. <a href="' + @json(route('members.create')) + '" class="text-primary ms-1">You need to save a member before adding as leader.</a>';
                        resultsDiv.appendChild(wrap);
                    } else {
                        data.forEach(function (member) {
                            const item = document.createElement('a');
                            item.href = '#';
                            item.className = 'list-group-item list-group-item-action py-2';
                            item.style.background = '#ffffff';
                            item.style.borderBottom = '1px solid #f0f0f0';
                            item.textContent = member.name + ' (' + member.email + ')';
                            item.addEventListener('click', function (e) {
                                e.preventDefault();
                                hiddenInput.value = member.uuid;
                                selectedName.textContent = member.name;
                                selectedDisplay.style.display = 'block';
                                searchInput.style.display = 'none';
                                resultsDiv.style.display = 'none';
                                if (fileInput) {
                                    fileInput.value = '';
                                }
                                loadMemberPreview(member.uuid);
                            });
                            resultsDiv.appendChild(item);
                        });
                    }
                    resultsDiv.style.display = 'block';
                });
        }, 300);
    });

    clearBtn.addEventListener('click', function (e) {
        e.preventDefault();
        hiddenInput.value = '';
        selectedName.textContent = '';
        selectedDisplay.style.display = 'none';
        searchInput.style.display = 'block';
        searchInput.value = '';
        if (fileInput) {
            fileInput.value = '';
        }
        hidePreview();
    });

    document.addEventListener('click', function (e) {
        if (memberSearchWrapper && !memberSearchWrapper.contains(e.target)) {
            resultsDiv.style.display = 'none';
        }
    });

    if (hiddenInput.value) {
        selectedName.textContent = 'Selected member';
        selectedDisplay.style.display = 'block';
        searchInput.style.display = 'none';
        loadMemberPreview(hiddenInput.value);
    }
});
</script>
@endsection
