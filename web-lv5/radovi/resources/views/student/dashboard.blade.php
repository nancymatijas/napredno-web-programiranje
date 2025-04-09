@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Popis dostupnih radova</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if($tasks->isEmpty())
        <p>Nema dostupnih radova.</p>
    @else
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Naziv rada</th>
                    <th>Naziv rada (engleski)</th>
                    <th>Zadatak rada</th>
                    <th>Tip studija</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tasks as $task)
                    @php
                        $acceptedStudent = $task->students->firstWhere('pivot.status', 'accepted');
                        $currentStudentApplied = $task->students->contains($studentId);
                    @endphp
                    
                    <tr>
                        <td>{{ $task->naziv_rada }}</td>
                        <td>{{ $task->naziv_rada_engleski }}</td>
                        <td>{{ $task->zadatak_rada }}</td>
                        <td>{{ ucfirst($task->tip_studija) }}</td>
                        <td>
                            @if($acceptedStudent)
                                @if($acceptedStudent->id === $studentId)
                                    <!-- Prikaz za prihvaćenog studenta -->
                                    <span class="text-success">Vi ste prihvaćeni na ovaj rad!</span>
                                @else
                                    <!-- Prikaz za studente koji nisu prihvaćeni -->
                                    <span class="text-muted">Rad je dodijeljen drugom studentu.</span>
                                @endif
                            @else
                                <!-- Prikaz opcija prijave i odjave ako rad nije dodijeljen -->
                                @if(!$isAccepted)
                                    @if($currentStudentApplied)
                                        <form action="{{ route('student.unapply', $task->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Odjavi rad</button>
                                        </form>
                                    @else
                                    <form action="{{ route('student.apply', $task->id) }}" method="POST">
                                        @csrf
                                        <select name="priority" class="form-select" required>
                                            <option value="" disabled selected>Odaberite prioritet (1-5)</option>
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if(!Auth::user()->tasks->contains('pivot.priority', $i))
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                @endif
                                            @endfor
                                        </select>
                                        <button type="submit" class="btn btn-primary mt-2">Prijavi se</button>
                                    </form>
                                    @endif
                                @else
                                    <!-- Ako je student već prijavljen na drugi rad -->
                                    <span class="text-info">Već ste prijavljeni na drugi rad.</span>
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
