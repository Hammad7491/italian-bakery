{{-- resources/views/frontend/equipment/index.blade.php --}}
@extends('frontend.layouts.app')

@section('title', 'Vetrina delle Attrezzature')

@section('content')
<div class="container py-5 px-md-5">

  <!-- Aggiungi Attrezzatura -->
  <div class="card mb-5 border-primary shadow-sm">
    <div class="card-header d-flex align-items-center" style="background-color: #041930;">
      <i class="bi bi-tools fs-4 me-2" style="color: #e2ae76;"></i>
      <h5 class="mb-0 fw-bold" style="color: #e2ae76;">Aggiungi Nuova Attrezzatura</h5>
    </div>
    <div class="card-body">
      <form action="{{ route('equipment.store') }}" method="POST" class="row g-3 needs-validation" novalidate>
        @csrf
        <div class="col-md-8">
          <label for="Name" class="form-label fw-semibold">Nome Attrezzatura</label>
          <input
            type="text"
            id="Name"
            name="name"
            class="form-control form-control-lg"
            placeholder="es. Planetaria, Forno"
            required
            value="{{ old('name') }}">
          <div class="invalid-feedback">Inserisci un nome per l'attrezzatura.</div>
        </div>
        <div class="col-md-4 text-end align-self-end">
          <button type="submit" class="btn btn-gold-filled btn-lg">
            <i class="bi bi-save2 me-1"></i> Salva Attrezzatura
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Elenco Attrezzature -->
  <div class="card border-primary shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #041930;">
      <h5 class="mb-0 fw-bold" style="color: #e2ae76;">
        <i class="bi bi-list-ul me-2" style="color: #e2ae76;"></i> Elenco Attrezzature
      </h5>
    </div>
    <div class="card-body table-responsive">
      <table
        id="equipmentTable"
        class="table table-bordered table-striped table-hover align-middle text-center mb-0"
        data-page-length="10">
        <thead>
          <tr>
            <th class="text-center">Nome</th>
            <th class="text-center">Azioni</th>
          </tr>
        </thead>
        <tbody>
          @forelse($equipments as $equ)
            <tr>
              <td>{{ $equ->name }}</td>
              <td>
                <a href="{{ route('equipment.show', $equ) }}" class="btn btn-sm btn-deepblue me-1" title="Visualizza">
                  <i class="bi bi-eye"></i>
                </a>
                <a href="{{ route('equipment.edit', $equ) }}" class="btn btn-sm btn-gold me-1" title="Modifica">
                  <i class="bi bi-pencil-square"></i>
                </a>
                <form
                  action="{{ route('equipment.destroy', $equ) }}"
                  method="POST"
                  class="d-inline"
                  onsubmit="return confirm('Eliminare questa attrezzatura?');">
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
              <td colspan="2" class="text-muted">Nessuna attrezzatura trovata.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection


<style>
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
  .btn-gold-filled i {
    color: inherit !important;
  }

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

  table thead th {
    background-color: #e2ae76 !important;
    color: #041930 !important;
    text-align: center;
    vertical-align: middle;
  }
  table tbody td {
    text-align: center;
    vertical-align: middle;
  }
</style>


@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    if (window.$ && $.fn.DataTable) {
      // Disabilita gli alert di DataTables
      $.fn.dataTable.ext.errMode = 'none';
      $('#equipmentTable').DataTable({
        paging:      true,
        ordering:    true,
        responsive:  true,
        pageLength:  $('#equipmentTable').data('page-length') || 10,
        columnDefs: [
          { orderable: false, targets: -1 } // solo colonna Azioni
        ],
        language: {
          search:        "Cerca:",
          lengthMenu:    "Mostra _MENU_ voci",
          info:          "Mostra _START_ di _END_ di _TOTAL_ elementi",
          infoEmpty:     "Nessun elemento disponibile",
          zeroRecords:   "Nessuna corrispondenza trovata",
          paginate: {
            first:    "Primo",
            last:     "Ultimo",
            next:     "Successivo",
            previous: "Precedente"
          }
        }
      });
    }

    // Validazione Bootstrap
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
      form.addEventListener('submit', function(e) {
        if (!this.checkValidity()) {
          e.preventDefault();
          e.stopPropagation();
        }
        this.classList.add('was-validated');
      }, false);
    });
  });
</script>
@endsection

