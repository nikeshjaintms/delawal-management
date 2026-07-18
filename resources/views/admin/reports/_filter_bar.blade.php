{{--
    Shared report filter bar partial.
    Usage: @include('admin.reports._filter_bar', ['route' => 'reports.sales'])
    Optional slots: $extraFilters (raw HTML blade), $hasFilters (bool)
--}}
<div class="card-box" style="margin-bottom:20px;">
    <form method="GET" action="{{ route($route) }}" class="rpt-filter-bar">
        <div class="rpt-filter-group">
            <span class="rpt-filter-label">From Date</span>
            <input type="date" name="from_date" value="{{ request('from_date') }}" class="rpt-filter-ctrl @error('from_date') is-invalid @enderror">
        </div>
        <div class="rpt-filter-group">
            <span class="rpt-filter-label">To Date</span>
            <input type="date" name="to_date" value="{{ request('to_date') }}" class="rpt-filter-ctrl @error('to_date') is-invalid @enderror">
        </div>
        @isset($extraFilters)
            {!! $extraFilters !!}
        @endisset
        <button type="submit" class="rpt-btn-search">
            <i class="fa-solid fa-magnifying-glass"></i> Apply
        </button>
        @if(request()->hasAny(['from_date','to_date']) || ($hasFilters ?? false))
            <a href="{{ route($route) }}" class="rpt-btn-reset">
                <i class="fa-solid fa-rotate-left"></i> Reset
            </a>
        @endif
    </form>
</div>
