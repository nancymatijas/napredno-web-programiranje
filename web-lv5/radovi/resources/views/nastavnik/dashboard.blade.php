@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ __('papers.title') }}</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Forma za dodavanje rada -->
    <div class="card mb-4">
        <div class="card-header">{{ __('Dodaj novi rad') }}</div>
        <div class="card-body">
            <form action="{{ route('nastavnik.store-task', ['locale' => app()->getLocale()]) }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="naziv_rada_engleski" class="form-label">{{ __('papers.title_en') }}</label>
                    <input type="text" name="naziv_rada_engleski" id="naziv_rada_engleski" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="naziv_rada" class="form-label">{{ __('papers.title_hr') }}</label>
                    <input type="text" name="naziv_rada" id="naziv_rada" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="zadatak_rada" class="form-label">{{ __('papers.task') }}</label>
                    <textarea name="zadatak_rada" id="zadatak_rada" rows="4" class="form-control" required></textarea>
                </div>

                <div class="mb-3">
                    <label for="tip_studija" class="form-label">{{ __('papers.study_type') }}</label>
                    <select name="tip_studija" id="tip_studija" class="form-select" required>
                        <option value="" disabled selected>{{ __('Odaberite tip studija') }}</option>
                        <option value="stručni">{{ __('Stručni') }}</option>
                        <option value="preddiplomski">{{ __('Preddiplomski') }}</option>
                        <option value="diplomski">{{ __('Diplomski') }}</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
            </form>
        </div>
    </div>

    <!-- Lista radova -->
    @foreach($tasks as $task)
        @php
            $acceptedStudent = $task->students->firstWhere('pivot.status', 'accepted');
        @endphp

        <div class="card mb-4">
            <div class="card-header">
                {{ __('Rad: ') }} {{ $task->naziv_rada }}
                @if($acceptedStudent)
                    <span class="badge bg-success float-end">
                        {{ __('Prihvaćen student: ') }} {{ $acceptedStudent->name }}
                    </span>
                @endif
            </div>

            <div class="card-body">
                @if($task->students->isEmpty())
                    <p>{{ __('Nema prijavljenih studenata.') }}</p>
                @else
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Ime studenta</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Akcija</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($task->students as $student)
                                <tr>
                                    <td>{{ $student->name }}</td>
                                    <td>{{ $student->email }}</td>
                                    <td>{{ ucfirst($student->pivot->status) }}</td>
                                    <td>
                                        @if(!$acceptedStudent && $student->pivot->status === 'pending')
                                        <form action="{{ route('nastavnik.accept', [
                                            'locale' => app()->getLocale(),
                                            'task' => $task->id,  // Eksplicitno prosljeđivanje ID-a
                                            'student' => $student->id
                                        ]) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">Prihvati</button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    @endforeach
</div>
@endsection
