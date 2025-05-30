{{-- resources/views/frontend/showcase/create.blade.php --}}
@extends('frontend.layouts.app')

@section('title', $isEdit ? 'Modifica Vetrina Giornaliera' : 'Crea Vetrina Giornaliera')

@section('content')
    @php
        $maxItems      = 100;
        $oldItems      = old('items') ? (array) old('items') : [];
        $oldCount      = count($oldItems);
        $existingCount = $isEdit ? $showcase->recipes->count() : 0;
        $rowCount      = $oldCount
            ? min($maxItems, $oldCount)
            : ($isEdit ? $existingCount : 1);
    @endphp

    <div class="container py-5">
        <div class="card border-primary shadow-sm mb-4">
            <div class="card-header d-flex align-items-center" style="background-color: #041930;">
                <i class="bi bi-calendar-day fs-2 me-3" style="color: #e2ae76;"></i>
                <h4 class="mb-0 fw-bold" style="color: #e2ae76;">
                    {{ $isEdit ? 'Modifica Vetrina Giornaliera' : 'Crea Vetrina Giornaliera' }}
                </h4>
            </div>
            <div class="card-body">
                <form method="POST"
                      action="{{ $isEdit ? route('showcase.update', $showcase) : route('showcase.store') }}"
                      class="needs-validation" novalidate>
                    @csrf
                    @if($isEdit) @method('PUT') @endif

                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <label for="showcase_name" class="form-label fw-semibold" id="showcaseNameLabel">
                                Nome Vetrina
                            </label>
                            <input type="text" id="showcase_name" name="showcase_name"
                                   class="form-control form-control-lg"
                                   value="{{ old('showcase_name', $isEdit ? $showcase->showcase_name : '') }}"
                                   required>
                            <div class="invalid-feedback">Inserisci un nome per la vetrina.</div>
                        </div>

                        @unless($isEdit)
                            <div class="col-md-4">
                                <label for="template_select" class="form-label fw-semibold">
                                    Scegli Modello
                                </label>
                                <select id="template_select" name="template_id" class="form-select">
                                    <option value="">-- Seleziona Modello --</option>
                                    @foreach($templates as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endunless

                        <div class="col-md-2">
                            <label for="showcase_date" class="form-label fw-semibold">Seleziona Data</label>
                            <input type="date" id="showcase_date" name="showcase_date"
                                   class="form-control form-control-lg"
                                   value="{{ old('showcase_date', $isEdit ? $showcase->showcase_date->format('Y-m-d') : '') }}"
                                   required>
                            <div class="invalid-feedback">Seleziona una data.</div>
                        </div>

                        <div class="col-md-2">
                            <label for="template_action" class="form-label fw-semibold">Salva come</label>
                            <select id="template_action" name="template_action" class="form-select form-select-lg">
                                <option value="none"
                                    @selected(old('template_action', $isEdit ? $showcase->template_action : 'none')==='none')>
                                    Solo Salva
                                </option>
                                <option value="template"
                                    @selected(old('template_action', $isEdit ? $showcase->template_action : '')==='template')>
                                    Salva come Modello
                                </option>
                               
                            </select>
                        </div>
                    </div>

                    <div class="card border-secondary shadow-sm mb-4">
                        <div class="card-header" style="background-color: #041930;">
                            <strong style="color: #e2ae76;">Prodotti in Vetrina</strong>
                        </div>
                        <div class="card-body p-0">
                            <table class="table mb-0" id="showcaseTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Ricetta</th>
                                        <th>Prezzo</th>
                                        <th class="d-none">Costo ingr.</th>
                                        <th class="text-center">Qtà</th>
                                        <th class="text-center">Venduto</th>
                                        <th class="text-center">Riutilizzo</th>
                                        <th class="text-center">Scarti</th>
                                        <th>Potenziale</th>
                                        <th>Reale</th>
                                        <th class="text-center">Azione</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @for($i=0; $i<$rowCount; $i++)
                                        @php
                                            $item = $oldItems[$i]
                                                ?? ($isEdit && isset($showcase->recipes[$i])
                                                    ? $showcase->recipes[$i]
                                                    : null);
                                        @endphp
                                        <tr class="showcase-row">
                                            <td>
                                                <select name="items[{{ $i }}][recipe_id]"
                                                        class="form-select form-select-sm recipe-select"
                                                        style="min-width:250px;" required>
                                                    <option value="">Seleziona Ricetta</option>
                                                    @foreach($recipes as $rec)
                                                        <option value="{{ $rec->id }}"
                                                            data-price="{{ $rec->sell_mode==='kg'
                                                                ? $rec->selling_price_per_kg
                                                                : $rec->selling_price_per_piece }}"
                                                            data-batch-ing-cost="{{ $rec->batch_ing_cost }}"
                                                            data-total-pieces="{{ $rec->total_pieces }}"
                                                            data-recipe-weight="{{ $rec->recipe_weight }}"
                                                            data-ingredients-grams="{{ $rec->ingredients->sum(fn($ing)=>$ing->quantity_g) }}"
                                                            data-sell-mode="{{ $rec->sell_mode }}"
                                                            @selected(old("items.$i.recipe_id", $item->recipe_id??'') == $rec->id)>
                                                            {{ $rec->recipe_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <input type="text"
                                                           name="items[{{ $i }}][price]"
                                                           class="form-control form-control-sm text-end price-field"
                                                           readonly
                                                           value="{{ old("items.$i.price",$item->price??'') }}">
                                                    <span class="input-group-text unit-field"></span>
                                                </div>
                                            </td>
                                            <td class="d-none">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">€</span>
                                                    <input type="text"
                                                           class="form-control form-control-sm text-end unit-ing-field"
                                                           readonly value="0.00"
                                                           style="max-width:80px;">
                                                </div>
                                            </td>
                                            <td>
                                                <input type="number"
                                                       name="items[{{ $i }}][quantity]"
                                                       class="form-control form-control-sm text-center qty-field"
                                                       style="max-width:100px;"
                                                       value="{{ old("items.$i.quantity",$item->quantity??0) }}"
                                                       required >
                                            </td>
                                            <td>
                                                <input type="number"
                                                       name="items[{{ $i }}][sold]"
                                                       class="form-control form-control-sm text-center sold-field"
                                                       style="max-width:100px;"
                                                       value="{{ old("items.$i.sold",$item->sold??0) }}"
                                                       required>
                                            </td>
                                            <td>
                                                <input type="number"
                                                       name="items[{{ $i }}][reuse]"
                                                       class="form-control form-control-sm text-center reuse-field"
                                                       style="max-width:100px;"
                                                       value="{{ old("items.$i.reuse",$item->reuse??0) }}"
                                                       required>
                                            </td>
                                            <td>
                                                <input type="number"
                                                       name="items[{{ $i }}][waste]"
                                                       class="form-control form-control-sm text-center waste-field"
                                                       style="max-width:100px;"
                                                       value="{{ old("items.$i.waste",$item->waste??0) }}"
                                                       required>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">€</span>
                                                    <input type="text"
                                                           name="items[{{ $i }}][potential_income]"
                                                           class="form-control form-control-sm potential-field"
                                                           readonly
                                                           value="{{ old("items.$i.potential_income",$item->potential_income??'') }}">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">€</span>
                                                    <input type="text"
                                                           name="items[{{ $i }}][actual_revenue]"
                                                           class="form-control form-control-sm revenue-field"
                                                           readonly
                                                           value="{{ old("items.$i.actual_revenue",$item->actual_revenue??'') }}">
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <button type="button"
                                                        class="btn btn-outline-danger btn-sm remove-row">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                            <div class="p-3 border-top text-end">
                                <button type="button" id="addRowBtn" class="btn btn-gold-outline btn-sm"
                                        style="border:1px solid #e2ae76; color:#041930; background-color:transparent;"
                                        onmouseover="this.style.backgroundColor='#e2ae76'; this.style.color='#fff'; this.querySelector('i').style.color='#fff';"
                                        onmouseout="this.style.backgroundColor='transparent'; this.style.color='#041930'; this.querySelector('i').style.color='#041930';">
                                    <i class="bi bi-plus-circle me-1" style="color:#041930;"></i>
                                    Aggiungi Ricetta
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-end justify-content-between flex-wrap mb-4 gap-3">
                        <div class="d-flex flex-column">
                            <label class="form-label fw-semibold">Punto di pareggio giornaliero (€)</label>
                            <input type="number"
                                   id="break_even"
                                   name="break_even"
                                   class="form-control form-control-sm"
                                   style="width:140px;"
                                   value="{{ old('break_even',$isEdit ? $showcase->break_even : ($laborCost?->daily_bep ?? 0)) }}"
                                   readonly>
                        </div>
                        <div class="d-flex flex-column">
                            <label class="form-label fw-semibold">Potenziale Totale (€)</label>
                            <input type="text" id="totalPotential"
                                   class="form-control form-control-sm" style="width:140px;"
                                   readonly name="total_potential">
                        </div>
                        <div class="d-flex flex-column">
                            <label class="form-label fw-semibold">Ricavo Totale (€)</label>
                            <input type="text" id="totalRevenue" name="total_revenue"
                                   class="form-control form-control-sm" style="width:140px;"
                                   readonly>
                        </div>
                        <div class="d-flex flex-column">
                            <label class="form-label fw-semibold">Extra (€)</label>
                            <input type="text" id="plusAmount"
                                   class="form-control form-control-sm" style="width:140px;"
                                   readonly name="plus">
                        </div>
                        <div class="d-flex flex-column">
                            <label class="form-label fw-semibold">Margine Reale (€)</label>
                            <input type="text" id="realMargin" name="real_margin"
                                   class="form-control form-control-sm" style="width:140px;"
                                   readonly>
                        </div>
                        <div class="d-flex align-items-end">
                            <button type="submit" class="btn btn-gold-filled px-5 py-3"
                                    style="background-color:#e2ae76; color:#041930;">
                                <i class="bi bi-save2 me-1" style="color:#041930;"></i>
                                {{ $isEdit ? 'Aggiorna Vetrina' : 'Salva Vetrina' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const templateSelect     = document.getElementById('template_select');
    const actionSelect       = document.getElementById('template_action');
    const showcaseNameInput  = document.getElementById('showcase_name');
    const showcaseNameLabel  = document.getElementById('showcaseNameLabel');
    const showcaseDateInput  = document.getElementById('showcase_date');
    const breakEvenInput     = document.getElementById('break_even');
    const tbody              = document.querySelector('#showcaseTable tbody');
    const addBtn             = document.getElementById('addRowBtn');
    const totalPotential     = document.getElementById('totalPotential');
    const totalRevenue       = document.getElementById('totalRevenue');
    const plusAmount         = document.getElementById('plusAmount');
    const realMargin         = document.getElementById('realMargin');

    let rowIndex = tbody.querySelectorAll('.showcase-row').length;
    const baseRow = tbody.querySelector('.showcase-row').cloneNode(true);

    // Cambia etichetta in base al tipo di salvataggio
    function toggleNameRequirement() {
        const isTemplate = ['template', 'both'].includes(actionSelect.value);
        showcaseNameLabel.textContent = isTemplate ? 'Nome Modello' : 'Nome Vetrina';
        showcaseNameInput.required = isTemplate;
    }
    toggleNameRequirement();
    actionSelect.addEventListener('change', toggleNameRequirement);

    // Generatore di riga vuota
    function blankRow() {
        const clone = baseRow.cloneNode(true);
        clone.querySelectorAll('input,select').forEach(el => {
            if (el.tagName === 'SELECT') el.selectedIndex = 0;
            else if (el.type === 'number') el.value = 0;
            else el.value = '';
        });
        return clone;
    }

    // Collega eventi alla riga
    function wireRowEvents(row) {
        row.querySelector('.recipe-select')?.addEventListener('change', () => recalcRow(row));
        row.querySelector('.qty-field')?.addEventListener('input', () => recalcRow(row));
        row.querySelector('.sold-field')?.addEventListener('input', () => recalcRow(row));
        row.querySelector('.reuse-field')?.addEventListener('input', () => recalcRow(row));
        row.querySelector('.waste-field')?.addEventListener('input', () => recalcRow(row));

        row.querySelector('.remove-row')?.addEventListener('click', () => {
            if (tbody.querySelectorAll('.showcase-row').length > 1) {
                row.remove();
                recalcSummary();
            }
        });

        recalcRow(row);
    }

    // Ricalcola valori di una riga
    function recalcRow(row) {
        const opt      = row.querySelector('.recipe-select')?.selectedOptions[0];
        const price    = parseFloat(opt?.dataset.price || 0) || 0;
        const mode     = opt?.dataset.sellMode || 'piece';
        const qty      = parseFloat(row.querySelector('.qty-field')?.value || 0);
        const sold     = parseFloat(row.querySelector('.sold-field')?.value || 0);
        const batch    = parseFloat(opt?.dataset.batchIngCost || 0);
        const weight   = parseFloat(opt?.dataset.recipeWeight || 0);
        const grams    = parseFloat(opt?.dataset.ingredientsGrams || 0);
        const totalPcs = parseFloat(opt?.dataset.totalPieces || 1);
        const unitCost = mode === 'piece'
            ? (batch / totalPcs)
            : (batch / ((weight || grams) / 1000));

        row.querySelector('.price-field').value       = price.toFixed(2);
        row.querySelector('.unit-field').textContent  = mode === 'kg' ? '€/kg' : '€/pz';
        row.querySelector('.unit-ing-field').value    = unitCost.toFixed(2);
        row.querySelector('.potential-field').value   = (price * qty).toFixed(2);
        row.querySelector('.revenue-field').value     = (price * sold).toFixed(2);

        recalcSummary();
    }

    // Ricalcola tutti i totali
    function recalcSummary() {
        let pot = 0, rev = 0, ingSold = 0, ingWaste = 0;
        const bep = parseFloat(breakEvenInput.value) || 0;

        tbody.querySelectorAll('.showcase-row').forEach(r => {
            const price    = parseFloat(r.querySelector('.price-field').value || 0);
            const qty      = parseFloat(r.querySelector('.qty-field').value || 0);
            const sold     = parseFloat(r.querySelector('.sold-field').value || 0);
            const waste    = parseFloat(r.querySelector('.waste-field').value || 0);
            const unitCost = parseFloat(r.querySelector('.unit-ing-field').value || 0);

            pot      += price * qty;
            rev      += price * sold;
            ingSold  += unitCost * sold;
            ingWaste += unitCost * waste;
        });

        const plus      = rev - bep;
        const pct       = rev > 0 ? ((ingSold + ingWaste) / rev) * 100 : 0;
        const rawMargin = plus - (plus * pct / 100);
        const finalMrg  = plus < 0 ? 0 : rawMargin;

        totalPotential.value = pot.toFixed(2);
        totalRevenue.value   = rev.toFixed(2);
        plusAmount.value     = plus.toFixed(2);
        plusAmount.style.color = plus < 0 ? 'red' : 'green';
        realMargin.value     = finalMrg.toFixed(2);
    }

    // Aggiungi nuova riga
    addBtn.addEventListener('click', () => {
        const newRow = blankRow();
        newRow.querySelectorAll('input[name],select[name]').forEach(el => {
            el.name = el.name.replace(/\[\d+\]/, `[${rowIndex}]`);
        });
        tbody.appendChild(newRow);
        wireRowEvents(newRow);
        rowIndex++;
    });

    // Carica template
    templateSelect?.addEventListener('change', function () {
        const id = this.value;
        if (!id) return;

        fetch(`/showcase/getTemplate/${id}`)
            .then(r => r.json())
            .then(data => {
                showcaseNameInput.value = data.showcase_name;
                showcaseDateInput.value = data.showcase_date;
                actionSelect.value      = data.template_action;
                toggleNameRequirement();

                tbody.innerHTML = '';
                rowIndex = 0;

                data.details.forEach(detail => {
                    const r = blankRow();
                    r.querySelectorAll('input[name],select[name]').forEach(el => {
                        el.name = el.name.replace(/\[\d+\]/, `[${rowIndex}]`);
                    });
                    r.querySelector('.recipe-select').value = detail.recipe_id;
                    r.querySelector('.qty-field').value      = detail.quantity;
                    r.querySelector('.sold-field').value     = detail.sold;
                    r.querySelector('.reuse-field').value    = detail.reuse;
                    r.querySelector('.waste-field').value    = detail.waste;
                    tbody.appendChild(r);
                    wireRowEvents(r);
                    rowIndex++;
                });
            })
            .catch(console.error);
    });

    // Bind iniziale per le righe già presenti
    tbody.querySelectorAll('.showcase-row').forEach(r => {
        wireRowEvents(r);
    });
});
</script>
@endsection




