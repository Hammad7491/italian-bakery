{{-- resources/views/frontend/user-management/users/index.blade.php --}}
@extends('frontend.layouts.app')

@section('title','Utenti')

@section('content')
@php
    use Carbon\Carbon;
@endphp

<div class="container py-5 px-md-4">

  {{-- Scheda Profilo Utente Loggato --}}
  <div class="row mb-5 justify-content-center">
    <div class="col-md-6">
      <div class="card shadow-lg rounded-3 border-0 overflow-hidden">
        <div class="card-body text-center pt-5">
          <h4 class="fw-bold mb-1">{{ auth()->user()->name }}</h4>
          <p class="text-muted mb-3">{{ auth()->user()->email }}</p>
          <div class="mb-3">
            @forelse(auth()->user()->roles as $role)
              <span class="badge bg-primary me-1">{{ ucfirst($role->name) }}</span>
            @empty
              <span class="text-secondary">Nessun ruolo assegnato</span>
            @endforelse
          </div>
          <a href="{{ route('users.show', auth()->user()) }}"
             class="btn btn-deepblue btn-sm me-2" title="Visualizza Profilo">
            <i class="bi bi-eye me-1"></i>Visualizza Profilo
          </a>
          <a href="{{ route('users.edit', auth()->user()) }}"
             class="btn btn-gold btn-sm me-2" title="Modifica Profilo">
            <i class="bi bi-pencil me-1"></i>Modifica Profilo
          </a>
          <a href="{{ route('logout') }}" 
             onclick="event.preventDefault();document.getElementById('logout-form').submit();"
             class="btn btn-red btn-sm" title="Esci">
            <i class="bi bi-box-arrow-right me-1"></i>Esci
          </a>
          <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
          </form>
        </div>
      </div>
    </div>
  </div>

  {{-- Intestazione Pagina & Bottone Aggiungi Utente --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div class="page-header d-flex align-items-center mb-0">
      <i class="bi bi-people-fill me-2 fs-3" style="color: #e2ae76;"></i>
      <h2 class="mb-0 fw-bold" style="color: #041930;">Utenti</h2>
    </div>
    <a href="{{ route('users.create') }}" class="btn btn-gold-blue btn-lg">
      <i class="bi bi-plus-lg me-1"></i> Aggiungi Utente
    </a>
  </div>

  {{-- Messaggio di Successo --}}
  @if(session('success'))
    <div class="alert alert-success mb-4 p-3 rounded-3 shadow-sm">
      <i class="bi bi-check-circle-fill me-2"></i>
      <strong>Successo!</strong> {{ session('success') }}
    </div>
  @endif

  {{-- Tabella Utenti --}}
  <div class="table-responsive">
    <table class="table table-hover table-bordered shadow-sm rounded align-middle">
      <thead style="background-color: #e2ae76; color: #041930;">
        <tr class="text-center fw-semibold">
          <th>Nome</th>
          <th>Email</th>
          <th>Ruoli</th>
          <th>Scadenza</th>
          <th>Stato</th>
          <th class="text-end">Azioni</th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $u)
          @php
            $today  = Carbon::today();
            $expiry = $u->expiry_date
                      ? Carbon::parse($u->expiry_date)
                      : null;

            if (!$expiry) {
                $label = '—';
                $badge = 'bg-secondary';
            } else {
                if ($expiry->isPast()) {
                    // scaduto
                    $diff      = $expiry->diffInDays($today);
                    $label     = "Scaduto {$diff} gg fa";
                    $badge     = 'bg-danger blink';
                } else {
                    $diff      = $today->diffInDays($expiry);
                    $label     = "Tra {$diff} gg";
                    if ($diff <= 7) {
                        // entro una settimana
                        $badge = 'bg-warning';
                    } else {
                        // oltre una settimana
                        $badge = 'bg-success';
                    }
                }
            }
          @endphp
          <tr>
            <td>{{ $u->name }}</td>
            <td>{{ $u->email }}</td>
            <td class="text-center">
              @forelse($u->roles as $r)
                <span class="badge bg-secondary">{{ ucfirst($r->name) }}</span>
              @empty
                <em class="text-muted">—</em>
              @endforelse
            </td>
            <td class="text-center">
              <span class="badge {{ $badge }}">
                {{ $label }}
              </span>
            </td>
            <td class="text-center">
              <span class="badge {{ $u->status ? 'bg-success' : 'bg-danger' }}">
                {{ $u->status ? 'Attivo' : 'Inattivo' }}
              </span>
            </td>
            <td class="text-end">
              <a href="{{ route('users.show', $u) }}" class="btn btn-deepblue btn-sm me-1" title="Visualizza Utente">
                <i class="bi bi-eye"></i> Visualizza
              </a>
              <a href="{{ route('users.edit', $u) }}" class="btn btn-gold btn-sm me-1" title="Modifica Utente">
                <i class="bi bi-pencil"></i> Modifica
              </a>
              @if(auth()->user()->hasRole('super') && auth()->id() !== $u->id)
                <form action="{{ route('users.toggleStatus', $u->id) }}" method="POST" class="d-inline me-1">
                  @csrf @method('PATCH')
                  <button type="submit"
                          class="btn btn-sm {{ $u->status ? 'btn-red' : 'btn-deepblue' }}">
                    {{ $u->status ? 'Disattiva' : 'Attiva' }}
                  </button>
                </form>
                <form action="{{ route('users.destroy', $u) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Eliminare questo utente?');">
                  @csrf @method('DELETE')
                  <button class="btn btn-red btn-sm">
                    <i class="bi bi-trash"></i> Elimina
                  </button>
                </form>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{-- Paginazione --}}
  <div class="mt-4">
    {{ $users->links() }}
  </div>
</div>

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

  .btn-gold-blue {
    background-color: #e2ae76 !important;
    color: #041930 !important;
    border: 1px solid #e2ae76;
  }
  .btn-gold-blue:hover {
    background-color: #d89d5c !important;
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
    border: 1px solid red !important;
    color: red !important;
    background-color: transparent !important;
  }
  .btn-red:hover {
    background-color: red !important;
    color: white !important;
  }

  /* Blinking animation for expired badges */
  @keyframes blink {
    0%, 49%   { opacity: 1; }
    50%, 100% { opacity: 0; }
  }
  .blink {
    animation: blink 1s steps(1, end) infinite;
  }
</style>
@endsection
