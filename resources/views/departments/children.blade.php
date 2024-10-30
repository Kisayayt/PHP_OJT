@foreach ($children as $child)
    <div class="accordion" id="childAccordion{{ $child->id }}">
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingChild{{ $child->id }}">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseChild{{ $child->id }}" aria-expanded="false"
                    aria-controls="collapseChild{{ $child->id }}">
                    <input type="checkbox" name="selected_departments[]" value="{{ $child->id }}"
                        class="me-2 department-checkbox">
                    {{ $child->name }}
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
