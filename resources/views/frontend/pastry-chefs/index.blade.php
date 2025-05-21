@extends('frontend.layouts.app')

@section('title','Showcase Pasticceri')

@section('content')
<div class="container py-5 px-md-5">

  <!-- Form Aggiungi/Modifica Pasticcere -->
  <div class="card mb-4 border-primary shadow-sm">
    <div class="card-header d-flex align-items-center" style="background-color: #041930;">
      <i class="bi bi-egg-fried fs-4 me-2" style="color: #e2ae76;"></i>
      <h5 class="mb-0 fw-bold" style="color: #e2ae76;">
        {{ isset($pastryChef) ? 'Modifica Pasticceri' : 'Aggiungi Pasticceri' }}
      </h5>
    </div>
    <div class="card-body">
      <form 
        action="{{ isset($pastryChef) ? route('pastry-chefs.update', $pastryChef) : route('pastry-chefs.store') }}" 
        method="POST" 
        class="row g-3 needs-validation" 
        novalidate>
        @csrf
        @if(isset($pastryChef)) @method('PUT') @endif

        <div class="col-md-4">
          <label for="Name" class="form-label fw-semibold">Nome Pasticceri</label>
          <input 
            type="text" 
            id="Name" 
            name="name" 
            class="form-control form-control-lg"
            value="{{ old('name', $pastryChef->name ?? '') }}"
            placeholder="Inserisci Nome Pasticceri" 
            required>
          <div class="invalid-feedback">Per favore inserisci il nome del Chef.</div>
        </div>

        <div class="col-md-4">
          <label for="Email" class="form-label fw-semibold">Email Pasticceri</label>
          <input 
            type="email" 
            id="Email" 
            name="email" 
            class="form-control form-control-lg"
            value="{{ old('email', $pastryChef->email ?? '') }}"
            placeholder="Inserisci Email Pasticceri">
          <div class="invalid-feedback">Per favore inserisci l'email del Chef.</div>
        </div>

        <div class="col-md-4">
          <label for="Phone" class="form-label fw-semibold">Telefono</label>
          <input 
            type="text" 
            id="Phone" 
            name="phone" 
            class="form-control form-control-lg"
            value="{{ old('phone', $pastryChef->phone ?? '') }}"
            placeholder="Inserisci Telefono Pasticceri">
          <div class="invalid-feedback">Per favore inserisci il telefono del Pasticceri.</div>
        </div>

        <div class="col-12 text-end">
          <button type="submit" class="btn btn-gold-filled btn-lg">
            <i class="bi bi-save2 me-2"></i>
            {{ isset($pastryChef) ? 'Aggiorna Chef' : 'Salva Chef' }}
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Tabella Pasticceri -->
  <div class="card border-primary shadow-sm">
    <div class="card-header d-flex align-items-center" style="background-color: #041930;">
      <i class="bi bi-people fs-4 me-2" style="color: #e2ae76;"></i>
      <h5 class="mb-0 fw-bold" style="color: #e2ae76;">Lista Pasticceri</h5>
    </div>
    <div class="card-body table-responsive">
      <table
        id="pastryChefsTable"
        class="table table-bordered table-striped table-hover align-middle text-center mb-0"
        data-page-length="10">
        <thead>
          <tr>
            <th>Nome</th>
            <th>Email</th>
            <th>Telefono</th>
            <th>Ultimo Aggiornamento</th>
            <th>Azioni</th>
          </tr>
        </thead>
        <tbody>
          @forelse($pastryChefs as $chef)
            <tr>
              <td>{{ $chef->name }}</td>
              <td>{{ $chef->email ?? '—' }}</td>
              <td>{{ $chef->phone ?? '—' }}</td>
              <td>{{ optional($chef->updated_at)?->format('Y-m-d H:i') ?? '—' }}</td>
              <td>
                <a href="{{ route('pastry-chefs.show', $chef) }}" class="btn btn-sm btn-deepblue me-1" title="Visualizza">
                  <i class="bi bi-eye"></i>
                </a>
                <a href="{{ route('pastry-chefs.edit', $chef) }}" class="btn btn-sm btn-gold me-1" title="Modifica">
                  <i class="bi bi-pencil-square"></i>
                </a>
                <form 
                  action="{{ route('pastry-chefs.destroy', $chef) }}" 
                  method="POST" 
                  class="d-inline"
                  onsubmit="return confirm('Eliminare questo Chef?');">
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
              <td colspan="5" class="text-center text-muted">Nessun Chef trovato.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection

<style>
  table thead th {
    background-color: #e2ae76 !important;
    color: #041930 !important;
    text-align: center !important;
    vertical-align: middle !important;
  }

  table tbody td {
    text-align: center !important;
    vertical-align: middle !important;
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

  .btn-gold, .btn-deepblue, .btn-red {
    border: 1px solid !important;
    font-weight: 500;
  }

  .btn-gold {
    border-color: #e2ae76 !important;
    color: #e2ae76 !important;
  }
  .btn-gold:hover {
    background-color: #e2ae76 !important;
    color: white !important;
  }

  .btn-deepblue {
    border-color: #041930 !important;
    color: #041930 !important;
  }
  .btn-deepblue:hover {
    background-color: #041930 !important;
    color: white !important;
  }

  .btn-red {
    border-color: #ff0000 !important;
    color: red !important;
  }
  .btn-red:hover {
    background-color: #ff0000 !important;
    color: white !important;
  }
</style>

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    if (window.$ && $.fn.DataTable) {
      // Disabilita gli avvisi di DataTables
      $.fn.dataTable.ext.errMode = 'none';

      $('#pastryChefsTable').DataTable({
        paging:      true,
        ordering:    true,
        responsive:  true,
        pageLength:  $('#pastryChefsTable').data('page-length') || 10,
        columnDefs: [
          { orderable: false, targets: -1 }
        ],
        language: {
          search:        "Cerca:",
          lengthMenu:    "Mostra _MENU_ voci",
          info:          "Mostra _START_ di _END_ di _TOTAL_ elementi",
          infoEmpty:     "Nessun elemento disponibile",
          zeroRecords:   "Nessuna corrispondenza trovata",
          paginate: {
           
           
          }
        }
      });
    }

    // Validazione client-side Bootstrap
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

