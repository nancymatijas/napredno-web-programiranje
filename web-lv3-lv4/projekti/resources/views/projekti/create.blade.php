@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Kreiraj Novi Projekt</h1>
    <form action="{{ route('projekti.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <h5><label for="naziv_projekta">Naziv Projekta</label></h5>
            <input type="text" name="naziv_projekta" id="naziv_projekta" class="form-control" required>
        </div>
        <br>

        <div class="form-group">
            <h5><label for="opis_projekta">Opis Projekta</label></h5>
            <textarea name="opis_projekta" id="opis_projekta" class="form-control" required></textarea>
        </div>
        <br>

        <div class="form-group">
            <h5><label for="cijena_projekta">Cijena Projekta</label></h5>
            <input type="number" name="cijena_projekta" id="cijena_projekta" class="form-control" step="0.01" required>
        </div>
        <br>

        <div class="form-group">
            <h5><label for="obavljeni_poslovi">Obavljeni Poslovi</label></h5>
            <textarea name="obavljeni_poslovi" id="obavljeni_poslovi" class="form-control"></textarea>
        </div>
        <br>

        <div class="form-group">
            <h5><label for="datum_pocetka">Datum Početka</label></h5>
            <input type="date" name="datum_pocetka" id="datum_pocetka" class="form-control" required>
        </div>
        <br>

        <div class="form-group">
            <h5><label for="datum_zavrsetka">Datum Završetka</label></h5>
            <input type="date" name="datum_zavrsetka" id="datum_zavrsetka" class="form-control">
        </div>
        <br>

        <div class="form-group">
            <h5><label for="members">Članovi Projektnog Tima</label></h5>
            <div class="form-check">
                @foreach($users as $user)
                    <div>
                        <input type="checkbox" name="members[]" id="member_{{ $user->id }}" value="{{ $user->id }}" class="form-check-input">
                        <label for="member_{{ $user->id }}" class="form-check-label">{{ $user->name }}</label>
                    </div>
                @endforeach
            </div>
        </div>
        <br>

        <button type="submit" class="btn btn-primary">Spremi</button>
    </form>
</div>
@endsection
