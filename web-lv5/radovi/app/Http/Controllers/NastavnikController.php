<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class NastavnikController extends Controller
{
    public function index($locale)
    {
        if (Auth::user()->role !== 'nastavnik') {
            abort(403, 'Nemate ovlasti za pristup ovoj stranici.');
        }

        if (in_array($locale, ['en', 'hr'])) {
            app()->setLocale($locale);
        } else {
            abort(404, 'Language not supported');
        }

        $tasks = Task::where('nastavnik_id', Auth::id())->get();

        return view('nastavnik.dashboard', compact('tasks'));
    }

    public function store(Request $request, $locale)
    {
        if (in_array($locale, ['en', 'hr'])) {
            app()->setLocale($locale);
        } else {
            abort(404, 'Language not supported');
        }

        $request->validate([
            'naziv_rada_engleski' => 'required|string|max:255',
            'naziv_rada' => 'required|string|max:255',
            'zadatak_rada' => 'required|string',
            'tip_studija' => 'required|in:stručni,preddiplomski,diplomski',
        ]);

        Task::create([
            'naziv_rada_engleski' => $request->naziv_rada_engleski,
            'naziv_rada' => $request->naziv_rada,
            'zadatak_rada' => $request->zadatak_rada,
            'tip_studija' => $request->tip_studija,
            'nastavnik_id' => Auth::id(),
        ]);

        return redirect()->route('nastavnik.dashboard', ['locale' => $locale])->with('success', __('Rad uspješno dodan!'));
    }


    public function acceptStudent(Request $request, $locale, Task $task, User $student)
    {
        // Provjera ovlasti
        if (Auth::user()->role !== 'nastavnik' || $task->nastavnik_id !== Auth::id()) {
            abort(403, 'Nemate ovlasti za ovu akciju.');
        }
    
        // Provjera prioriteta
        $pivotData = $task->students()->find($student->id)->pivot;
        if ($pivotData->priority !== 1) {
            return back()->with('error', 'Student mora imati ovaj rad označen kao prioritet 1.');
        }
    
        // Ažuriraj status
        $task->students()->updateExistingPivot($student->id, ['status' => 'accepted']);
    
        // Odbaci studenta sa svih ostalih radova
        $student->tasks()
            ->where('task_id', '!=', $task->id)
            ->update(['student_task.status' => 'rejected']);
    
        return back()->with('success', 'Student uspješno prihvaćen.');
    }
    
}
