@extends('frontend.layouts.app')

@section('title', 'Crea Ricetta')

@php
    $sectionHeaderStyle = 'style="background-color: #041930; color: #e2ae76;"';
@endphp
@section('content')


    <style>
        .incidence-col {
            display: none !important;
        }
    </style>
    <div class="container py-5">


             {{-- Validation Errors --}}
       
        @php
            $isEdit = isset($recipe);
            $formAction = $isEdit ? route('recipes.update', $recipe->id) : route('recipes.store');
        @endphp

        <form method="POST" action="{{ $formAction }}">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

<div class="card mb-4 border-primary shadow-sm">
    <div class="card-header d-flex align-items-center" style="background-color: #041930;">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
             style="width: 1.5em; height: 1.5em; fill: #e2ae76; margin-right: .5em;" aria-hidden="true"
             focusable="false">
            <path d="M356.334,494.134c43.124-12.321,153.745-52.878,155.636-110.035c1.88-57.184-85.049-58.301-139.549-49.294L356.334,494.134z" />
            <path d="M17.864,155.664l159.328-16.088c9.01-54.497,7.893-141.426-49.291-139.546C70.742,1.918,30.184,112.54,17.864,155.664z" />
            <path d="M182.525,479.291c17.563,9.501,39.263,18.014,58.835,23.244c44.236,11.799,107.683,14.791,113.83-4.066c6.165-18.838,22.757-161.567,15.537-175.497c-7.204-13.913-32.605-22.372-47.628-26.378c-5.971-1.59-13.743-3.393-21.822-4.467L182.525,479.291z" />
            <path d="M9.466,270.641c5.227,19.569,13.741,41.27,23.244,58.835l187.165-118.752c-1.076-8.081-2.879-15.851-4.47-21.824c-4.015-15.03-12.462-40.422-26.375-47.626c-13.93-7.219-156.661,9.37-175.497,15.537C-5.325,162.957-2.332,226.404,9.466,270.641z" />
            <path d="M277.509,234.492c-10.833-10.833-30.659-28.329-46.765-27.786C214.616,207.227,48.711,314.21,34.496,328.424c-14.223,14.223,18.794,66.572,50.651,98.429c31.855,31.855,84.205,64.874,98.429,50.651c14.215-14.215,121.194-180.123,121.717-196.251C305.836,265.147,288.341,245.322,277.509,234.492z" />
        </svg>
        <h5 class="mb-0" style="color: #e2ae76;">Dettagli Ricetta</h5>
    </div>

    <div class="card-body">
        <div class="row g-3">

            <div class="col-md-4">
                <label for="recipeName" class="form-label fw-semibold">Nome</label>
                <input
                    type="text"
                    id="recipeName"
                    name="recipe_name"
                    class="form-control @error('recipe_name') is-invalid @enderror"
                    placeholder="Torta al cioccolato"
                    value="{{ old('recipe_name', $isEdit ? $recipe->recipe_name : '') }}"
                    >
                @error('recipe_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label for="recipeCategory" class="form-label fw-semibold">Categoria</label>
                <select
                    id="recipeCategory"
                    name="recipe_category_id"
                    class="form-select @error('recipe_category_id') is-invalid @enderror"
                    >
                    <option value="">Scegli…</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}"
                            {{ old('recipe_category_id', $isEdit ? $recipe->recipe_category_id : '') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                @error('recipe_category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label for="recipeDept" class="form-label fw-semibold">Reparto</label>
                <select
                    id="recipeDept"
                    name="department_id"
                    class="form-select @error('department_id') is-invalid @enderror"
                    >
                    <option value="">Scegli…</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}"
                            {{ old('department_id', $isEdit ? $recipe->department_id : '') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
                
            </div>

        </div>
    </div>
</div>





<div class="card mb-4 border-info shadow-sm">
    <div class="card-header d-flex align-items-center" style="background-color: #041930;">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
             style="width: 1.5em; height: 1.5em; fill: #e2ae76; margin-right: .5em;" aria-hidden="true" focusable="false">
            <path d="M479.605,91.769c-23.376,23.376-66.058,33.092-79.268,19.882c-13.21-13.21-3.494-55.892,19.883-79.268s85.999-26.614,85.999-26.614S502.982,68.393,479.605,91.769z"/>
            <path d="M506.218,5.785L400.345,111.658c13.218,13.2,55.888,3.483,79.26-19.889C502.864,68.511,506.186,6.411,506.218,5.785z"/>
            <path d="M432.367,200.156c-33.059,0-70.11-23.311-70.11-41.992s37.052-41.992,70.11-41.992s79.629,41.992,79.629,41.992S465.426,200.156,432.367,200.156z"/>
            <path d="M311.84,79.629c0,33.059,23.311,70.11,41.992,70.11s41.992-37.052,41.992-70.11S353.832,0,353.832,0S311.84,46.571,311.84,79.629z"/>
            <path d="M367.516,265.006c-33.059,0-70.11-23.311-70.11-41.992s37.052-41.992,70.11-41.992s79.629,41.992,79.629,41.992S400.575,265.006,367.516,265.006z"/>
            <path d="M246.99,144.48c0,33.059,23.311,70.11,41.992,70.11c18.681,0,41.992-37.052,41.992-70.11S288.982,64.85,288.982,64.85S246.99,111.421,246.99,144.48z"/>
            <path d="M302.666,329.857c-33.059,0-70.11-23.311-70.11-41.992c0-18.681,37.052-41.992,70.11-41.992s79.629,41.992,79.629,41.992S335.726,329.857,302.666,329.857z"/>
            <path d="M182.14,209.33c0,33.059,23.311,70.11,41.992,70.11s41.992-37.052,41.992-70.11s-41.992-79.629-41.992-79.629S182.14,176.27,182.14,209.33z"/>
            <path d="M237.025,395.498c-33.059,0-70.11-23.311-70.11-41.992c0-18.681,37.052-41.992,70.11-41.992s79.629,41.992,79.629,41.992S270.085,395.498,237.025,395.498z"/>
            <path d="M116.498,274.97c0,33.059,23.31,70.11,41.992,70.11s41.992-37.052,41.992-70.11s-41.992-79.629-41.992-79.629S116.498,241.912,116.498,274.97z"/>
            <path d="M170.438,462.084c-33.059,0-70.11-23.311-70.11-41.992c0-18.681,37.052-41.992,70.11-41.992s79.629,41.992,79.629,41.992S203.497,462.084,170.438,462.084z"/>
            <path d="M49.912,341.558c0,33.059,23.31,70.11,41.992,70.11s41.992-37.052,41.992-70.11s-41.992-79.629-41.992-79.629S49.912,308.499,49.912,341.558z"/>
            <path d="M4.917,507.087c-6.552-6.552-6.552-17.174,0-23.725L404.75,83.527c6.552-6.552,17.174-6.552,23.725,0c6.552,6.552,6.552,17.174,0,23.725L28.643,507.087C22.091,513.637,11.468,513.637,4.917,507.087z"/>
        </svg>
        <h5 class="mb-0" style="color: #e2ae76;">Ingredienti</h5>
        <button type="button" class="btn btn-outline-light ms-auto" data-bs-toggle="modal" data-bs-target="#addIngredientModal">
            <i class="bi bi-plus-lg"></i> Nuovo Ingrediente
        </button>
    </div>

    <style>
        .incidence-col {
            display: none !important;
        }
    </style>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Ingrediente</th>
                        <th class="text-center">Qtà&nbsp;(g)</th>
                        <th class="text-center">Costo&nbsp;(€)</th>
                        <th class="text-center incidence-col">Incidenza&nbsp;(%)</th>
                        <th class="text-center">Azione</th>
                    </tr>
                </thead>
               <tbody id="ingredientsTable">
    @php
        $oldRows = old('ingredients', []);
    @endphp

    @if (count($oldRows) > 0)
        {{-- Repopulate from old input --}}
        @foreach($oldRows as $i => $old)
            <tr class="ingredient-row">
                <td>
                    <select
                        name="ingredients[{{ $i }}][id]"
                        class="form-select ingredient-select @error('ingredients.'.$i.'.id') is-invalid @enderror"
                        
                    >
                        <option value="">Seleziona ingrediente…</option>
                        @foreach($ingredients as $ing)
                            <option
                                value="{{ $ing->id }}"
                                data-price="{{ $ing->price_per_kg }}"
                                {{ (int)($old['id'] ?? '') === $ing->id ? 'selected' : '' }}
                            >
                                {{ $ing->ingredient_name }} (€{{ $ing->price_per_kg }}/kg)
                            </option>
                        @endforeach
                    </select>
                    @error('ingredients.'.$i.'.id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </td>
                <td>
                    <input
                        type="number"
                        step="0.01"
                        name="ingredients[{{ $i }}][quantity]"
                        class="form-control text-center ingredient-quantity @error('ingredients.'.$i.'.quantity') is-invalid @enderror"
                        value="{{ $old['quantity'] ?? '' }}"
                        
                    >
                    @error('ingredients.'.$i.'.quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </td>
                <td>
                    <input
                        type="text"
                        name="ingredients[{{ $i }}][cost]"
                        class="form-control text-center ingredient-cost"
                        readonly
                        value="{{ $old['cost'] ?? '' }}"
                    >
                </td>
                <td class="incidence-col">
                    <input type="text" class="form-control text-center ingredient-incidence" readonly>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-ingredient">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        @endforeach

    @elseif($isEdit && $recipe->ingredients->isNotEmpty())
        {{-- Existing recipe ingredients on edit --}}
        @foreach($recipe->ingredients as $i => $line)
            <tr class="ingredient-row">
                <td>
                    <select
                        name="ingredients[{{ $i }}][id]"
                        class="form-select ingredient-select @error('ingredients.'.$i.'.id') is-invalid @enderror"
                        
                    >
                        <option value="">Seleziona ingrediente…</option>
                        @foreach($ingredients as $ing)
                            <option
                                value="{{ $ing->id }}"
                                data-price="{{ $ing->price_per_kg }}"
                                {{ $ing->id === $line->ingredient_id ? 'selected' : '' }}
                            >
                                {{ $ing->ingredient_name }} (€{{ $ing->price_per_kg }}/kg)
                            </option>
                        @endforeach
                    </select>
                    @error('ingredients.'.$i.'.id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </td>
                <td>
                    <input
                        type="number"
                        step="0.01"
                        name="ingredients[{{ $i }}][quantity]"
                        class="form-control text-center ingredient-quantity @error('ingredients.'.$i.'.quantity') is-invalid @enderror"
                        value="{{ old('ingredients.'.$i.'.quantity', $line->quantity_g) }}"
                        
                    >
                    @error('ingredients.'.$i.'.quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </td>
                <td>
                    <input
                        type="text"
                        name="ingredients[{{ $i }}][cost]"
                        class="form-control text-center ingredient-cost"
                        readonly
                        value="{{ old('ingredients.'.$i.'.cost', $line->cost) }}"
                    >
                </td>
                <td class="incidence-col">
                    <input type="text" class="form-control text-center ingredient-incidence" readonly>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-ingredient">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        @endforeach

    @else
        {{-- First empty row on create --}}
        <tr class="ingredient-row">
            <td>
                <select
                    name="ingredients[0][id]"
                    class="form-select ingredient-select @error('ingredients.0.id') is-invalid @enderror"
                   
                >
                    <option value="">Seleziona ingrediente…</option>
                    @foreach($ingredients as $ing)
                        <option value="{{ $ing->id }}" data-price="{{ $ing->price_per_kg }}">
                            {{ $ing->ingredient_name }} (€{{ $ing->price_per_kg }}/kg)
                        </option>
                    @endforeach
                </select>
                @error('ingredients.0.id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </td>
            <td>
                <input
                    type="number"
                    step="0.01"
                    name="ingredients[0][quantity]"
                    class="form-control text-center ingredient-quantity @error('ingredients.0.quantity') is-invalid @enderror"
                   
                >
                @error('ingredients.0.quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </td>
            <td>
                <input type="text" name="ingredients[0][cost]" class="form-control text-center ingredient-cost" readonly>
            </td>
            <td class="incidence-col">
                <input type="text" class="form-control text-center ingredient-incidence" readonly>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-outline-danger btn-sm remove-ingredient">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
    @endif
</tbody>

                <tfoot class="table-light">
                    <tr>
                        <td class="fw-semibold">Peso Totale (g)</td>
                        <td>
                            <input type="number" id="totalWeightFooter" class="form-control text-center" readonly>
                            <input type="hidden" name="ingredients_total_weight" id="ingredientsTotalWeightHidden">
                        </td>
                        <td>
                            <input type="text" id="totalCostFooter" name="ingredients_total_cost" class="form-control text-center" readonly>
                        </td>
                        <td class="incidence-col">
                            <input type="text" id="totalIncidenceFooter" class="form-control text-center fw-bold" readonly>
                        </td>
                        <td class="text-center">
                            <button id="addIngredientBtn" class="btn btn-outline-success btn-sm">
                                <i class="bi bi-plus"></i> Aggiungi
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Peso dopo il calo (g)</td>
                        <td>
                            <input type="number" id="weightWithLoss" name="recipe_weight"
                                   class="form-control text-center @error('recipe_weight') is-invalid @enderror"
                                   value="{{ old('recipe_weight', $isEdit ? $recipe->recipe_weight : 0) }}">
                            @error('recipe_weight')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>









<div class="card mb-4 border-warning shadow-sm">
    {{-- pass the selected labor cost record ID --}}
    <input type="hidden" name="labor_cost_id" value="{{ optional($laborCost)->id }}">

    {{-- preserve your existing shop/external rates for JS --}}
    <input type="hidden" id="shopRate" value="{{ optional($laborCost)->shop_cost_per_min ?? 0 }}">
    <input type="hidden" id="externalRate" value="{{ optional($laborCost)->external_cost_per_min ?? 0 }}">

    <div class="card-header d-flex align-items-center" style="background-color: #041930;">
        <i class="bi bi-clock-history fs-4 me-2" style="color: #e2ae76;"></i>
        <h5 class="mb-0" style="color: #e2ae76;">Manodopera</h5>
    </div>

    <div class="card-body">
        <div class="form-check form-check-inline @error('labor_cost_mode') is-invalid @enderror">
            <input class="form-check-input" type="radio" name="labor_cost_mode" id="costModeShop"
                   value="shop"
                   {{ old('labor_cost_mode', $isEdit ? $recipe->labor_cost_mode : 'shop') == 'shop' ? 'checked' : '' }}>
            <label class="form-check-label" for="costModeShop">Usa costo interno (€/min)</label>
        </div>
        <div class="form-check form-check-inline @error('labor_cost_mode') is-invalid @enderror">
            <input class="form-check-input" type="radio" name="labor_cost_mode" id="costModeExternal"
                   value="external"
                   {{ old('labor_cost_mode', $isEdit ? $recipe->labor_cost_mode : 'shop') == 'external' ? 'checked' : '' }}>
            <label class="form-check-label" for="costModeExternal">Usa costo esterno (€/min)</label>
        </div>
        @error('labor_cost_mode')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror

        <div class="row g-3 mt-3">
            <div class="col-md-3">
                <label for="laborTimeInput" class="form-label fw-semibold">Tempo lavoro (min)</label>
                <input type="number" id="laborTimeInput"
                       name="labor_time_input"
                       class="form-control @error('labor_time_input') is-invalid @enderror"
                       min="0"
                       value="{{ old('labor_time_input', $isEdit ? $recipe->labour_time_min : 0) }}">
                @error('labor_time_input')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-3">
                <label for="costPerMin" class="form-label fw-semibold">Costo al minuto (€)</label>
                <div class="input-group">
                    <span class="input-group-text">€</span>
                    <input type="text" id="costPerMin" class="form-control" readonly>
                </div>
            </div>

            <div class="col-md-3">
                <label for="laborCost" class="form-label fw-semibold">Costo lavoro (€)</label>
                <div class="input-group">
                    <span class="input-group-text">€</span>
                    <input type="text"
                           id="laborCost"
                           name="labor_cost"
                           class="form-control @error('labor_cost') is-invalid @enderror"
                           readonly
                           value="{{ old('labor_cost', $isEdit ? $recipe->labour_cost : '') }}">
                </div>
                @error('labor_cost')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- incidence stays hidden --}}
            <div class="col-md-3" style="display: none">
                <label for="laborIncidence" class="form-label fw-semibold">Incidenza (%)</label>
                <input type="text" id="laborIncidence" class="form-control text-center" readonly>
            </div>
        </div>
    </div>
</div>






<div class="row gx-4 mb-4">
    <div class="col-md-6">
        <div class="card border-success shadow-sm h-100">
            <div class="card-header d-flex align-items-center" style="background-color: #041930;">
                <i class="bi bi-calculator fs-4 me-2" style="color: #e2ae76;"></i>
                <h5 class="mb-0" style="color: #e2ae76;">Spesa Totale</h5>
            </div>

            <div class="card-body d-flex flex-column align-items-center">
                <div class="input-group w-75 mb-3">
                    <span class="input-group-text">Costo €/kg prima Imballaggio</span>
                    <span class="input-group-text">€</span>
                    <input 
                        type="text" 
                        id="prodCostKg" 
                        name="production_cost_per_kg"
                        class="form-control text-end @error('production_cost_per_kg') is-invalid @enderror" 
                        readonly
                        value="{{ old('production_cost_per_kg', $isEdit ? $recipe->production_cost_per_kg : '') }}">
                    @error('production_cost_per_kg')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="input-group w-75 mb-3">
                    <span class="input-group-text">Imballaggio</span>
                    <span class="input-group-text">€</span>
                    <input 
                        type="number" 
                        step="0.01" 
                        id="packingCost" 
                        name="packing_cost"
                        class="form-control text-end @error('packing_cost') is-invalid @enderror"
                        value="{{ old('packing_cost', $isEdit ? $recipe->packing_cost : 0) }}">
                    @error('packing_cost')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="input-group input-group-lg w-75 mb-3">
                    <span class="input-group-text">Costo €/kg dopo Imballaggio</span>
                    <span class="input-group-text">€</span>
                    <input 
                        type="text" 
                        id="totalExpense" 
                        name="total_expense"
                        class="form-control fw-bold text-center @error('total_expense') is-invalid @enderror" 
                        readonly
                        value="{{ old('total_expense', $isEdit ? $recipe->total_expense : '') }}">
                    @error('total_expense')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="w-75 text-center">
                    <span class="fw-semibold">Margine Potenziale:</span>
                    <span id="potentialMargin" class="fw-bold ms-2">{{ old('potential_margin', $isEdit ? $recipe->potential_margin : '') }}</span>
                    <input type="hidden" name="potential_margin" id="potentialMarginInput" value="{{ old('potential_margin', $isEdit ? $recipe->potential_margin : '') }}">
                    <input type="hidden" name="potential_margin_pct" id="potentialMarginPctInput" value="{{ old('potential_margin_pct', $isEdit ? $recipe->potential_margin_pct : '') }}">
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-secondary shadow-sm h-100">
            <div class="card-header d-flex align-items-center" style="background-color: #041930;">
                <i class="bi bi-shop fs-4 me-2" style="color: #e2ae76;"></i>
                <h5 class="mb-0" style="color: #e2ae76;">Modalità di Vendita</h5>
            </div>

            <div class="card-body">
                <div class="mb-3 @error('sell_mode') is-invalid @enderror">
                    <div class="form-check form-check-inline">
                        <input 
                            class="form-check-input" 
                            type="radio" 
                            name="sell_mode" 
                            id="modePiece"
                            value="piece"
                            {{ old('sell_mode', $isEdit ? $recipe->sell_mode : 'piece') == 'piece' ? 'checked' : '' }}>
                        <label class="form-check-label" for="modePiece">Vendita a Pezzo</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input 
                            class="form-check-input" 
                            type="radio" 
                            name="sell_mode" 
                            id="modeKg"
                            value="kg"
                            {{ old('sell_mode', $isEdit ? $recipe->sell_mode : '') == 'kg' ? 'checked' : '' }}>
                        <label class="form-check-label" for="modeKg">Vendita al Kg</label>
                    </div>
                    @error('sell_mode')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div id="pieceInputs">
                    <div class="mb-3">
                        <label for="totalPieces" class="form-label fw-semibold">pezzi/dose</label>
                        <input 
                            type="number" 
                            id="totalPieces" 
                            name="total_pieces" 
                            class="form-control @error('total_pieces') is-invalid @enderror"
                            value="{{ old('total_pieces', $isEdit ? $recipe->total_pieces : '') }}">
                        @error('total_pieces')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="weightPerPiece" class="form-label fw-semibold">Peso per Pezzo (g)</label>
                        <input 
                            type="text" 
                            id="weightPerPiece" 
                            class="form-control" 
                            readonly
                            value="{{ old('weight_per_piece', $isEdit && $recipe->total_pieces > 0 ? number_format(1000 / $recipe->total_pieces, 2) : '') }}">
                    </div>
                    <div class="mb-3">
                        <label for="pricePerPiece" class="form-label fw-semibold">Prezzo di Vendita per Pezzo (€)</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input 
                                type="number" 
                                step="0.01" 
                                id="pricePerPiece"
                                name="selling_price_per_piece" 
                                class="form-control @error('selling_price_per_piece') is-invalid @enderror"
                                value="{{ old('selling_price_per_piece', $isEdit ? $recipe->selling_price_per_piece : '') }}">
                        </div>
                        @error('selling_price_per_piece')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div id="kgInputs" class="d-none">
                    <div class="mb-3">
                        <label for="pricePerKg" class="form-label fw-semibold">Prezzo di Vendita per Kg (€)</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input 
                                type="number" 
                                step="0.01" 
                                id="pricePerKg" 
                                name="selling_price_per_kg"
                                class="form-control @error('selling_price_per_kg') is-invalid @enderror"
                                value="{{ old('selling_price_per_kg', $isEdit ? $recipe->selling_price_per_kg : '') }}">
                        </div>
                        @error('selling_price_per_kg')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label for="vatRate" class="form-label fw-semibold">Aliquota IVA</label>
                        @php
                            $currentVat = old('vat_rate', $isEdit ? $recipe->vat_rate : 0);
                        @endphp
                        <select 
                            id="vatRate" 
                            name="vat_rate" 
                            class="form-select @error('vat_rate') is-invalid @enderror">
                            <option value="0" @selected($currentVat == 0)>Esente IVA</option>
                            <option value="4" @selected($currentVat == 4)>4%</option>
                            <option value="10" @selected($currentVat == 10)>10%</option>
                            <option value="22" @selected($currentVat == 22)>22%</option>
                        </select>
                        @error('vat_rate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>










            {{-- only show on Create, not on Edit --}}
@if (! $isEdit)
    <div class="card mb-4 border-info shadow-sm">
        <div class="card-header d-flex align-items-center" style="background-color: #041930;">
            <i class="bi bi-plus-circle fs-4 me-2" style="color: #e2ae76;"></i>
            <h5 class="mb-0" style="color: #e2ae76;">Aggiunte</h5>
        </div>

        <div class="card-body">
            <div class="form-check">
                <input 
                    class="form-check-input @error('add_as_ingredient') is-invalid @enderror" 
                    type="checkbox" 
                    id="addAsIngredient"
                    name="add_as_ingredient" 
                    value="1"
                    {{ old('add_as_ingredient', 0) ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold" for="addAsIngredient">
                    Aggiungi questa ricetta come <em>ingrediente</em>
                </label>
                @error('add_as_ingredient')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <p class="small text-muted mb-0">
                    Se selezionato, il nome della ricetta verrà salvato nella tabella degli ingredienti 
                    con un costo €/kg pari al "Costo €/kg prima Imballaggio".
                </p>
            </div>
        </div>
    </div>
@endif










<div class="text-end">
    <button type="submit" class="btn btn-lg submit_btn"
            style="background-color: #e2ae76; color: #041930; border: 2px solid #e2ae76;">
        <i class="bi bi-save2 me-2"></i>
        {{ $isEdit ? 'Aggiorna Ricetta' : 'Salva Ricetta' }}
    </button>
</div>

        </form>
    </div>






<div class="modal fade" id="addIngredientModal" tabindex="-1" aria-labelledby="addIngredientModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addIngredientModalLabel">Aggiungi Nuovo Ingrediente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
      </div>
      <div class="modal-body">
        <form id="addIngredientForm" action="{{ route('ingredients.store') }}" method="POST">
          @csrf
          <div class="mb-3">
            <label for="ingredientNameModal" class="form-label">Nome Ingrediente</label>
            <input
              type="text"
              id="ingredientNameModal"
              name="ingredient_name"
              class="form-control @error('ingredient_name') is-invalid @enderror"
              value="{{ old('ingredient_name') }}"
              >
            @error('ingredient_name')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label for="pricePerKgModal" class="form-label">Prezzo per kg (€)</label>
            <input
              type="number"
              id="pricePerKgModal"
              name="price_per_kg"
              class="form-control @error('price_per_kg') is-invalid @enderror"
              step="0.01"
              value="{{ old('price_per_kg') }}"
              >
            @error('price_per_kg')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <button type="submit" class="btn btn-primary">Salva Ingrediente</button>
        </form>
      </div>
    </div>
  </div>
</div>








@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const addForm            = document.getElementById('addIngredientForm');
            const modalEl            = document.getElementById('addIngredientModal');
            const vatRateEl          = document.getElementById('vatRate');
            const shopRateEl         = document.getElementById('shopRate');
            const externalRateEl     = document.getElementById('externalRate');
            const costModeShop       = document.getElementById('costModeShop');
            const costModeExternal   = document.getElementById('costModeExternal');
            const laborTimeInput     = document.getElementById('laborTimeInput');
            const costPerMinIn       = document.getElementById('costPerMin');
            const laborCostIn        = document.getElementById('laborCost');
            const laborIncidenceIn   = document.getElementById('laborIncidence');
            const pricePerPiece      = document.getElementById('pricePerPiece');
            const pricePerKg         = document.getElementById('pricePerKg');
            const modePiece          = document.getElementById('modePiece');
            const modeKg             = document.getElementById('modeKg');
            const totalPiecesIn      = document.getElementById('totalPieces');
            const weightPerPieceIn   = document.getElementById('weightPerPiece');
            const weightWithLossIn   = document.getElementById('weightWithLoss');
            const tableBody          = document.getElementById('ingredientsTable');
            const totalWeightFt      = document.getElementById('totalWeightFooter');
            const hiddenTotalWt      = document.getElementById('ingredientsTotalWeightHidden');
            const totalCostIn        = document.getElementById('totalCostFooter');
            const totalIncidenceIn   = document.getElementById('totalIncidenceFooter');
            const packingCostIn      = document.getElementById('packingCost');
            const prodCostKgIn       = document.getElementById('prodCostKg');
            const totalExpenseIn     = document.getElementById('totalExpense');
            const potentialMargin    = document.getElementById('potentialMargin');
            const potentialInput     = document.getElementById('potentialMarginInput');
            const potentialPctInput  = document.getElementById('potentialMarginPctInput');

            let weightEdited = false;
            let idx = {{ isset($recipe) ? $recipe->ingredients->count() : 1 }};

            // 1) AJAX “Add Ingredient”
            addForm.addEventListener('submit', async e => {
                e.preventDefault();
                const fd = new FormData(addForm);
                try {
                    const res = await fetch(addForm.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: fd
                    });
                    const json = await res.json();
                    if (!res.ok) {
                        alert(json.errors ? Object.values(json.errors).flat().join('\n') :
                              'Impossibile salvare l\'ingrediente.');
                        return;
                    }
                    const opt = document.createElement('option');
                    opt.value = json.id;
                    opt.textContent = `${json.ingredient_name} (€${json.price_per_kg}/kg)`;
                    opt.dataset.price = json.price_per_kg;
                    document.querySelectorAll('.ingredient-select').forEach(sel => {
                        const prev = sel.value;
                        const clone = opt.cloneNode(true);
                        let inserted = false;
                        const newName = json.ingredient_name.toLowerCase().trim();
                        for (let i = 1; i < sel.options.length; i++) {
                            const existingName = sel.options[i].textContent.split('(')[0]
                                .toLowerCase().trim();
                            if (newName.localeCompare(existingName) < 0) {
                                sel.insertBefore(clone, sel.options[i]);
                                inserted = true;
                                break;
                            }
                        }
                        if (!inserted) sel.appendChild(clone);
                        const placeholder = sel.querySelector('option[value=""]');
                        sel.removeChild(placeholder);
                        sel.insertBefore(placeholder, sel.firstChild);
                        sel.value = prev || '';
                        if (prev && sel.value !== prev) sel.selectedIndex = 0;
                    });
                    bootstrap.Modal.getInstance(modalEl).hide();
                    addForm.reset();
                    weightEdited = false;
                } catch (err) {
                    console.error(err);
                    alert('Errore imprevisto durante il salvataggio dell\'ingrediente.');
                }
            });

            // 2) gross → net (VAT)
            function netPrice(gross) {
                const vat = parseFloat(vatRateEl.value) || 0;
                return gross / (1 + vat / 100);
            }

            // 3) grams per piece
            function calcWeightPerPiece() {
                const pcs    = parseFloat(totalPiecesIn.value) || 0;
                const totalG = parseFloat(weightWithLossIn.value) || 0;
                weightPerPieceIn.value = pcs > 0 ? (totalG / pcs).toFixed(2) : '';
            }

            // 4) cost-per-min toggle
            function updateCostPerMin() {
                const rate = costModeShop.checked
                           ? parseFloat(shopRateEl.value) || 0
                           : parseFloat(externalRateEl.value) || 0;
                costPerMinIn.value = rate.toFixed(4);
                updateLaborCost();
            }

            // 5) labor cost
            function updateLaborCost() {
                const mins = parseFloat(laborTimeInput.value) || 0;
                const rate = parseFloat(costPerMinIn.value) || 0;
                laborCostIn.value = (mins * rate).toFixed(2);
                calculateLaborIncidence();
                recalcTotals();
            }

            // 6) labor incidence %
            function calculateLaborIncidence() {
                const lc   = parseFloat(laborCostIn.value) || 0;
                const g    = parseFloat(weightWithLossIn.value) || 0;
                const kg   = g / 1000;
                const sell = modePiece.checked
                           ? (parseFloat(totalPiecesIn.value) || 0) * (parseFloat(pricePerPiece.value) || 0)
                           : parseFloat(pricePerKg.value) || 0;
                laborIncidenceIn.value = (kg > 0 && sell > 0
                    ? ((lc / kg) / netPrice(sell)) * 100
                    : 0
                ).toFixed(2);
            }

            // 7) recalc one ingredient row
            function recalcRow(row) {
                const price = parseFloat(row.querySelector('.ingredient-select').selectedOptions[0]?.dataset.price) || 0;
                const qty   = parseFloat(row.querySelector('.ingredient-quantity').value) || 0;
                const cost  = price / 1000 * qty;
                row.querySelector('.ingredient-cost').value = cost.toFixed(2);

                const g    = parseFloat(weightWithLossIn.value) || 0;
                const kg   = g / 1000;
                const sell = modePiece.checked
                           ? (parseFloat(totalPiecesIn.value) || 0) * (parseFloat(pricePerPiece.value) || 0)
                           : parseFloat(pricePerKg.value) || 0;
                row.querySelector('.ingredient-incidence').value = (
                    kg > 0 && sell > 0
                    ? ((cost / kg) / netPrice(sell)) * 100
                    : 0
                ).toFixed(2);
            }

            // 8) recalc all ingredient totals
            function recalcTotals() {
                calculateLaborIncidence();
                let sW = 0, sC = 0, sI = 0;
                document.querySelectorAll('.ingredient-row').forEach(r => {
                    sW += parseFloat(r.querySelector('.ingredient-quantity').value) || 0;
                    sC += parseFloat(r.querySelector('.ingredient-cost').value)      || 0;
                    sI += parseFloat(r.querySelector('.ingredient-incidence').value) || 0;
                });
                totalWeightFt.value    = sW;
                totalCostIn.value      = sC.toFixed(2);
                totalIncidenceIn.value = sI.toFixed(2);
                hiddenTotalWt.value    = sW;
                if (!weightEdited) {
                    weightWithLossIn.value = sW;
                }
                recalcExpense();
                recalcMargin();
            }

            // 9) cost before/after packing
            function recalcExpense() {
                const ing = parseFloat(totalCostIn.value) || 0;
                const lab = parseFloat(laborCostIn.value) || 0;
                const raw = ing + lab;
                const pack = parseFloat(packingCostIn.value) || 0;

                if (modePiece.checked) {
                    const pcs          = parseFloat(totalPiecesIn.value) || 0;
                    const before       = pcs > 0 ? raw / pcs : 0;
                    const packPerPiece = pcs > 0 ? pack / pcs : 0;
                    prodCostKgIn.value  = before.toFixed(2);
                    totalExpenseIn.value = (before + packPerPiece).toFixed(2);
                } else {
                    const g     = parseFloat(weightWithLossIn.value) || 0;
                    const kg    = g / 1000;
                    const before = kg > 0 ? raw / kg : 0;
                    prodCostKgIn.value  = before.toFixed(2);
                    totalExpenseIn.value = (before + pack).toFixed(2);
                }
                recalcMargin();
            }

            // 10) potential margin
            function recalcMargin() {
                const sellP = parseFloat(pricePerPiece.value) || 0;
                const sellK = parseFloat(pricePerKg.value)   || 0;
                const netSP = netPrice(sellP);
                const netSK = netPrice(sellK);
                const costU = parseFloat(totalExpenseIn.value) || 0;

                if (modePiece.checked) {
                    const m   = netSP - costU;
                    const pct = netSP > 0 ? (m * 100 / netSP) : 0;
                    potentialMargin.innerText   = `€${m.toFixed(2)} (${pct.toFixed(2)}%) / piece`;
                    potentialInput.value        = m.toFixed(2);
                    potentialPctInput.value     = pct.toFixed(2);
                } else {
                    const m   = netSK - costU;
                    const pct = netSK > 0 ? (m * 100 / netSK) : 0;
                    potentialMargin.innerText   = `€${m.toFixed(2)} (${pct.toFixed(2)}%) / kg`;
                    potentialInput.value        = m.toFixed(2);
                    potentialPctInput.value     = pct.toFixed(2);
                }
            }

            // 11) mode toggle & label swapping
            function updateMode() {
                const beforeLabel = prodCostKgIn.closest('.input-group').querySelector('.input-group-text');
                const afterLabel  = totalExpenseIn.closest('.input-group').querySelector('.input-group-text');
                if (modePiece.checked) {
                    beforeLabel.textContent = 'Costo per pz prima dell’imballaggio';
                    afterLabel.textContent  = ' Costo per pz dopo l’imballaggio';
                } else {
                    beforeLabel.textContent = 'Costo €/kg prima dell’imballaggio';
    afterLabel.textContent  = 'Costo €/kg dopo dell’imballaggio';
                }
                document.getElementById('pieceInputs').classList.toggle('d-none', !modePiece.checked);
                document.getElementById('kgInputs')   .classList.toggle('d-none',  modePiece.checked);
                recalcTotals();
                if (modePiece.checked) calcWeightPerPiece();
            }

            // ——————————————
            // Wire up all listeners
            costModeShop.addEventListener('change', updateCostPerMin);
            costModeExternal.addEventListener('change', updateCostPerMin);
            laborTimeInput.addEventListener('input', updateLaborCost);
            vatRateEl.addEventListener('change', recalcTotals);
            packingCostIn.addEventListener('input', recalcExpense);

            // allow manual weight edits
            weightWithLossIn.addEventListener('input', () => {
                weightEdited = true;
                calcWeightPerPiece();
                recalcTotals();
            });

            pricePerPiece.addEventListener('input', recalcMargin);
            pricePerKg.addEventListener('input', recalcMargin);

            totalPiecesIn.addEventListener('input', () => {
                calcWeightPerPiece();
                updateMode();
            });
            modePiece.addEventListener('change', () => {
                calcWeightPerPiece();
                updateMode();
            });
            modeKg.addEventListener('change', updateMode);

            // reset weightEdited when ingredients change
            tableBody.addEventListener('input', e => {
                if (e.target.matches('.ingredient-select, .ingredient-quantity')) {
                    weightEdited = false;
                    recalcRow(e.target.closest('.ingredient-row'));
                    recalcTotals();
                }
            });

            document.getElementById('addIngredientBtn').addEventListener('click', e => {
                e.preventDefault();
                const first = tableBody.querySelector('.ingredient-row');
                const clone = first.cloneNode(true);
                const newIdx = idx++;
                clone.querySelectorAll('select[name], input[name]').forEach(el => {
                    el.name = el.name.replace(/\[\d+\]/, `[${newIdx}]`);
                    if (el.tagName === 'SELECT') el.selectedIndex = 0;
                    else el.value = el.classList.contains('ingredient-quantity') ? '0' : '';
                });
                tableBody.appendChild(clone);
                weightEdited = false;
                recalcRow(clone);
                recalcTotals();
            });

            tableBody.addEventListener('click', e => {
                if (e.target.closest('.remove-ingredient') && tableBody.children.length > 1) {
                    e.target.closest('.ingredient-row').remove();
                    weightEdited = false;
                    recalcTotals();
                }
            });

            // initial setup
            updateMode();
            updateCostPerMin();
            document.querySelectorAll('.ingredient-row').forEach(r => recalcRow(r));
            calcWeightPerPiece();
            recalcTotals();
        });
    </script>
@endsection

