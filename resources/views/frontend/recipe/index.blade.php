{{-- resources/views/frontend/recipe/index.blade.php --}}
@extends('frontend.layouts.app')

@section('title', 'Tutte le ricette')

@section('content')
    <div class="container py-5">

        <!-- Header -->
        <div class="page-header d-flex align-items-center mb-4 p-4 rounded" style="background-color: #041930;">
            <i class="bi bi-bookmark-star-fill me-3 fs-3" style="color: #e2ae76;"></i>
            <div>
                <h4 class="mb-0 fw-bold" style="color: #e2ae76;">Tutte le ricette</h4>
                <small class="d-block text-light">Cerca, ordina e filtra rapidamente tutte le tue ricette qui sotto.</small>
            </div>
        </div>

        <!-- Card w/ Filter + Table -->
        <div class="card shadow-sm">
            <div class="card-body">

                <!-- Sell Mode Filter -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="sellModeFilter" class="form-label fw-semibold">Filtra per modalità di vendita</label>
                        <select id="sellModeFilter" class="form-select">
                            <option value="">Tutte</option>
                            <option value="piece">Pezzo</option>
                            <option value="kg">kg</option>
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="recipesTable" class="table table-striped table-hover table-bordered mb-0" style="width:100%;">
                        <thead class="custom-recipe-head">
                            <tr class="text-center">
                                <th>Nome</th>
                                <th>Modalità vendita</th>
                                <th class="text-end">Prezzo</th>
                                <th class="text-end">Costo ingr.</th>
                                <th class="text-end">Costo lavoro</th>
                                <th class="text-end">Costo totale</th>
                                <th class="text-end">Margine</th>
                                <th class="text-center">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($recipes as $r)
                            @php
                                // 1) determine unit selling price (gross)
                                $unitSell    = $r->sell_mode === 'piece'
                                               ? $r->selling_price_per_piece
                                               : $r->selling_price_per_kg;

                                // 2) compute net sale price (remove VAT)
                                $vatRate     = $r->vat_rate ?? 0;
                                $netSell     = $vatRate
                                               ? $unitSell / (1 + $vatRate/100)
                                               : $unitSell;

                                // 3) ingredient & labor batch costs
                                $batchIngCost = $r->batch_ing_cost;
                                $batchLabCost = $r->batch_labor_cost;
