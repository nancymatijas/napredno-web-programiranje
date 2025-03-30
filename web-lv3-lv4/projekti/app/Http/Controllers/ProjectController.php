<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
     public function index()
    {
        $user = Auth::user();
        
        return view('projekti.index', [
            'sviProjekti' => Project::with(['voditelj', 'members'])->get(),
            'projektiVoditelj' => $user->projektiVoditelj,
            'projektiClan' => $user->projektiClan
        ]);
    }

    public function create()
    {
        $users = User::where('id', '!=', Auth::id())->get();
        return view('projekti.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'naziv_projekta' => 'required|string|max:255',
            'opis_projekta' => 'required|string',
            'cijena_projekta' => 'required|numeric|min:0',
            'obavljeni_poslovi' => 'nullable|string',
            'datum_pocetka' => 'required|date',
            'datum_zavrsetka' => 'nullable|date|after_or_equal:datum_pocetka',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,id',
        ]);

        $project = Project::create($validated + ['voditelj_id' => Auth::id()]);
        $project->members()->sync($request->members ?? []);

        return redirect()->route('projekti.index')->with('success', 'Projekt uspješno kreiran!');
    }

    public function show(Project $projekt)
    {
        $projekt->load('voditelj', 'members');
        return view('projekti.show', compact('projekt'));
    }

    public function edit(Project $projekt)
    {
        $users = User::where('id', '!=', $projekt->voditelj_id)->get();
        return view('projekti.edit', compact('projekt', 'users'));
    }

    public function update(Request $request, Project $projekt)
    {
        if (Auth::id() === $projekt->voditelj_id) {
            $validated = $request->validate([
                'naziv_projekta' => 'required|string|max:255',
                'opis_projekta' => 'required|string',
                'cijena_projekta' => 'required|numeric|min:0',
                'datum_pocetka' => 'required|date',
                'datum_zavrsetka' => 'nullable|date|after_or_equal:datum_pocetka',
                'members' => 'nullable|array',
                'members.*' => 'exists:users,id',
                'obavljeni_poslovi' => 'nullable|string',
            ]);
    
            $projekt->update($validated);
            $projekt->members()->sync($request->members ?? []);
    
            $poruka = 'Projekt ažuriran!';
    
        } elseif ($projekt->members->contains(Auth::id())) {
            $validated = $request->validate([
                'obavljeni_poslovi' => 'nullable|string',
            ]);
    
            $projekt->update($validated);
            $poruka = 'Obavljeni poslovi spremljeni!';
    
        } else {
            abort(403, 'Nemate pravo uređivati ovaj projekt.');
        }
    
        return redirect()->route('projekti.show', $projekt)->with('success', $poruka);
    }

    public function destroy(string $id)
    {
        // 
    }

}
