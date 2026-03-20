@php
    use App\Models\Leader;
    $rows = old('social_links', $initialSocialLinks ?? []);
    if (!is_array($rows)) {
        $rows = [];
    }
    $platformDefs = Leader::socialPlatformDefinitions();
@endphp
<div class="mb-3" id="leader-social-root">
    <div class="d-flex align-items-center gap-2 mb-2">
        <label class="form-label mb-0">Social links</label>
        <button type="button" class="btn btn-icon btn-sm btn-outline-primary" id="leader-social-add" title="Add social link" aria-label="Add social link">
            <i class="ti ti-plus"></i>
        </button>
    </div>
    <div id="leader-social-rows" class="d-flex flex-column gap-2">
        @foreach($rows as $idx => $row)
            @php
                $p = is_array($row) ? ($row['platform'] ?? '') : '';
                $u = is_array($row) ? ($row['url'] ?? '') : '';
            @endphp
            <div class="leader-social-row d-flex flex-column gap-2">
                <div class="d-flex align-items-center gap-2 w-100">
                    <select name="social_links[{{ $idx }}][platform]" class="form-select leader-social-platform flex-shrink-0" style="min-width: 0; max-width: 14rem; width: 100%;">
                        <option value="">Platform</option>
                        @foreach($platformDefs as $key => $meta)
                            <option value="{{ $key }}" @selected($p === $key)>{{ $meta['label'] }}</option>
                        @endforeach
                    </select>
                    <span class="text-secondary d-inline-flex align-items-center leader-social-admin-icon flex-shrink-0" aria-hidden="true"></span>
                    <button type="button" class="btn btn-icon btn-ghost-danger leader-social-remove flex-shrink-0 ms-auto" title="Remove" aria-label="Remove social link">
                        <i class="ti ti-trash"></i>
                    </button>
                </div>
                <input type="url" name="social_links[{{ $idx }}][url]" value="{{ $u }}" class="form-control leader-social-url w-100" placeholder="https://" autocomplete="url">
            </div>
        @endforeach
    </div>
    <template id="leader-social-row-template">
        <div class="leader-social-row d-flex flex-column gap-2">
            <div class="d-flex align-items-center gap-2 w-100">
                <select name="social_links[__IDX__][platform]" class="form-select leader-social-platform flex-shrink-0" style="min-width: 0; max-width: 14rem; width: 100%;">
                    <option value="">Platform</option>
                    @foreach($platformDefs as $key => $meta)
                        <option value="{{ $key }}">{{ $meta['label'] }}</option>
                    @endforeach
                </select>
                <span class="text-secondary d-inline-flex align-items-center leader-social-admin-icon flex-shrink-0" aria-hidden="true"></span>
                <button type="button" class="btn btn-icon btn-ghost-danger leader-social-remove flex-shrink-0 ms-auto" title="Remove" aria-label="Remove social link">
                    <i class="ti ti-trash"></i>
                </button>
            </div>
            <input type="url" name="social_links[__IDX__][url]" value="" class="form-control leader-social-url w-100" placeholder="https://" autocomplete="url">
        </div>
    </template>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var root = document.getElementById('leader-social-root');
    if (!root) return;
    var container = document.getElementById('leader-social-rows');
    var tpl = document.getElementById('leader-social-row-template');
    var addBtn = document.getElementById('leader-social-add');
    var platformIcons = @json(collect(Leader::socialPlatformDefinitions())->mapWithKeys(fn ($meta, $key) => [$key => $meta['admin_icon']])->all());

    function rowIndex() {
        return container.querySelectorAll('.leader-social-row').length;
    }

    function reindexRows() {
        container.querySelectorAll('.leader-social-row').forEach(function (row, i) {
            row.querySelectorAll('[name]').forEach(function (el) {
                var n = el.getAttribute('name');
                if (n) {
                    el.setAttribute('name', n.replace(/social_links\[\d+]/, 'social_links[' + i + ']'));
                }
            });
        });
    }

    function setRowIcon(row) {
        var sel = row.querySelector('select');
        var holder = row.querySelector('.leader-social-admin-icon');
        if (!sel || !holder) return;
        var icon = platformIcons[sel.value];
        holder.innerHTML = icon ? '<i class="ti ' + icon + ' fs-4"></i>' : '';
    }

    function wireRow(row) {
        var sel = row.querySelector('select');
        if (sel) {
            sel.addEventListener('change', function () { setRowIcon(row); });
            setRowIcon(row);
        }
        var rm = row.querySelector('.leader-social-remove');
        if (rm) {
            rm.addEventListener('click', function () {
                row.remove();
                reindexRows();
            });
        }
    }

    container.querySelectorAll('.leader-social-row').forEach(wireRow);

    if (addBtn && tpl) {
        addBtn.addEventListener('click', function () {
            var html = tpl.innerHTML.replace(/__IDX__/g, String(rowIndex()));
            var wrap = document.createElement('div');
            wrap.innerHTML = html.trim();
            var row = wrap.firstElementChild;
            if (row) {
                container.appendChild(row);
                wireRow(row);
                reindexRows();
            }
        });
    }
});
</script>
