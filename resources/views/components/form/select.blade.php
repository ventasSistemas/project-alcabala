<div class="mb-3">
    <label class="form-label">{{ $label }}</label>
    <select name="{{ $name }}" class="form-select">
        <option value="">Seleccione...</option>
        @foreach($options as $key => $text)
            <option value="{{ $key }}" {{ old($name, $value ?? '') == $key ? 'selected' : '' }}>{{ $text }}</option>
        @endforeach
    </select>
    @error($name)
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>