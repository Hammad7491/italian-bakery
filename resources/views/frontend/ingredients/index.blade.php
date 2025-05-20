{{-- resources/views/frontend/ingredients/index.blade.php --}}
@extends('frontend.layouts.app')

@section('title','Vetrina Ingredienti')

@section('content')
<div class="container py-5 px-md-5">

  <!-- Add/Edit Ingredient Form -->
  <div class="card border-primary shadow-sm mb-5">
    <div class="card-header d-flex align-items-center"
         style="background-color: #041930; padding: .5rem; border-top-left-radius: .5rem; border-top-right-radius: .5rem;">
      <!-- SVG Ingredient Icon with gold color -->
      <svg class="me-2" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"
           style="width: 1.6vw; height: 1.6vw; color: #e2ae76; fill: currentColor;">
        <!-- ... SVG paths omitted for brevity ... -->
      </svg>

      <h5 class="mb-0 fw-bold" style="color: #e2ae76; font-size: 1.6vw;">
        {{ isset($ingredient) ? 'Modifica Ingrediente' : 'Aggiungi Ingrediente' }}
      </h5>
    </div>

    <div class="card-body">
      <form
        action="{{ isset($ingredient) ? route('ingredients.update', $ingredient) : route('ingredients.store') }}"
        method="POST"
        class="row g-3 needs-validation"
        novalidate
      >
        @csrf
        @if (isset($ingredient)) @method('PUT') @endif

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
          <div class="invalid-feedback">Inserisci un nome ingrediente.</div>
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
            <div class="invalid-feedback">Inserisci un prezzo valido.</div>
          </div>
        </div>

        <div class="col-12 text-end">
          <button type="submit"
                  class="btn btn-lg"
                  style="background-color: #e2ae76; color: #041930; border-color: #e2ae76;">
            <i class="bi bi-save2 me-2"></i>
            {{ isset($ingredient) ? 'Aggiorna Ingrediente' : 'Salva Ingrediente' }}
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Ingredients Table -->
  <div class="card border-primary shadow-sm">
    <div class="card-header" style="background-color: #041930;">
      <h5 class="mb-0" style="color: #e2ae76;">Vetrina Ingredienti</h5>
    </div>
    <div class="card-body px-4" style="overflow-x: hidden;">
      <div class="table-responsive p-3">
        <table
          id="ingredientsTable"
          class="table table-bordered table-striped table-hover align-middle mb-0 text-center"
          data-page-length="10"
        >
          <thead>
            <tr>
              <th>Nome</th>
              <th>Prezzo / kg</th>
              <th>Ultimo agg.</th>
              <th>Azioni</th>
            </tr>
          </thead>
          <tbody>
            @forelse($ingredients as $ing)
              <tr>
                <td>{{ $ing->ingredient_name }}</td>
                <td>€{{ number_format($ing->price_per_kg, 2) }}</td>
                <td>{{ $ing->updated_at->format('Y-m-d H:i') }}</td>
                <td>
                  <a href="{{ route('ingredients.edit', $ing) }}" class="btn btn-sm btn-gold me-1" title="Modifica">
                    <i class="bi bi-pencil"></i>
                  </a>
                  <a href="{{ route('ingredients.show', $ing) }}" class="btn btn-sm btn-deepblue me-1" title="Visualizza">
                    <i class="bi bi-eye"></i>
                  </a>
                  <form action="{{ route('ingredients.destroy', $ing) }}"
                        method="POST"
                        class="d-inline"
                        onsubmit="return confirm('Eliminare questo ingrediente?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-red" title="Elimina">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-muted">Nessun ingrediente trovato.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>
@endsection

<style>
  /* Table header */
  table.dataTable thead th {
    background-color: #e2ae76 !important;
    color: #041930 !important;
    text-align: center;
    vertical-align: middle;
  }

  /* Table cells */
  table.dataTable tbody td {
    text-align: center;
    vertical-align: middle;
  }

  /* Prevent bottom scrollbar */
  .card-body[style*="overflow-x"] .table-responsive {
    overflow-x: visible !important;
  }

  /* Buttons */
  .btn-gold {
    border: 1px solid #e2ae76 !important;
    color: #e2ae76 !important;
    background-color: transparent !important;
  }
  .btn-gold:hover {
    background-color: #e2ae76 !important;
    color: #fff !important;
  }
  .btn-deepblue {
    border: 1px solid #041930 !important;
    color: #041930 !important;
    background-color: transparent !important;
  }
  .btn-deepblue:hover {
    background-color: #041930 !important;
    color: #fff !important;
  }
  .btn-red {
    border: 1px solid #ff0000 !important;
    color: red !important;
    background-color: transparent !important;
  }
  .btn-red:hover {
    background-color: red !important;
    color: #fff !important;
  }

  /* Dropdown arrow styling */
  .dataTables_length select {
    border: 1px solid #e2ae76;
    color: #041930;
    padding-right: 30px;
    background: #fff url('data:image/svg+xml;utf8,<svg fill="%23e2ae76" height="20" viewBox="0 0 24 24" width="20" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>') no-repeat right 10px center;
    appearance: none;
  }

  /* Prevent Primo/Ultimo from wrapping */
  .dataTables_wrapper .dataTables_paginate .paginate_button {
    white-space: nowrap;
  }
</style>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Initialize DataTable with explicit column defs
  if (window.$ && $.fn.DataTable) {
    $('#ingredientsTable').DataTable({
      responsive: true,
      columns: [
        null,
        null,
        null,
        { orderable: false }
      ],
      language: {
        search: "_INPUT_",
        searchPlaceholder: "Cerca ingredienti...",
        lengthMenu: "Mostra _MENU_ elementi",
        zeroRecords: "Nessun ingrediente corrispondente",
        info: "Mostra _START_ a _END_ di _TOTAL_ elementi",
        infoEmpty: "Nessun ingrediente disponibile",
        paginate: {
          first: "",
          last:  "",
          next:  "→",
          previous: "←"
        }
      }
    });
  }

  // Bootstrap 5 tooltips
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(el => new bootstrap.Tooltip(el));

  // Bootstrap form validation
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
});
</script>
@endsection
