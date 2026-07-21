@if(auth()->user() && auth()->user()->isAdmin())
    <div class="form-group">
        <label class="form-label" for="firm_id">Firm <span>*</span></label>
        <select name="firm_id" id="firm_id" class="form-control @error('firm_id') is-invalid @enderror" required>
            <option value="">-- Select Firm --</option>
            @foreach(\App\Models\Firm::where('status', 'active')->orderBy('firm_name')->get() as $firm)
                <option value="{{ $firm->id }}" {{ (old('firm_id', $selected_firm_id ?? ($model->firm_id ?? '')) == $firm->id) ? 'selected' : '' }}>
                    {{ $firm->firm_name }}
                </option>
            @endforeach
        </select>
        @error('firm_id') <div class="text-error">{{ $message }}</div> @enderror
    </div>
@else
    <input type="hidden" name="firm_id" value="{{ old('firm_id', $selected_firm_id ?? ($model->firm_id ?? (auth()->user() ? auth()->user()->firm_id : session('firm_id')))) }}">
@endif
