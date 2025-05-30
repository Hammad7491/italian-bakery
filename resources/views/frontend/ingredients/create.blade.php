@extends('frontend.layouts.app')

@section('title', isset($ingredient) ? 'Modifica Ingrediente' : 'Aggiungi Ingrediente')

@section('content')
    <div class="container py-5">
        <div class="card border-success shadow-sm">
            <div class="card-header d-flex align-items-center" style="background-color: #041930; color: #e2ae76;">
                <!-- SVG Icon unchanged -->
                <svg height="30px" width="30px" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" style="fill: #e2ae76; margin-right: 10px;">
                    <path style="fill:#FFDC35;" d="M479.605,91.769c-23.376,23.376-66.058,33.092-79.268,19.882
                      c-13.21-13.21-3.494-55.892,19.883-79.268s85.999-26.614,85.999-26.614S502.982,68.393,479.605,91.769z"/>
                    <g>
                      <path style="fill:#FFAE33;" d="M506.218,5.785L400.345,111.658c13.218,13.2,55.888,3.483,79.26-19.889
                        C502.864,68.511,506.186,6.411,506.218,5.785z"/>
                      <path style="fill:#FFAE33;" d="M432.367,200.156c-33.059,0-70.11-23.311-70.11-41.992s37.052-41.992,70.11-41.992
                        s79.629,41.992,79.629,41.992S465.426,200.156,432.367,200.156z"/>
                    </g>
                    <path style="fill:#FFDC35;" d="M311.84,79.629c0,33.059,23.311,70.11,41.992,70.11s41.992-37.052,41.992-70.11S353.832,0,353.832,0
                      S311.84,46.571,311.84,79.629z"/>
                    <path style="fill:#FFAE33;" d="M367.516,265.006c-33.059,0-70.11-23.311-70.11-41.992s37.052-41.992,70.11-41.992
                      s79.629,41.992,79.629,41.992S400.575,265.006,367.516,265.006z"/>
                    <path style="fill:#FFDC35;" d="M246.99,144.48c0,33.059,23.311,70.11,41.992,70.11c18.681,0,41.992-37.052,41.992-70.11
                      S288.982,64.85,288.982,64.85S246.99,111.421,246.99,144.48z"/>
                    <path style="fill:#FFAE33;" d="M302.666,329.857c-33.059,0-70.11-23.311-70.11-41.992c0-18.681,37.052-41.992,70.11-41.992
                      s79.629,41.992,79.629,41.992S335.726,329.857,302.666,329.857z"/>
                    <path style="fill:#FFDC35;" d="M182.14,209.33c0,33.059,23.311,70.11,41.992,70.11s41.992-37.052,41.992-70.11
                      s-41.992-79.629-41.992-79.629S182.14,176.27,182.14,209.33z"/>
                    <path style="fill:#FFAE33;" d="M237.025,395.498c-33.059,0-70.11-23.311-70.11-41.992c0-18.681,37.052-41.992,70.11-41.992
                      s79.629,41.992,79.629,41.992S270.085,395.498,237.025,395.498z"/>
                    <path style="fill:#FFDC35;" d="M116.498,274.97c0,33.059,23.31,70.11,41.992,70.11s41.992-37.052,41.992-70.11
                      s-41.992-79.629-41.992-79.629S116.498,241.912,116.498,274.97z"/>
                    <path style="fill:#FFAE33;" d="M170.438,462.084c-33.059,0-70.11-23.311-70.11-41.992c0-18.681,37.052-41.992,70.11-41.992
                      s79.629,41.992,79.629,41.992S203.497,462.084,170.438,462.084z"/>
                    <path style="fill:#FFDC35;" d="M49.912,341.558c0,33.059,23.31,70.11,41.992,70.11s41.992-37.052,41.992-70.11
                      s-41.992-79.629-41.992-79.629S49.912,308.499,49.912,341.558z"/>
                    <path style="fill:#F29C2A;" d="M4.917,507.087c-6.552-6.552-6.552-17.174,0-23.725L404.75,83.527c6.552-6.552,17.174-6.552,23.725,0
                      c6.552,6.552,6.552,17.174,0,23.725L28.643,507.087C22.091,513.637,11.468,513.637,4.917,507.087z"/>
                </svg>
                <h5 class="mb-0" style="color: #e2ae76;">
                    {{ isset($ingredient) ? 'Modifica Ingrediente' : 'Aggiungi Ingrediente' }}
                </h5>
            </div>

            <div class="card-body">
                <form
                    action="{{ isset($ingredient) ? route('ingredients.update', $ingredient->id) : route('ingredients.store') }}"
                    method="POST"
                    class="row g-3 needs-validation"
                    novalidate
                >
                    @csrf
                    @if (isset($ingredient))
                        @method('PUT')
                    @endif

                    <div class="col-md-6">
                        <label for="ingredientName" class="form-label fw-semibold">Nome Ingrediente</label>
                        <input
                            type="text"
                            id="ingredientName"
                            name="ingredient_name"
                            class="form-control form-control-lg"
                            placeholder="es. Farina 00"
                            value="{{ old('ingredient_name', $ingredient->ingredient_name ?? '') }}"
                            required
                        >
                        <div class="invalid-feedback">
                            Inserisci un nome ingrediente.
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="pricePerKg" class="form-label fw-semibold">Prezzo al kg</label>
                        <div class="input-group input-group-lg has-validation">
                            <span class="input-group-text">€</span>
                            <input
                                type="number"
                                id="pricePerKg"
                                name="price_per_kg"
                                class="form-control"
                                step="0.01"
                                placeholder="0,00"
                                value="{{ old('price_per_kg', $ingredient->price_per_kg ?? '') }}"
                                required
                            >
                            <span class="input-group-text">/kg</span>
                            <div class="invalid-feedback">
                                Inserisci un prezzo valido.
                            </div>
                        </div>
                    </div>

                    <div class="col-12 text-end">
                        <button
                            type="submit"
                            class="btn btn-lg"
                            style="background-color: #e2ae76; color: #041930;"
                        >
                            <i class="bi bi-save2 me-2" style="color: #041930;"></i>
                            {{ isset($ingredient) ? 'Aggiorna Ingrediente' : 'Salva Ingrediente' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Bootstrap form validation
        (() => {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
@endsection
