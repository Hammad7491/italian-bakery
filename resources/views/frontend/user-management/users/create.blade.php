{{-- resources/views/frontend/user-management/users/create.blade.php --}}
@extends('frontend.layouts.app')

@section('title', $isEdit ? 'Modifica Utente' : 'Aggiungi Utente')

@section('content')
<div class="container py-5 px-md-4">

  <!-- Intestazione -->
  <div class="page-header d-flex align-items-center mb-4"
       style="background-color: #041930; border-radius: 0.75rem; padding: 1rem 2rem;">
    <i class="bi bi-person-fill-gear me-2 fs-3" style="color: #e2ae76;"></i>
    <h4 class="mb-0 fw-bold" style="color: #e2ae76;">
      {{ $isEdit ? 'Modifica Utente' : 'Aggiungi Utente' }}
    </h4>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <form action="{{ $isEdit
                        ? route('users.update', $user)
                        : route('users.store') }}"
            method="POST">
        @csrf
        @if($isEdit) @method('PUT') @endif

        {{-- Nome --}}
        <div class="mb-3">
          <label class="form-label">Nome</label>
          <input type="text"
                 name="name"
                 class="form-control"
                 value="{{ old('name', $user->name) }}"
                 required>
        </div>

        {{-- Email --}}
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email"
                 name="email"
                 class="form-control"
                 value="{{ old('email', $user->email) }}"
                 required>
        </div>

        {{-- Password solo in creazione --}}
        @unless($isEdit)
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password"
                   name="password"
                   class="form-control"
                   required>
          </div>
        @endunless

        {{-- Ruolo --}}
@php
  $currentRole = $isEdit
    ? optional($user->roles->first())->name
    : null;
@endphp

<div class="mb-4">
  <label for="role" class="form-label">Ruolo</label>

  @if($isEdit && auth()->id() === $user->id)
    {{-- Autoprofilo: mostra badge del ruolo --}}
    <div>
      <span class="badge bg-primary">{{ ucfirst($currentRole) }}</span>
    </div>
    <input type="hidden" name="role" value="{{ optional($user->roles->first())->id }}">
  @else
    {{-- Dropdown --}}
    <select id="role"
            name="role"
            class="form-select"
            required>
      @foreach($roles as $role)
        @if($role->name === 'super')
          @continue
        @endif
        @if($isEdit && $currentRole === 'admin' && $role->name === 'admin')
          @continue
        @endif
        <option value="{{ $role->id }}"
                {{ (string) old('role', optional($user->roles->first())->id) === (string)$role->id
                    ? 'selected'
                    : '' }}>
          {{ ucfirst($role->name) }}
        </option>
      @endforeach
    </select>
  @endif
</div>

        <button type="submit"
                class="btn btn-gold-blue px-4 py-2 fw-semibold">
          <i class="bi bi-check-circle me-1"></i>
          {{ $isEdit ? 'Aggiorna Utente' : 'Aggiungi Utente' }}
        </button>
      </form>
    </div>
  </div>
</div>

{{-- Stili inline --}}
<style>
  .btn-gold-blue {
    background-color: #e2ae76 !important;
    color: #041930 !important;
    border: 1px solid #e2ae76;
  }
  .btn-gold-blue:hover {
    background-color: #d89d5c !important;
    color: #fff !important;
  }
</style>
@endsection
