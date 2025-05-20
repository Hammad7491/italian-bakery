{{-- resources/views/frontend/incomes/index.blade.php --}}
@extends('frontend.layouts.app')

@section('title','Tutte le Entrate')

@section('content')
<div class="container py-5 px-md-5">

  <!-- Aggiungi / Modifica Entrata -->
  <div class="card mb-5 border-success shadow-sm">
    <div class="card-header d-flex align-items-center" style="background-color: #041930;">
      <h5 class="mb-0 fw-bold d-flex align-items-center" style="color: #e2ae76; font-size: 1.7vw;">
        <iconify-icon
          icon="mdi:currency-eur"
          style="margin-right: 0.0em; height: 1.1em; font-size: 1.7vw; color: #e2ae76;">
        </iconify-icon>
        {{ isset($income) ? 'Modifica Entrata' : 'Aggiungi Entrata' }}
      </h5>
    </div>
    <div class="card-body">
      <form
        action="{{ isset($income) ? route('incomes.update', $income) : route('incomes.store') }}"
        method="POST"
        class="row g-3 needs-validation"
        novalidate
      >
        @csrf
        @if(isset($income)) @method('PUT') @endif

        <div class="col-md-6">
          <label for="identifier" class="form-label fw-semibold">
            Identificatore <small class="text-muted">(facoltativo)</small>
          </label>
          <input
            type="text"
            name="identifier"
            id="identifier"
            class="form-control form-control-lg"
            value="{{ old('identifier', $income->identifier ?? '') }}"
          >
        </div>

        <div class="col-md-6">
          <label for="amount" class="form-label fw-semibold">Importo (€)</label>
          <div class="input-group input-group-lg has-validation">
            <span class="input-group-text"><i class="bi bi-currency-euro"></i></span>
            <input
              type="number"
              step="0.01"
              name="amount"
              id="amount"
              class="form-control"
              value="{{ old('amount', $income->amount ?? '') }}"
              required
            >
            <div class="invalid-feedback">
              {{ $errors->first('amount', 'Inserisci un importo valido.') }}
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <label for="date" class="form-label fw-semibold">Data</label>
          <input
            type="date"
            name="date"
            id="date"
            class="form-control form-control-lg"
            value="{{ old('date', isset($income) ? $income->date->format('Y-m-d') : '') }}"
            required
          >
          <div class="invalid-feedback">
            {{ $errors->first('date', 'Seleziona una data.') }}
          </div>
        </div>

        <div class="col-12 text-end">
          <button type="submit" class="btn btn-gold-save btn-lg">
            <i class="bi bi-save2 me-1"></i>
            {{ isset($income) ? 'Aggiorna Entrata' : 'Salva Entrata' }}
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Filtra per Mese -->
  <div class="row g-2 align-items-end mb-4">
    <div class="col-auto">
      <label for="filterMonth" class="form-label fw-semibold">Mostra mese</label>
      <input type="month" id="filterMonth" class="form-control form-control-lg"
             value="{{ now()->format('Y-m') }}">
    </div>
  </div>

  <!-- Tabella Entrate Registrate -->
  <div class="card border-success shadow-sm">
    <div class="card-header d-flex align-items-center justify-content-between"
         style="background-color: #041930; color: #e2ae76;">
      <h5 class="mb-0 fw-bold">
        <i class="bi bi-list-ul me-2"></i>Entrate Registrate
      </h5>
    </div>
    <div class="card-body table-responsive">
      <table
        id="incomesTable"
        class="table table-bordered table-striped table-hover align-middle text-center mb-0"
        data-page-length="10"
      >
        <thead>
          <tr>
            <th>Identificatore</th>
            <th>Data</th>
            <th>Importo (€)</th>
            <th>Azioni</th>
          </tr>
        </thead>
        <tbody>
          @forelse($incomes as $inc)
            <tr>
              <td>{{ $inc->identifier ?? '—' }}</td>
              <td data-order="{{ $inc->date->format('Y-m-d') }}">
                {{ $inc->date->format('Y-m-d') }}
              </td>
              <td>€{{ number_format($inc->amount, 2) }}</td>
              <td class="text-center">
                <a href="{{ route('incomes.show', $inc) }}" class="btn btn-sm btn-deepblue me-1" title="Visualizza Entrata">
                  <i class="bi bi-eye"></i>
                </a>
                <a href="{{ route('incomes.edit', $inc) }}" class="btn btn-sm btn-gold me-1" title="Modifica Entrata">
                  <i class="bi bi-pencil"></i>
                </a>
                <form action="{{ route('incomes.destroy', $inc) }}" method="POST" class="d-inline" onsubmit="return confirm('Eliminare questa entrata?');">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-red" title="Elimina Entrata">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-muted">Nessuna entrata registrata.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
      <div class="mt-3">
        {{ $incomes->links() }}
      </div>
    </div>
  </div>

</div>
@endsection

<style>
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
  .btn-gold-save {
    border: 1px solid #e2ae76 !important;
    color: #041930 !important;
    background-color: #e2ae76 !important;
  }
  .btn-gold-save:hover {
    background-color: #d89d5c !important;
    color: white !important;
  }
  .btn-gold, .btn-deepblue, .btn-red {
    border: 1px solid;
    background-color: transparent !important;
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
    border-color: red !important;
    color: red !important;
  }
  .btn-red:hover {
    background-color: red !important;
    color: white !important;
  }
</style>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  if (window.$ && $.fn.DataTable) {
    $.fn.dataTable.ext.errMode = 'none';
    var table = $('#incomesTable').DataTable({
      paging:     true,
      ordering:   true,
      responsive: true,
      pageLength: $('#incomesTable').data('page-length'),
      order: [[1, 'desc']],
      columnDefs: [{ orderable: false, targets: 3 }],
      language: {
        search: "Cerca:",
        lengthMenu: "Mostra _MENU_ voci",
        info: "Visualizzati da _START_ a _END_ di _TOTAL_ entrate",
        paginate: { previous: "«", next: "»" },
        zeroRecords: "Nessuna entrata trovata"
      }
    });

    $.fn.dataTable.ext.search.push(function(settings, data) {
      if (settings.nTable.id !== 'incomesTable') return true;
      var selected = $('#filterMonth').val();
      if (!selected) return true;
      return data[1].substr(0,7) === selected;
    });

    $('#filterMonth').on('change', function() {
      table.draw();
    });
  }

  const forms = document.querySelectorAll('.needs-validation');
  forms.forEach(form => {
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
