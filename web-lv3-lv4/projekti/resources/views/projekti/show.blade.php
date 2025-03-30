@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detalji Projekta</h1>

    <div class="card mb-4">
        <div class="card-header">
            <h2>{{ $projekt->naziv_projekta }}</h2>
        </div>
        <div class="card-body">
            <p><strong>Opis:</strong> {{ $projekt->opis_projekta }}</p>
            <p><strong>Cijena:</strong> {{ $projekt->cijena_projekta }} kn</p>
            <p><strong>Datum Početka:</strong> {{ $projekt->datum_pocetka }}</p>
            <p><strong>Datum Završetka:</strong> {{ $projekt->datum_zavrsetka ?? 'Nije definirano' }}</p>
            <p><strong>Obavljeni Poslovi:</strong> {{ $projekt->obavljeni_poslovi ?? 'Nema unosa' }}</p>
        </div>
    </div>

    <div class="mb-4">
        <h5>Voditelj Projekta</h5>
        <p>{{ $projekt->voditelj->name }}</p>
    </div>

    <div class="mb-4">
        <h5>Članovi Projektnog Tima</h5>
        @if($projekt->members->isEmpty())
            <p>Nema članova tima.</p>
        @else
            <ul>
                @foreach($projekt->members as $member)
                    <li>{{ $member->name }}</li>
                @endforeach
            </ul>
        @endif
    </div>

    @if(auth()->id() === $projekt->voditelj_id)
        <a href="{{ route('projekti.edit', $projekt) }}" class="btn btn-primary">Uredi Projekt</a>
    @endif

    @if($projekt->members->contains(auth()->user()))
        <form action="{{ route('projekti.update', $projekt) }}" method="POST">
        @csrf
        @method('PATCH') 
        <div class="form-group">
            <h5><label for="obavljeni_poslovi">Ažuriraj Obavljene Poslove</label></h5>
            <textarea name="obavljeni_poslovi" id="obavljeni_poslovi" class="form-control">{{ $projekt->obavljeni_poslovi }}</textarea>
        </div>

        <br>
        <button type="submit" class="btn btn-success">Spremi</button>
    </form>

    @endif
</div>
@endsection
