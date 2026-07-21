@php
    $selectedFirmIds = old('firm_ids', isset($model) ? (
        $model->relationLoaded('firms') && $model->firms->isNotEmpty()
            ? $model->firms->pluck('id')->toArray()
            : ($model->firm_id ? [$model->firm_id] : [])
    ) : (array)($selected_firm_ids ?? old('firm_id', [])));

    if (!is_array($selectedFirmIds)) {
        $selectedFirmIds = $selectedFirmIds ? [$selectedFirmIds] : [];
    }
@endphp

@if(auth()->user() && auth()->user()->isAdmin())
    <div class="form-group">
        <label class="form-label" for="firm_ids">Firm(s) <span>*</span></label>
        <select name="firm_ids[]" id="firm_ids" class="form-control select2-multi @error('firm_ids') is-invalid @enderror" multiple required data-placeholder="Search and select firm(s)...">
            @foreach(\App\Models\Firm::where('status', 'active')->orderBy('firm_name')->get() as $firm)
                <option value="{{ $firm->id }}" {{ in_array($firm->id, $selectedFirmIds) ? 'selected' : '' }}>
                    {{ $firm->firm_name }}
                </option>
            @endforeach
        </select>
        @error('firm_ids') <div class="text-error">{{ $message }}</div> @enderror
        @error('firm_ids.*') <div class="text-error">{{ $message }}</div> @enderror
    </div>
@else
    @php
        $userFirmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');
        $defaultFirmIds = !empty($selectedFirmIds) ? $selectedFirmIds : ($userFirmId ? [$userFirmId] : []);
    @endphp
    @foreach($defaultFirmIds as $fId)
        <input type="hidden" name="firm_ids[]" value="{{ $fId }}">
    @endforeach
    @if(count($defaultFirmIds) > 0)
        <input type="hidden" name="firm_id" value="{{ $defaultFirmIds[0] }}">
    @endif
@endif
