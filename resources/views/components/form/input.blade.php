<div class="mb-3">
    <label class="form-label">{{ $label }}</label>
    <input type="{{ $type ?? 'text' }}" name="{{ $name }}" value="{{ old($name, $value ?? '') }}" class="form-control">
    @error($name)
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>