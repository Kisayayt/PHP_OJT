@foreach ($children as $child)
    <div class="accordion" id="childAccordion{{ $child->id }}">
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingChild{{ $child->id }}">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseChild{{ $child->id }}" aria-expanded="false"
                    aria-controls="collapseChild{{ $child->id }}">
                    <input type="checkbox" name="department_ids[]" value="{{ $child->id }}"
                        class="me-2 department-checkbox">
                    {{ $child->name }}
                </button>
                <button type="button" onclick="window.location.href='/updateDepartment/{{ $child->id }}'"
                    class="btn btn-success mb-2 btn-sm ms-2">Cập nhật</button>
                <button onclick="window.location.href='/departments/{{ $child->id }}/update-status'" type="button"
                    class="btn mb-2 btn-sm ms-2 {{ $child->status ? 'btn-success' : 'btn-secondary' }}">
                    {{ $child->status ? 'Hoạt động' : 'Đình chỉ' }}
                </button>
                <button onclick="window.location.href='/departmentDashboard/{{ $child->id }}/details'"
                    type="button" class="btn btn-info mb-2 btn-sm ms-2">
                    Chi tiết <i class="bi bi-info-circle"></i>
                </button>
            </h2>
            <div id="collapseChild{{ $child->id }}" class="accordion-collapse collapse"
                aria-labelledby="headingChild{{ $child->id }}" data-bs-parent="#childAccordion{{ $child->id }}">
                <div class="accordion-body">
                    @if ($child->children)
                        @include('departments.children', ['children' => $child->children])
                    @endif
                </div>
            </div>
        </div>
    </div>
@endforeach
