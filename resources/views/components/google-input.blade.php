@props([
    'type' => 'text',
    'name',
    'id',
    'label' => '',
    'value' => '',
    'selectedValues' => [],
    'options' => [],
    'error' => false,
    'alwaysActive' => false,
    'wireModel' => null,
    'multiple' => false,

    'helpText' => null,
    'filterId' => null,
    'placeholder' => null,
])

@php
    $classes = 'input-box mt-0';
    if ($alwaysActive) $classes .= ' active';
    if ($errors->has($name)) $classes .= ' error';


    // Handle selected values for multiple select
    $selected = $selectedValues;
    if (is_string($selectedValues)) {
        $selected = explode(',', $selectedValues);
    }
    if (!is_array($selected)) {
        $selected = [];
    }
@endphp

<div class="{{ $classes }}" @if($filterId) id="filter-wrapper-{{ $filterId }}" @endif>
    <label class="input-label" for="{{ $id }}">{{ $label }}</label>

    @if ($type === 'select')
        <select
            class="input-1 {{ $multiple ?? '' }} {{ $attributes->get('class') }}"
            name="{{ $name }}{{ $multiple ? '[]' : '' }}"
            id="{{ $id }}"
            @if ($multiple) multiple="multiple" @endif
            @if ($filterId) data-filter-id="{{ $filterId }}" @endif
            @if ($wireModel) wire:model.defer="{{ $wireModel }}" @endif
            onfocus="this.parentNode.classList.add('active')"
            onchange="if(this.value){ this.parentNode.classList.add('active'); } else if(!{{ $alwaysActive ? 'true' : 'false' }}){ this.parentNode.classList.remove('active'); }"
            @if (! $alwaysActive && !$multiple)
                onblur="if(!this.value) this.parentNode.classList.remove('active')"
            @endif
        >
            @if (!$multiple)
                <option value="" selected disabled hidden></option>
            @endif

            @if (!empty($options))
                @foreach($options as $key => $option)
                    @php
                        if (is_array($option)) {
                            $optionValue = $option['id'] ?? $key;
                            $optionLabel = $option['name'] ?? $option['label'] ?? $option['translations'][0]['name'] ?? 'Option #' . $optionValue;
                        } else {
                            $optionValue = $key;
                            $optionLabel = $option;
                        }

                        $isSelected = false;
                        if ($multiple) {
                            $isSelected = in_array($optionValue, $selected);
                        } else {
                            $isSelected = $wireModel ?
                                (data_get($attributes->getAttributes(), 'wire:model') == $optionValue) :
                                old($name, $value) == $optionValue;
                        }
                    @endphp

                    <option value="{{ $optionValue }}" @if($isSelected) selected @endif>
                        {{ $optionLabel }}
                    </option>
                @endforeach
            @else
                <option disabled>No options available</option>
            @endif
        </select>

    @elseif($type === 'textarea')
        <textarea
            name="{{ $name }}"
            id="{{ $id }}"
            class="input-1"
            @if ($wireModel) wire:model.defer="{{ $wireModel }}" @endif
            onfocus="this.parentNode.classList.add('active')"
            onblur="if(!this.value) this.parentNode.classList.remove('active')"
            rows="16"
        >{{ old($name, $value) }}</textarea>


    @else
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $id }}"
            value="{{ old($name, $value) }}"
            class="input-1"
            @if ($wireModel) wire:model.defer="{{ $wireModel }}" @endif
            onfocus="this.parentNode.classList.add('active')"
            onblur="if(!this.value) this.parentNode.classList.remove('active')"
            autocomplete="{{ $type === 'password' ? 'new-password' : 'off' }}"
        />
    @endif

    @if ($helpText)
        <div class="form-text text-muted">{{ $helpText }}</div>
    @endif

    @error($name)
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.input-box input, .input-box select').forEach((el) => {
                if (el.value) {
                    el.closest('.input-box')?.classList.add('active');
                }
            });

            document.addEventListener('input', function (e) {
                const input = e.target;
                const inputBox = input.closest('.input-box');

                if (inputBox && inputBox.classList.contains('error')) {
                    inputBox.classList.remove('error');

                    const errorMsg = inputBox.querySelector('.text-danger');
                    if (errorMsg) {
                        errorMsg.remove();
                    }
                }
            });
        });
    </script>


@endpush
