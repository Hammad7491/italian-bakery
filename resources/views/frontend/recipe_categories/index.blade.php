{{-- resources/views/frontend/recipe-categories/index.blade.php --}}
@extends('frontend.layouts.app')

@section('title','Gestione Categorie Ricette')

@section('content')
<div class="container py-5 px-md-5">
  <!-- Form Card -->
  <div class="card border-primary shadow-sm mb-5">
    <div class="card-header d-flex align-items-center" style="background-color: #041930;">
      <i class="bi bi-tags fs-4 me-2" style="color: #e2ae76;"></i>
      <h5 class="mb-0 fw-bold" style="color: #e2ae76;">
        {{ isset($category) ? 'Modifica Categoria Ricetta' : 'Aggiungi Categoria Ricetta' }}
      </h5>
    </div>
    <div class="card-body">
      <form
        action="{{ isset($category) ? route('recipe-categories.update', $category->id) : route('recipe-categories.store') }}"
        method="POST"
        class="row g-3 needs-validation"
        novalidate>
        @csrf
        @if(isset($category)) @method('PUT') @endif

        <div class="col-md-8">
          <label for="categoryName" class="form-label fw-semibold">Nome Categoria</label>
          <input
            type="text"
            id="categoryName"
            name="name"
            class="form-control form-control-lg"
            placeholder="es. Dessert"
            value="{{ old('name', $category->name ?? '') }}"
            required>
          <div class="invalid-feedback">Inserisci un nome per la categoria.</div>
        </div>

        <div class="col-12 text-end">
          <button type="submit" class="btn btn-lg" style="background-color: #e2ae76; color: #041930;">
            <i class="bi bi-save2 me-2" style="color: #041930;"></i>
            {{ isset($category) ? 'Aggiorna Categoria' : 'Salva Categoria' }}
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Table Card -->
  <div class="card border-primary shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #041930;">
      <h5 class="mb-0 fw-bold d-flex align-items-center" style="color: #e2ae76;">
        <i class="bi bi-tags fs-4 me-2" style="color: #e2ae76;"></i>
        Elenco Categorie Ricette
      </h5>
    </div>

    <div class="card-body px-4">
      <div class="table-responsive p-3">
        <table id="categoryTable"
               class="table table-bordered table-striped table-hover align-middle mb-0 text-center"
               data-page-length="10">
          <thead style="background-color: #e2ae76; color: #041930;">
            <tr>
              <th>Nome</th>
              <th>Ultimo Aggiornamento</th>
              <th>Azioni</th>
            </tr>
          </thead>
          <tbody>
            @foreach($categories as $cat)
              <tr>
                <td>{{ $cat->name }}</td>
                <td>{{ $cat->updated_at->format('Y-m-d H:i') }}</td>
                <td>
                  <a href="{{ route('recipe-categories.edit', $cat) }}" class="btn btn-sm btn-gold me-1" title="Modifica">
                    <i class="bi bi-pencil"></i>
                  </a>
                  <a href="{{ route('recipe-categories.show', $cat) }}" class="btn btn-sm btn-deepblue me-1" title="Visualizza">
                    <i class="bi bi-eye"></i>
                  </a>
                  <form action="{{ route('recipe-categories.destroy', $cat) }}"
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
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    if (window.$ && $.fn.DataTable) {
      $('#categoryTable').DataTable({
        paging:     true,
        ordering:   true,
        responsive: true,
        pageLength: $('#categoryTable').data('page-length'),
        order:      [[0, 'asc']],
        language: {
          lengthMenu:    "Mostra _MENU_ elementi per pagina",
          search:        "Cerca:",
          info:          "Mostra _START_ a _END_ di _TOTAL_ elementi",
          zeroRecords:   "Nessun record trovato",
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


<style>
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
    border: 1px solid #ff0000 !important;
    color: red !important;
    background-color: transparent !important;
  }
  .btn-red:hover {
    background-color: #ff0000 !important;
    color: white !important;
  }

  table th {
    background-color: #e2ae76 !important;
    color: #041930 !important;
    text-align: center !important;
    vertical-align: middle !important;
    font-weight: bold;
  }

  table td {
    text-align: center !important;
    vertical-align: middle !important;
  }

  /* Sorting arrows color */
  table.dataTable thead .sorting:after,
  table.dataTable thead .sorting_asc:after,
  table.dataTable thead .sorting_desc:after {
    color: #041930 !important;
  }
</style>
