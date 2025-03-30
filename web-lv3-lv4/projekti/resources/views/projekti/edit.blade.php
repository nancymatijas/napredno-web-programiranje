@extends('layouts.app')

@section('content')
<div class="container">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <h1>Uredi Projekt</h1>
    <br>
    <form action="{{ route('projekti.update', $projekt) }}" method="POST">
        @csrf
        @method('PUT')

        @if(auth()->id() === $projekt->voditelj_id)
            <div class="form-group">
                <h5><label for="naziv_projekta">Naziv Projekta</label></h5>
                <input type="text" name="naziv_projekta" id="naziv_projekta" 
                    value="{{ $projekt->naziv_projekta }}" class="form-control">
            </div>
            <br>

            <div class="form-group">
                <h5><label for="opis_projekta">Opis Projekta</label></h5>
                <textarea name="opis_projekta" id="opis_projekta" 
                    class="form-control">{{ $projekt->opis_projekta }}</textarea>
            </div>
            <br>

            <div class="form-group">
                <h5><label for="cijena_projekta">Cijena Projekta</label></h5>
                <input type="number" name="cijena_projekta" id="cijena_projekta" 
                    value="{{ $projekt->cijena_projekta }}" class="form-control" step="0.01">
            </div>
            <br>

            <div class="form-group">
                <h5><label for="datum_pocetka">Datum Početka</label></h5>
                <input type="date" name="datum_pocetka" id="datum_pocetka" 
                    value="{{ $projekt->datum_pocetka }}" class="form-control">
            </div>
            <br>

            <div class="form-group">
                <h5><label for="datum_zavrsetka">Datum Završetka</label></h5>
                <input type="date" name="datum_zavrsetka" id="datum_zavrsetka" 
                    value="{{ $projekt->datum_zavrsetka }}" class="form-control">
            </div>
            <br>

            <div class="form-group">
                <h5><label>Članovi Tima</label></h5>
                <div class="form-check">
                    @foreach($users as $user)
                        <div>
                            <input type="checkbox" name="members[]" 
                                id="member_{{ $user->id }}" 
                                value="{{ $user->id }}"
                                class="form-check-input"
                                {{ $projekt->members->contains($user->id) ? 'checked' : '' }}>
                            <label for="member_{{ $user->id }}" 
                                class="form-check-label">{{ $user->name }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
            <br>
        @endif

        @if(auth()->id() === $projekt->voditelj_id || $projekt->members->contains(auth()->id()))
            <div class="form-group">
                <h5><label for="obavljeni_poslovi">Obavljeni Poslovi</label></h5>
                <textarea name="obavljeni_poslovi" id="obavljeni_poslovi" 
                    class="form-control">{{ $projekt->obavljeni_poslovi }}</textarea>
            </div>
            <br>
        @endif

        <button type="submit" class="btn btn-primary">Spremi Promjene</button>
    </form>
</div>
@endsection
