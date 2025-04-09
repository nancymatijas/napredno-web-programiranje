<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function index()
    {
        if (Auth::user()->role !== 'student') {
            abort(403, 'Nemate ovlasti za pristup ovoj stranici.');
        }
    
        $studentId = Auth::id();
    
        $tasks = Task::with(['students' => function ($query) {
            $query->withPivot('status');
        }])->get();
    
        $isAccepted = Task::whereHas('students', function ($query) use ($studentId) {
            $query->where('student_id', $studentId)
                  ->where('status', 'accepted');
        })->exists();
    
        return view('student.dashboard', compact('tasks', 'studentId', 'isAccepted'));
    }

    public function apply(Request $request, Task $task)
    {
        /** @var \App\Models\User $student */
        $student = Auth::user();

        if ($student->tasks()->count() >= 5) {
            return back()->with('error', 'Možete prijaviti maksimalno 5 radova.');
        }

        $usedPriorities = $student->tasks()->pluck('priority')->toArray();
        $desiredPriority = $request->priority;
        $assignedPriority = $desiredPriority;

        // Ako je željeni prioritet zauzet, traži sljedeći slobodan
        if (in_array($desiredPriority, $usedPriorities)) {
            for ($p = 1; $p <= 5; $p++) {
                if (!in_array($p, $usedPriorities)) {
                    $assignedPriority = $p;
                    break;
                }
            }
        }

        if ($task->students()->wherePivot('status', 'accepted')->exists()) {
            return back()->with('error', 'Ovaj rad je već zauzet.');
        }

        $student->tasks()->attach($task->id, [
            'status' => 'pending',
            'priority' => $assignedPriority
        ]);

        return back()->with('success', 'Uspješno ste se prijavili na rad s prioritetom ' . $assignedPriority);
        }
    

    public function unapply(Request $request, Task $task)
    {
        if (!$task->students()->where('student_id', Auth::id())->exists()) {
            return back()->with('error', 'Niste prijavljeni na ovaj rad.');
        }

        $task->students()->detach(Auth::id());

        return back()->with('success', 'Uspješno ste se odjavili s rada.');
    }

}
