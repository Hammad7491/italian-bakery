<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class IngredientController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (is_null($user->created_by)) {
            $visibleUserIds = User::where('created_by', $user->id)
                                  ->pluck('id')
                                  ->push($user->id)
                                  ->unique();
        } else {
            $visibleUserIds = collect([$user->id, $user->created_by])->unique();
        }

        $ingredients = Ingredient::with('user')
                          ->whereIn('user_id', $visibleUserIds)
                          ->get();

        return view('frontend.ingredients.index', compact('ingredients'));
    }

    public function create()
    {
        return view('frontend.ingredients.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'ingredient_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('ingredients')
                    ->where(fn($q) => $q->where('user_id', Auth::id())),
            ],
            'price_per_kg' => 'required|numeric|min:0',
        ]);

        $data['user_id'] = Auth::id();

        $ingredient = Ingredient::create($data);

        if ($request->expectsJson()) {
            return response()->json($ingredient, 201);
        }

        return back()->with('success', 'Ingrediente salvato con successo.');
    }

    public function show(Ingredient $ingredient)
    {
        abort_unless($ingredient->user_id === Auth::id(), 403);
        return view('frontend.ingredients.show', compact('ingredient'));
    }

    public function edit(Ingredient $ingredient)
    {
        abort_unless($ingredient->user_id === Auth::id(), 403);
        return view('frontend.ingredients.create', compact('ingredient'));
    }

    public function update(Request $request, Ingredient $ingredient)
    {
        abort_unless($ingredient->user_id === Auth::id(), 403);

        $data = $request->validate([
            'ingredient_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('ingredients')
                    ->where(fn($q) => $q->where('user_id', Auth::id()))
                    ->ignore($ingredient->id),
            ],
            'price_per_kg' => 'required|numeric|min:0',
        ]);

        $ingredient->update($data);

        return redirect()
            ->route('ingredients.index')
            ->with('success', 'Ingrediente aggiornato con successo!');
    }

    public function destroy(Ingredient $ingredient)
    {
        abort_unless($ingredient->user_id === Auth::id(), 403);

        $ingredient->recipes()->detach();
        $ingredient->delete();

        return redirect()
            ->route('ingredients.index')
            ->with('success', 'Ingrediente eliminato con successo!');
    }
}