if ($r->sell_mode === 'piece') {
    $pcs          = ($r->total_pieces > 0) ? $r->total_pieces : 1;
    $unitIngCost  = $batchIngCost / $pcs;
    $unitLabCost  = $batchLabCost / $pcs;
} else {
    $wLoss        = $r->recipe_weight ?: $r->ingredients->sum(fn($i) => $i->quantity_g);
    $kg           = $wLoss > 0 ? $wLoss / 1000 : 1;
    $unitIngCost  = $batchIngCost / $kg;
    $unitLabCost  = $batchLabCost / $kg;
}


                                // 4) total expense per unit
                                $unitTotalCost = $r->total_expense;

                                // 5) margin & percentages (all ÷ netSell)
                                $unitMargin   = $r->potential_margin;
                                $marPct       = $r->potential_margin_pct;

                                $ingPct       = $netSell > 0
                                                ? round(($unitIngCost  * 100) / $netSell, 2)
                                                : 0;
                                $labPct       = $netSell > 0
                                                ? round(($unitLabCost  * 100) / $netSell, 2)
                                                : 0;
                                $totalPct     = $netSell > 0
                                                ? round(($unitTotalCost * 100) / $netSell, 2)
                                                : 0;

                                // 6) prepare child‐row data
                                $ingredientsData = $r->ingredients->map(fn($ri) => [
                                    'name'  => $ri->ingredient->ingredient_name,
                                    'qty_g' => $ri->quantity_g,
                                    'cost'  => round(($ri->quantity_g / 1000) * $ri->ingredient->price_per_kg, 2),
                                ]);
                            @endphp

                            <tr class="dt-control text-center"
                                data-sell-mode="{{ $r->sell_mode }}"
                                data-ingredients='@json($ingredientsData)'>
                                <td class="text-start">{{ $r->recipe_name }}</td>
                                <td>
                                    <span class="badge bg-secondary text-uppercase">
                                        {{ $r->sell_mode }}
                                    </span>
                                </td>
                                <td class="text-end" data-order="{{ $unitSell }}">
                                    <div class="d-flex flex-column align-items-end">
                                        <span>€{{ number_format($unitSell, 2) }}</span>
                                        <small class="text-muted">({{ number_format($netSell,2) }})</small>
                                    </div>
                                </td>
                                <td class="text-end" data-order="{{ $unitIngCost }}">
                                    <div class="d-flex flex-column align-items-end">
                                        <span>€{{ number_format($unitIngCost, 2) }}</span>
                                        <small class="text-muted">({{ $ingPct }}%)</small>
                                    </div>
                                </td>
                                <td class="text-end" data-order="{{ $unitLabCost }}">
                                    <div class="d-flex flex-column align-items-end">
                                        <span>€{{ number_format($unitLabCost, 2) }}</span>
                                        <small class="text-muted">({{ $labPct }}%)</small>
                                    </div>
                                </td>
                                <td class="text-end" data-order="{{ $unitTotalCost }}">
                                    <div class="d-flex flex-column align-items-end">
                                        <span>€{{ number_format($unitTotalCost, 2) }}</span>
                                        <small class="text-muted">({{ $totalPct }}%)</small>
                                    </div>
                                </td>
                                <td class="text-end" data-order="{{ $unitMargin }}">
                                    <div class="d-flex flex-column align-items-end">
                                        <span class="{{ $unitMargin >= 0 ? 'text-success' : 'text-danger' }}">
                                            €{{ number_format($unitMargin, 2) }}
                                        </span>
                                        <small class="text-muted">({{ $marPct }}%)</small>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('recipes.edit', $r) }}" class="btn btn-sm me-1"
                                       style="border:1px solid #e2ae76;color:#e2ae76;"
                                       onmouseover="this.style.backgroundColor='#e2ae76';this.style.color='#fff';"
                                       onmouseout="this.style.backgroundColor='transparent';this.style.color='#e2ae76';">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="{{ route('recipes.show', $r) }}" class="btn btn-sm me-1"
                                       style="border:1px solid #041930;color:#041930;"
                                       onmouseover="this.style.backgroundColor='#041930';this.style.color='#fff';"
                                       onmouseout="this.style.backgroundColor='transparent';this.style.color='#041930';">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <form action="{{ route('recipes.destroy', $r) }}" method="POST"
                                          class="d-inline" onsubmit="return confirm('Eliminare?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm"
                                                style="border:1px solid #ff0000;color:#ff0000;"
                                                onmouseover="this.style.backgroundColor='#ff0000';this.style.color='#fff';"
                                                onmouseout="this.style.backgroundColor='transparent';this.style.color='#ff0000';">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
@endsection

<style>
    table#recipesTable thead.custom-recipe-head th {
        background-color: #e2ae76 !important;
        color: #041930 !important;
        text-align: center;
    }
</style>

@section('scripts')
<script>
    $(function() {
        const table = $('#recipesTable').DataTable({
            paging: true,
            ordering: true,
            responsive: true,
            pageLength: 10,
            order: [[0, 'asc']],
            columnDefs: [{ orderable: false, targets: -1 }],
            language: {
                lengthMenu: "Mostra _MENU_ elementi per pagina",
                search:      "Cerca:",
                searchPlaceholder: "Cerca ricette..."
            }
        });

        // Sell‐mode filter
        $.fn.dataTable.ext.search.push((settings, data, dataIndex) => {
            if (settings.nTable.id !== 'recipesTable') return true;
            const selected = $('#sellModeFilter').val();
            const mode     = $(table.row(dataIndex).node()).data('sell-mode') || '';
            return selected === '' || mode === selected;
        });
        $('#sellModeFilter').on('change', () => table.draw());

        // Child‐row toggle
        $('#recipesTable tbody').on('click', 'tr.dt-control', function() {
            const tr          = $(this),
                  row         = table.row(tr),
                  ingredients = JSON.parse(tr.attr('data-ingredients'));

            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
            } else {
                let html = '<table class="table mb-0"><thead><tr>'
                         + '<th>Ingrediente</th><th class="text-end">Qtà (g)</th><th class="text-end">Costo</th>'
                         + '</tr></thead><tbody>';
                ingredients.forEach(i => {
                    html += `<tr>
                                <td>${i.name}</td>
                                <td class="text-end">${i.qty_g}</td>
                                <td class="text-end">€${i.cost.toFixed(2)}</td>
                             </tr>`;
                });
                html += '</tbody></table>';
                row.child(html).show();
                tr.addClass('shown');
            }
        });
    });
</script>
@endsection

