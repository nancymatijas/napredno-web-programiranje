@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Projekti</h1>

    <h2>Projekti gdje ste voditelj</h2>
    @if($projektiVoditelj->isEmpty())
        <div class="alert alert-info">Niste voditelj niti jednog projekta</div>
    @else
        <div class="list-group mb-4">
            @foreach($projektiVoditelj as $projekt)
                <a href="{{ route('projekti.show', $projekt) }}" class="list-group-item list-group-item-action">
                    {{ $projekt->naziv_projekta }}
                    <span class="badge bg-primary float-end">Voditelj</span>
                </a>
            @endforeach
        </div>
    @endif

    <h2>Projekti gdje ste član tima</h2>
    @if($projektiClan->isEmpty())
        <div class="alert alert-info">Niste član niti jednog projekta</div>
    @else
        <div class="list-group">
            @foreach($projektiClan as $projekt)
                <a href="{{ route('projekti.show', $projekt) }}" class="list-group-item list-group-item-action">
                    {{ $projekt->naziv_projekta }}
                    <span class="badge bg-success float-end">Član tima</span>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
