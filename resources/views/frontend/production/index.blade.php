{{-- resources/views/frontend/production/index.blade.php --}}
@extends('frontend.layouts.app')

@section('title', 'Tutti i Record di Produzione')

<style>
    .filter-chip .remove-filter {
        font-weight: bold;
        margin-left: 6px;
        cursor: pointer;
    }

    .filter-chip .remove-filter:hover {
        color: red;
    }

    .btn-gold {
        border: 1px solid #e2ae76 !important;
        color: #e2ae76 !important;
        background-color: transparent !important;
    }

    .btn-gold:hover {
        background-color: #e2ae76 !important;
        color: white !important;
    }

    .btn-deepblue {
        border: 1px solid #041930 !important;
        color: #041930 !important;
        background-color: transparent !important;
    }

    .btn-deepblue:hover {
        background-color: #041930 !important;
        color: white !important;
    }

    .btn-red {
        border: 1px solid red !important;
        color: red !important;
        background-color: transparent !important;
    }

    .btn-red:hover {
        background-color: red !important;
        color: white !important;
    }

    .page-header {
        background-color: #041930;
        color: #e2ae76;
        padding: 1rem 2rem;
        border-radius: 0.75rem;
        margin-bottom: 2rem;
        font-size: 2rem;
        font-weight: bold;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .page-header i {
        font-size: 2rem;
        color: #e2ae76;
    }

    .filter-chip {
        display: inline-block;
        background: #e2ae76;
        color: #041930;
        padding: .25em .6em;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        font-weight: 500;
        margin-right: 0.25rem;
        margin-top: 0.25rem;
    }

    .revenue-card {
        background: linear-gradient(to right, #041930 0%, #e2ae76 100%);
        color: #fff;
        border-radius: 0.75rem;
    }

    .revenue-card .card-body i {
        color: #e2ae76;
    }

    .revenue-card .h5,
    .revenue-card .h3 {
        color: #fff;
    }

    .filter-card {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: .75rem;
    }

    .filter-card .dropdown-menu {
        max-height: 200px;
        overflow-y: auto;
        border-radius: .5rem;
    }

    .production-table {
        border-radius: .5rem;
        overflow: hidden;
    }

    .production-table thead th {
        background-color: #e2ae76 !important;
        color: #041930 !important;
        text-align: center;
        vertical-align: middle;
    }

    .production-table tbody td {
        text-align: center;
        vertical-align: middle;
    }

    .production-table tbody tr:hover {
        background: rgba(13, 110, 253, .05);
    }

    .detail-row td {
        background: #fafafa;
    }

    .toggle-btn i {
        transition: transform .2s;
    }

    .toggle-btn.open i {
        transform: rotate(90deg);
    }
</style>

@section('content')
    @php
        use Illuminate\Support\Str;
        $allRecipes = $productions->flatMap(fn($p) => $p->details->pluck('recipe.recipe_name'))->unique()->sort();
        $allChefs = $productions->flatMap(fn($p) => $p->details->pluck('chef.name'))->unique()->sort();
    @endphp

    <div class="container py-5">
        {{-- Page Header --}}
        <div class="page-header mb-4">
            <i class="bi bi-gear-fill"></i>
            <span>Record di Produzione</span>
        </div>

        {{-- Filters Card --}}
        <div class="card filter-card mb-4 shadow-sm p-3">
            <div class="row g-3 align-items-end">
                {{-- Recipe Filter --}}
                <div class="col-md-3">
                    <label class="form-label small">Ricetta</label>
                    <div class="dropdown" data-bs-auto-close="outside">
                        <button class="btn btn-outline-primary w-100 text-start dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-journal-bookmark me-1"></i> Ricette
                        </button>
                        <div class="dropdown-menu p-3">
                            @foreach ($allRecipes as $r)
                                @php $slug = Str::slug($r,'_') @endphp
                                <div class="form-check mb-1">
                                    <input class="form-check-input recipe-checkbox" type="checkbox"
                                        value="{{ strtolower($r) }}" id="recipe_{{ $slug }}">
                                    <label class="form-check-label"
                                        for="recipe_{{ $slug }}">{{ $r }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Chef Filter --}}
                <div class="col-md-3">
                    <label class="form-label small">Pasticcere</label>
                    <div class="dropdown" data-bs-auto-close="outside">
                        <button class="btn btn-outline-success w-100 text-start dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-person-lines-fill me-1"></i> Pasticcere
                        </button>
                        <div class="dropdown-menu p-3">
                            @foreach ($allChefs as $c)
                                @php $slug = Str::slug($c,'_') @endphp
                                <div class="form-check mb-1">
                                    <input class="form-check-input chef-checkbox" type="checkbox"
                                        value="{{ strtolower($c) }}" id="chef_{{ $slug }}">
                                    <label class="form-check-label"
                                        for="chef_{{ $slug }}">{{ $c }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Date Range Filter --}}
                <div class="col-md-2">
                    <label class="form-label small">Da</label>
                    <input type="date" id="filterStartDate" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">A</label>
                    <input type="date" id="filterEndDate" class="form-control">
                </div>
            </div>
        </div>

        {{-- Active Filters Chips --}}
        <div id="activeFilters" class="mb-4"></div>

        {{-- Total Revenue Card --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card revenue-card shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-cash-stack fs-2 me-3"></i>
                            <div>
                                <div class="small text-white">Potenziale Totale</div>
                                <div class="h5 fw-bold text-white mb-0">Ricavi</div>
                            </div>
                        </div>
                        <div id="totalRevenue" class="h3 fw-bold text-white">€0.00</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card production-table shadow-sm">
            <div class="card-body p-0">
                <table class="table mb-0" id="productionTable">
                    <thead>
                        <tr>
                            <th style="width:48px"></th>
                            <th>Data</th>
                            <th>Voci</th>
                            <th>Potenziale</th>
                            <th class="text-center">Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($productions as $p)
                            @php
                                $equipNames = collect(explode(',', $p->details->pluck('equipment_ids')->join(',')))
                                    ->filter()
                                    ->unique()
                                    ->map(fn($id) => $equipmentMap[$id] ?? '')
                                    ->filter()
                                    ->implode(', ');
                                $rowRecipes = strtolower($p->details->pluck('recipe.recipe_name')->join(' '));
                                $rowChefs = strtolower($p->details->pluck('chef.name')->join(' '));
                            @endphp

                            {{-- main row --}}
                            <tr class="prod-row" data-recipes="{{ $rowRecipes }}" data-chefs="{{ $rowChefs }}"
                                data-equipment="{{ strtolower($equipNames) }}" data-date="{{ $p->production_date }}">
                                <td>
                                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse"
                                        data-bs-target="#detail-{{ $p->id }}" aria-expanded="false"
                                        aria-controls="detail-{{ $p->id }}">
                                        <i class="bi bi-caret-right-fill"></i>
                                    </button>
                                </td>
                                <td>{{ $p->production_date }}</td>
                                <td>{{ $p->details->count() }}</td>
                                <td class="row-potential" data-original="{{ $p->total_potential_revenue }}">
                                    €{{ number_format($p->total_potential_revenue, 2) }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('production.show', $p) }}" class="btn btn-sm btn-deepblue me-1"><i
                                            class="bi bi-eye"></i></a>
                                    <a href="{{ route('production.edit', $p) }}" class="btn btn-sm btn-gold me-1"><i
                                            class="bi bi-pencil"></i></a>
                                    <form action="{{ route('production.destroy', $p) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Eliminare?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-red"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>

                            {{-- detail row, hidden by default --}}
                            <tr id="detail-{{ $p->id }}" class="detail-row collapse">
                                <td colspan="5" class="p-3">
                                    <ul class="mb-0 ps-3">
                                        @foreach ($p->details as $d)
                                            @php
                                                $ids = array_filter(explode(',', $d->equipment_ids));
                                                $names = collect($ids)
                                                    ->map(fn($id) => $equipmentMap[$id] ?? '')
                                                    ->filter()
                                                    ->implode(', ');
                                            @endphp
                                            <li data-recipe="{{ strtolower($d->recipe->recipe_name) }}"
                                                data-potential="{{ $d->potential_revenue }}">
                                                <strong>{{ $d->recipe->recipe_name }}</strong> × {{ $d->quantity }}
                                                — Chef: {{ $d->chef->name }}, <i class="bi bi-tools"></i>
                                                {{ $names ?: '—' }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection


@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const recipeCBs      = Array.from(document.querySelectorAll('.recipe-checkbox'));
    const chefCBs        = Array.from(document.querySelectorAll('.chef-checkbox'));
    const equipmentCBs   = Array.from(document.querySelectorAll('.equipment-checkbox'));
    const rows           = Array.from(document.querySelectorAll('.prod-row'));
    const detailRows     = Array.from(document.querySelectorAll('.detail-row'));
    const totalRevElem   = document.getElementById('totalRevenue');
    const activeTags     = document.getElementById('activeFilters');
    const startInput     = document.getElementById('filterStartDate');
    const endInput       = document.getElementById('filterEndDate');

    // Toggle detail-row carets
    document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(btn => {
        btn.addEventListener('click', () => {
            const icon = btn.querySelector('i');
            icon.classList.toggle('bi-caret-right-fill');
            icon.classList.toggle('bi-caret-down-fill');
        });
    });

    // Build the little chips under the filters
    function updateActiveFilters(selRecipes, selChefs, selEquipments) {
        activeTags.innerHTML = '';

        selRecipes.forEach(r => {
            const span = document.createElement('span');
            span.className = 'filter-chip';
            span.innerHTML = `${r} <span class="remove-filter" data-type="recipe" data-value="${r}">&times;</span>`;
            activeTags.appendChild(span);
        });
        selChefs.forEach(c => {
            const span = document.createElement('span');
            span.className = 'filter-chip';
            span.innerHTML = `${c} <span class="remove-filter" data-type="chef" data-value="${c}">&times;</span>`;
            activeTags.appendChild(span);
        });
        selEquipments.forEach(e => {
            const span = document.createElement('span');
            span.className = 'filter-chip';
            span.innerHTML = `${e} <span class="remove-filter" data-type="equipment" data-value="${e}">&times;</span>`;
            activeTags.appendChild(span);
        });

        // remove-chip click handlers
        document.querySelectorAll('.remove-filter').forEach(el => {
            el.addEventListener('click', () => {
                const { type, value } = el.dataset;
                let group = type === 'recipe' ? recipeCBs
                          : type === 'chef'   ? chefCBs
                                              : equipmentCBs;
                group.forEach(cb => {
                    if (cb.value === value) cb.checked = false;
                });
                filterTable();
            });
        });
    }

    // Main filter function
    function filterTable() {
        const selRecipes    = recipeCBs.filter(cb => cb.checked).map(cb => cb.value.toLowerCase());
        const selChefs      = chefCBs.filter(cb => cb.checked).map(cb => cb.value.toLowerCase());
        const selEquipments = equipmentCBs.filter(cb => cb.checked).map(cb => cb.value.toLowerCase());

        updateActiveFilters(selRecipes, selChefs, selEquipments);

        // parse date inputs
        const startDate = startInput.value ? new Date(startInput.value) : null;
        const endDate   = endInput.value   ? new Date(endInput.value)   : null;

        let grandTotal = 0;

        rows.forEach((row, i) => {
            const recs      = row.dataset.recipes;
            const chefs     = row.dataset.chefs;
            const equipment = row.dataset.equipment;
            const rowDate   = new Date(row.dataset.date);

            // apply recipe/chef/equipment filters
            const recipeMatch = !selRecipes.length    || selRecipes.some(r => recs.includes(r));
            const chefMatch   = !selChefs.length      || selChefs.some(c => chefs.includes(c));
            const equipMatch  = !selEquipments.length || selEquipments.some(e => equipment.includes(e));

            // apply date range
            let dateMatch = true;
            if (startDate && rowDate < startDate) dateMatch = false;
            if (endDate   && rowDate > endDate)   dateMatch = false;

            const showRow = recipeMatch && chefMatch && equipMatch && dateMatch;

            // show/hide main + detail row
            row.style.display         = showRow ? '' : 'none';
            detailRows[i].style.display = showRow ? '' : 'none';

            if (!showRow) return;

            // recalculate row total based on detail-row visibility
            let rowSum = 0;
            detailRows[i].querySelectorAll('li').forEach(li => {
                const recipe    = li.dataset.recipe;
                const potential = parseFloat(li.dataset.potential) || 0;
                const chefMatchDetail = selChefs.length
                    ? li.textContent.toLowerCase().match(/chef:\s*([^,]+)/i)?.[1]?.trim()
                    : true;
                const equipMatchDetail = true; // keep existing logic

                const recMatchDetail = !selRecipes.length || selRecipes.includes(recipe);

                const showDetail = recMatchDetail && chefMatchDetail && equipMatchDetail;
                if (showDetail) {
                    li.style.display = '';
                    rowSum += potential;
                } else {
                    li.style.display = 'none';
                }
            });

            // update row total
            const cell = row.querySelector('.row-potential');
            cell.textContent = `€${rowSum.toFixed(2)}`;
            grandTotal += rowSum;
        });

        totalRevElem.textContent = `€${grandTotal.toFixed(2)}`;
    }

    // wire up all change events
    [...recipeCBs, ...chefCBs, ...equipmentCBs].forEach(cb => {
        cb.setAttribute('data-type', 
           cb.classList.contains('recipe-checkbox') ? 'recipe' :
           cb.classList.contains('chef-checkbox')   ? 'chef'   :
                                                      'equipment');
        cb.addEventListener('change', filterTable);
    });
    startInput.addEventListener('change', filterTable);
    endInput.addEventListener('change', filterTable);

    // initial run
    filterTable();
});
</script>
@endsection


