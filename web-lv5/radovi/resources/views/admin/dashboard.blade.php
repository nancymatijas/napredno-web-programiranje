@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Admin Dashboard</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Statistike -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Administratori</h5>
                    <p class="display-4">{{ $userCounts['admin'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Nastavnici</h5>
                    <p class="display-4">{{ $userCounts['nastavnik'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Studenti</h5>
                    <p class="display-4">{{ $userCounts['student'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista korisnika -->
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0">Zadnje registrirani korisnici</h4>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Ime</th>
                            <th>Email</th>
                            <th>Uloga</th>
                            <th>Registriran</th>
                            <th>Akcije</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($latestUsers as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge badge-pill 
                                        @if($user->role === 'admin') badge-danger
                                        @elseif($user->role === 'nastavnik') badge-success
                                        @else badge-info @endif">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td>{{ $user->created_at->format('d.m.Y. H:i') }}</td>
                                <td>
                                    @if($user->role !== 'admin')
                                    <form action="{{ route('admin.users.update-role', $user) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <select name="role" class="form-select" onchange="this.form.submit()">
                                            <option value="student" {{ $user->role === 'student' ? 'selected' : '' }}>Student</option>
                                            <option value="nastavnik" {{ $user->role === 'nastavnik' ? 'selected' : '' }}>Nastavnik</option>
                                        </select>
                                    </form>

                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Nema registriranih korisnika</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
