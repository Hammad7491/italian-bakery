@extends('frontend.layouts.app')

@section('title', 'Tutti i Costi')

@section('content')
    <div class="container py-5 px-md-5">

        <!-- Aggiungi / Modifica Costo -->
        <div class="card mb-5 border-warning shadow-sm">
            <div class="card-header d-flex align-items-center" style="background-color: #041930;">
                <iconify-icon icon="mdi:format-list-bulleted" class="me-2"
                    style="width: 1.0em; height: 1.0em; color: #e2ae76; font-size: 1.6vw;">
                </iconify-icon>
                <h5 class="mb-0 fw-bold" style="color: #e2ae76;">
                    {{ isset($cost) ? 'Modifica Costo' : 'Aggiungi Costo' }}
                </h5>
            </div>
            
            <div class="card-body">
                <form method="POST" action="{{ isset($cost) ? route('costs.update', $cost) : route('costs.store') }}"
                    class="row g-3 needs-validation" novalidate>
                    @csrf
                    @isset($cost)
                        @method('PUT')
                    @endisset

                    <div class="col-md-6">
                        <label for="cost_identifier" class="form-label fw-semibold">Identificatore Costo <small
                                class="text-muted">(facoltativo)</small></label>
                        <input type="text" id="cost_identifier" name="cost_identifier"
                            class="form-control form-control-lg"
                            value="{{ old('cost_identifier', $cost->cost_identifier ?? '') }}">
                    </div>

                    <div class="col-md-6">
                        <label for="supplier" class="form-label fw-semibold">Fornitore</label>
                        <input type="text" id="supplier" name="supplier" class="form-control form-control-lg"
                            value="{{ old('supplier', $cost->supplier ?? '') }}" required>
                        <div class="invalid-feedback">Inserisci un fornitore.</div>
                    </div>

                    <div class="col-md-6">
                        <label for="amount" class="form-label fw-semibold">Importo</label>
                        <div class="input-group input-group-lg has-validation">
                            <span class="input-group-text">€</span>
                            <input type="number" step="0.01" id="amount" name="amount" class="form-control"
                                value="{{ old('amount', $cost->amount ?? '') }}" required>
                            <div class="invalid-feedback">Inserisci un importo valido.</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="due_date" class="form-label fw-semibold">Data di scadenza</label>
                        <input type="date" id="due_date" name="due_date" class="form-control form-control-lg"
                            value="{{ old('due_date', $cost->due_date ?? '') }}" required>
                        <div class="invalid-feedback">Seleziona una data.</div>
                    </div>

                    <div class="col-md-6">
                        <label for="category_id" class="form-label fw-semibold">Categoria</label>
                        <select id="category_id" name="category_id" class="form-select form-select-lg" required>
                            <option value="">Seleziona…</option>
                            @foreach ($categories as $c)
                                <option value="{{ $c->id }}"
                                    {{ old('category_id', $cost->category_id ?? '') == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Seleziona una categoria.</div>
                    </div>

                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-gold-filled btn-lg">
                            <i class="bi bi-save2 me-1"></i>
                            {{ isset($cost) ? 'Aggiorna Costo' : 'Salva Costo' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Filtra per mese -->
        <div class="row g-2 align-items-end mb-4">
            <div class="col-auto">
                <label for="filterMonth" class="form-label fw-semibold">Mostra mese</label>
                <input type="month" id="filterMonth" class="form-control form-control-lg"
                    value="{{ now()->format('Y-m') }}">
            </div>
        </div>

        <!-- Tabella Costi -->
        <div class="card border-warning shadow-sm">
            <div class="card-header d-flex align-items-center" style="background-color: #041930;">
                <h5 class="mb-0 fw-bold d-flex align-items-center" style="color: #e2ae76; font-size: 1.6vw;">
                    <iconify-icon icon="mdi:table" class="me-2" style="font-size: 1.7vw; color: #e2ae76;">
                    </iconify-icon>
                    Tutti i Costi
                </h5>
            </div>
            <div class="card-body table-responsive">
                <table id="costTable"
                       class="table table-bordered table-striped table-hover align-middle text-center mb-0"
                       data-page-length="10">
                    <thead >
                        <tr >
                            <th style="width:20px;">Identificatore</th>
                            <th>Fornitore</th>
                            <th class="text-end">Importo</th>
                            <th>Scadenza</th>
                            <th>Categoria</th>
                            <th class="text-center">Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($costs as $item)
                            <tr>
                                <td>{{ $item->cost_identifier }}</td>
                                <td>{{ $item->supplier }}</td>
                                <td class="text-end">€{{ number_format($item->amount, 2) }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->due_date)->format('Y-m-d') }}</td>
                                <td>{{ $item->category->name ?? '–' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('costs.show', $item) }}"
                                       class="btn btn-sm btn-deepblue me-1" title="Visualizza Costo">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('costs.edit', $item) }}"
                                       class="btn btn-sm btn-gold me-1" title="Modifica Costo">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('costs.destroy', $item) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Eliminare questo costo?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-red" title="Elimina Costo">
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
@endsection

<style>


/* Make the first column at least 200px wide */
/* force fixed layout so widths are respected */



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
        border-color: #ff0000 !important;
        color: red !important;
    }
    .btn-red:hover {
        background-color: #ff0000 !important;
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
        document.addEventListener('DOMContentLoaded', function() {
            if (window.$ && $.fn.DataTable) {
                var table = $('#costTable').DataTable({
                    paging: true,
                    ordering: true,
                    responsive: true,
                    pageLength: $('#costTable').data('page-length'),
                    columnDefs: [{ orderable: false, targets: 5 }],
                    language: {
                        search: "Cerca:",
                        lengthMenu: "Mostra _MENU_ voci per pagina",
                        info: "Visualizzati da _START_ a _END_ di _TOTAL_ costi",
                        paginate: { previous: "«", next: "»" },
                        zeroRecords: "Nessun costo trovato"
                    }
                });

                $.fn.dataTable.ext.search.push(function(settings, data) {
                    var selected = $('#filterMonth').val();
                    if (!selected) return true;
                    var dueDate = data[3];
                    return dueDate.substr(0, 7) === selected;
                });

                $('#filterMonth').on('change', function() {
                    table.draw();
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
