{{-- resources/views/frontend/cost_categories/index.blade.php --}}
@extends('frontend.layouts.app')

@section('title', 'Tutte le Categorie di Costo')

@section('content')
<div class="container py-5 px-md-5">

  <!-- Aggiungi / Modifica Categoria -->
  <div class="card border-primary shadow-sm mb-5">
    <div class="card-header d-flex align-items-center" style="background-color: #041930;">
      <i class="bi bi-list fs-4 me-2" style="color: #e2ae76;"></i>
      <h5 class="mb-0 fw-bold" style="color: #e2ae76;">
        {{ isset($category) ? 'Modifica Categoria' : 'Aggiungi Categoria' }}
      </h5>
    </div>
    <div class="card-body">
      <form
        action="{{ isset($category) ? route('cost_categories.update', $category) : route('cost_categories.store') }}"
        method="POST"
        class="needs-validation row g-3"
        novalidate>
        @csrf
        @if(isset($category)) @method('PUT') @endif

        <div class="col-md-8">
          <label for="name" class="form-label fw-semibold">Nome Categoria</label>
          <input
            type="text"
            id="name"
            name="name"
            class="form-control form-control-lg"
            placeholder="es. Utenze, Affitto, Imballaggio"
            value="{{ old('name', $category->name ?? '') }}"
            required>
          <div class="invalid-feedback">Per favore inserisci un nome di categoria.</div>
        </div>

        <div class="col-12 text-end">
          <button type="submit" class="btn btn-gold-filled btn-lg">
            <i class="bi bi-save2 me-2"></i>
            {{ isset($category) ? 'Aggiorna Categoria' : 'Salva Categoria' }}
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Tabella Categorie -->
  <div class="card border-primary shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #041930;">
      <h5 class="mb-0 fw-bold" style="color: #e2ae76;">
        <i class="bi bi-list fs-4 me-2"></i> Categorie di Costo
      </h5>
    </div>
    <div class="card-body table-responsive">
      <table
        id="categoriesTable"
        class="table table-bordered table-striped table-hover align-middle text-center mb-0"
        data-page-length="10">
        <thead>
          <tr>
            <th>Nome Categoria</th>
            <th>Azioni</th>
          </tr>
        </thead>
        <tbody>
          @forelse($categories as $cat)
            <tr>
              <td>{{ $cat->name }}</td>
              <td>
                <a href="{{ route('cost_categories.show', $cat) }}" class="btn btn-sm btn-deepblue me-1" title="Visualizza">
                  <i class="bi bi-eye"></i>
                </a>
                <a href="{{ route('cost_categories.edit', $cat) }}" class="btn btn-sm btn-gold me-1" title="Modifica">
                  <i class="bi bi-pencil-square"></i>
                </a>
                <form action="{{ route('cost_categories.destroy', $cat) }}"
                      method="POST"
                      class="d-inline"
                      onsubmit="return confirm('Eliminare questa categoria?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-red" title="Elimina">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="2" class="text-muted">Nessuna categoria trovata.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

<style>
  .btn-gold {
    border: 1px solid #e2ae76 !important;
    color: #e2ae76 !important;
    background-color: transparent !important;
    transition: all 0.2s ease-in-out;
  }
  .btn-gold:hover {
    background-color: #e2ae76 !important;
    color: white !important;
  }

  .btn-deepblue {
    border: 1px solid #041930 !important;
    color: #041930 !important;
    background-color: transparent !important;
    transition: all 0.2s ease-in-out;
  }
  .btn-deepblue:hover {
    background-color: #041930 !important;
    color: white !important;
  }

  .btn-red {
    border: 1px solid #ff0000 !important;
    color: red !important;
    background-color: transparent !important;
    transition: all 0.2s ease-in-out;
  }
  .btn-red:hover {
    background-color: #ff0000 !important;
    color: white !important;
  }

  .btn-gold-filled {
    background-color: #e2ae76 !important;
    color: #041930 !important;
    border: none !important;
    font-weight: 500;
    padding: 10px 24px;
    border-radius: 12px;
    transition: background-color 0.2s ease;
  }
  .btn-gold-filled:hover {
    background-color: #d89d5c !important;
    color: white !important;
  }

  table th {
    background-color: #e2ae76 !important;
    color: #041930 !important;
    text-align: center;
    vertical-align: middle;
  }
  table td {
    text-align: center;
    vertical-align: middle;
  }
</style>

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    if (window.$ && $.fn.DataTable) {
      $.fn.dataTable.ext.errMode = 'none';
      $('#categoriesTable').DataTable({
        paging:      true,
        ordering:    true,
        responsive:  true,
        pageLength:  $('#categoriesTable').data('page-length') || 10,
        columnDefs: [
          { orderable: false, targets: -1 }
        ],
        language: {
          lengthMenu:    "Mostra _MENU_ elementi per pagina",
          zeroRecords:   "Nessun record trovato",
          info:          "Mostra _START_ a _END_ di _TOTAL_ elementi",
          infoEmpty:     "Mostra 0 a 0 di 0 elementi",
          infoFiltered:  "(filtrati da _MAX_ totali)",
          search:        "Cerca:",
          paginate: {
            first:    "Primo",
            previous: "←",
            next:     "→",
            last:     "Ultimo"
          }
        }
      });
    }

    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
      form.addEventListener('submit', e => {
        if (!form.checkValidity()) {
          e.preventDefault();
          e.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  });
</script>
@endsection
