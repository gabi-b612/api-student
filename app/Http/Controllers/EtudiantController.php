<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class EtudiantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() : Collection
    {
        return Etudiant::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'post-nom' => 'required|string|max:255',
            'pre-nom' => 'required|string|max:255',
            'email' => 'required|email|unique:etudiants,email',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $photoPath = $request->file('photo')->store('photos','public');

        Etudiant::create([
            'nom' => $validated['nom'],
            'post-nom' => $validated['post-nom'],
            'pre-nom' => $validated['pre-nom'],
            'email' => $validated['email'],
            'photo' => $photoPath
        ]);
        return response()->json(['message' => 'Etudiant créé avec succès'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Etudiant::findOrFail($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $etudiant = Etudiant::findOrFail($id);
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'post-nom' => 'required|string|max:255',
            'pre-nom' => 'required|string|max:255',
            'email' => 'required|email|unique:etudiants,email',
            'photo' => 'nullable|image|max:2048'
        ]);

        $etudiant->update($validated);
        if ($request->hasFile('photo')) {
            if ($etudiant->photo) {
                Storage::disk('public')->delete($etudiant->photo);
            }
            $etudiant->photo = $request->file('photo')->store('photo', 'public');
            $etudiant->save();
        }

        return $etudiant;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): Response
    {
        $etudiant = Etudiant::findOrFail($id);

        if ($etudiant->photo) {
            Storage::disk('public')->delete($etudiant->photo);
        }

        $etudiant->delete();

        return response()->noContent();
    }
}
